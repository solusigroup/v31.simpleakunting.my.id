<?php

require_once 'Jurnal_model.php';

class TutupBuku_model 
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getLatestClosedPeriod() {
        $this->db->query("SELECT * FROM periode_akuntansi WHERE status = 'Closed' AND tipe_proses = 'Bulanan' ORDER BY tahun DESC, bulan DESC LIMIT 1");
        return $this->db->single();
    }
    
    public function getClosingJournalPreview($periode, $isTahunan = false) {
        $jurnalModel = new Jurnal_model($this->db);
        
        if ($isTahunan) {
            $tanggal_mulai = $periode . '-01-01';
            $tanggal_selesai = $periode . '-12-31';
        } else { // Bulanan
            $tanggal_mulai = $periode . '-01';
            $tanggal_selesai = date('Y-m-t', strtotime($tanggal_mulai));
        }
        
        return $jurnalModel->getLabaRugi($tanggal_mulai, $tanggal_selesai);
    }

    /**
     * FUNGSI YANG DIPERBARUI dengan logika pembuatan jurnal penutup yang lengkap.
     */
    public function prosesTutupBuku($periode, $tipe_proses) {
        $jurnalModel = new Jurnal_model($this->db);
        $isTahunan = ($tipe_proses == 'Tahunan');

        if ($isTahunan) {
            $tanggal_mulai = $periode . '-01-01';
            $tanggal_selesai = $periode . '-12-31';
        } else { // Bulanan
            $tanggal_mulai = $periode . '-01';
            $tanggal_selesai = date('Y-m-t', strtotime($tanggal_mulai));
        }

        $labaRugiData = $this->getClosingJournalPreview($periode, $isTahunan);
        $labaBersih = ($labaRugiData['total_pendapatan_1'] ?? 0) - ($labaRugiData['total_beban_1'] ?? 0);
        $totalPendapatan = $labaRugiData['total_pendapatan_1'] ?? 0;
        $totalBeban = $labaRugiData['total_beban_1'] ?? 0;

        $akun_ikhtisar_lr = '3-9999';
        $akun_laba_ditahan = '3-2000'; // Pastikan sesuai dengan Daftar Akun Anda

        $this->db->beginTransaction();
        try {
            $jurnalData = [
                'no_transaksi' => 'CL-' . ($isTahunan ? $periode : date('Ym', strtotime($tanggal_mulai))),
                'tanggal' => $tanggal_selesai,
                'deskripsi' => 'Jurnal Penutup ' . $tipe_proses . ' Periode ' . ($isTahunan ? 'Tahun '.$periode : date('F Y', strtotime($tanggal_mulai))),
                'sumber_jurnal' => 'Adjustment',
                'details' => []
            ];

            // 1. Tutup semua akun Pendapatan ke Ikhtisar L/R
            if (!empty($labaRugiData['pendapatan'])) {
                foreach($labaRugiData['pendapatan'] as $akun) {
                    // Debit akun Pendapatan untuk me-nol-kan saldo kreditnya
                    $jurnalData['details'][] = ['kode_akun' => $akun['kode_akun'], 'debit' => $akun['total_1'], 'kredit' => 0];
                }
                // Kredit Ikhtisar L/R sebesar total Pendapatan
                $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => 0, 'kredit' => $totalPendapatan];
            }

            // 2. Tutup semua akun Beban ke Ikhtisar L/R
             if (!empty($labaRugiData['beban'])) {
                // Debit Ikhtisar L/R sebesar total Beban
                $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => $totalBeban, 'kredit' => 0];
                foreach($labaRugiData['beban'] as $akun) {
                    // Kredit akun Beban untuk me-nol-kan saldo debitnya
                    $jurnalData['details'][] = ['kode_akun' => $akun['kode_akun'], 'debit' => 0, 'kredit' => $akun['total_1']];
                }
            }
            
            // 3. Tutup Ikhtisar L/R ke Laba Ditahan
            if ($labaBersih >= 0) { // Jika Laba
                $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => $labaBersih, 'kredit' => 0];
                $jurnalData['details'][] = ['kode_akun' => $akun_laba_ditahan, 'debit' => 0, 'kredit' => $labaBersih];
            } else { // Jika Rugi
                $jurnalData['details'][] = ['kode_akun' => $akun_laba_ditahan, 'debit' => abs($labaBersih), 'kredit' => 0];
                $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => 0, 'kredit' => abs($labaBersih)];
            }
            
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal penutup.");
            
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();

            // 4. Tandai periode sebagai 'Closed'
            if ($isTahunan) {
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $this->db->query("INSERT INTO periode_akuntansi (tahun, bulan, status, tipe_proses, tanggal_tutup) VALUES (:tahun, :bulan, 'Closed', :tipe, NOW()) ON DUPLICATE KEY UPDATE status = 'Closed', tipe_proses = :tipe, tanggal_tutup = NOW()");
                    $this->db->bind('tahun', $periode);
                    $this->db->bind('bulan', $bulan);
                    $this->db->bind('tipe', $tipe_proses);
                    $this->db->execute();
                }
            } else {
                $tahun = date('Y', strtotime($tanggal_mulai));
                $bulan = date('m', strtotime($tanggal_mulai));
                $this->db->query("INSERT INTO periode_akuntansi (tahun, bulan, status, tipe_proses, tanggal_tutup) VALUES (:tahun, :bulan, 'Closed', :tipe, NOW()) ON DUPLICATE KEY UPDATE status = 'Closed', tipe_proses = :tipe, tanggal_tutup = NOW()");
                $this->db->bind('tahun', $tahun);
                $this->db->bind('bulan', $bulan);
                $this->db->bind('tipe', $tipe_proses);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }
    
    public function isPeriodClosed($tanggal) {
        $tahun = date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));

        $this->db->query("SELECT status FROM periode_akuntansi WHERE tahun = :tahun AND bulan = :bulan");
        $this->db->bind('tahun', $tahun);
        $this->db->bind('bulan', $bulan);
        
        $result = $this->db->single();
        
        return ($result && $result['status'] === 'Closed');
    }
}

