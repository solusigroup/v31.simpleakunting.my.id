<?php

// Muat model lain yang dibutuhkan secara manual
require_once 'Jurnal_model.php';

class Penerimaan_model {
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Mengambil semua data header penerimaan pembayaran.
     */
    public function getAllPenerimaan() {
        $this->db->query("SELECT pp.*, pl.nama_pelanggan 
                         FROM penerimaan_pelanggan pp
                         JOIN pelanggan pl ON pp.id_pelanggan = pl.id_pelanggan
                         ORDER BY pp.tanggal DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Mengambil data satu bukti penerimaan lengkap dengan detailnya.
     */
    public function getPenerimaanByIdWithDetails($id)
    {
        // 1. Ambil data header penerimaan
        $this->db->query("SELECT pp.*, pl.nama_pelanggan, pl.alamat as alamat_pelanggan, ak.nama_akun as nama_akun_kas
                         FROM penerimaan_pelanggan pp
                         JOIN pelanggan pl ON pp.id_pelanggan = pl.id_pelanggan
                         JOIN akun ak ON pp.akun_kas_bank = ak.kode_akun
                         WHERE pp.id_penerimaan = :id");
        $this->db->bind('id', $id);
        $header = $this->db->single();

        if (!$header) {
            return false;
        }

        // 2. Ambil data detail (faktur yang dibayar)
        $this->db->query("SELECT ppd.*, p.no_faktur, p.tanggal_faktur
                         FROM penerimaan_pelanggan_detail ppd
                         LEFT JOIN penjualan p ON ppd.id_penjualan = p.id_penjualan
                         WHERE ppd.id_penerimaan = :id");
        $this->db->bind('id', $id);
        $details = $this->db->resultSet();
        
        // Menangani pembayaran Saldo Awal yang tidak memiliki detail faktur
        if(empty($details)) {
             $details[] = [
                'no_faktur' => 'SALDO AWAL',
                'tanggal_faktur' => $header['tanggal'],
                'jumlah_bayar' => $header['total_diterima']
             ];
        }

        $header['details'] = $details;
        return $header;
    }
    
    /**
     * Mengambil daftar faktur penjualan yang belum lunas, termasuk sisa Saldo Awal.
     */
    public function getFakturBelumLunasByPelanggan($id_pelanggan) {
        // 1. Ambil semua faktur penjualan nyata yang belum lunas
        $this->db->query("SELECT id_penjualan, no_faktur, tanggal_faktur, sisa_tagihan 
                         FROM penjualan 
                         WHERE id_pelanggan = :id AND status_pembayaran != 'Lunas'
                         ORDER BY tanggal_faktur ASC");
        $this->db->bind('id', $id_pelanggan);
        $fakturNyata = $this->db->resultSet();

        // 2. Ambil saldo terkini pelanggan
        $this->db->query("SELECT saldo_terkini_piutang FROM pelanggan WHERE id_pelanggan = :id");
        $this->db->bind('id', $id_pelanggan);
        $pelanggan = $this->db->single();
        $saldoTerkini = (float)($pelanggan['saldo_terkini_piutang'] ?? 0);

        // 3. Hitung total sisa tagihan dari faktur nyata
        $totalSisaFaktur = 0;
        foreach ($fakturNyata as $faktur) {
            $totalSisaFaktur += (float)$faktur['sisa_tagihan'];
        }

        // 4. Sisa Saldo Awal adalah selisih antara saldo terkini dan total sisa faktur
        $sisaSaldoAwal = $saldoTerkini - $totalSisaFaktur;

        // 5. Jika ada sisa Saldo Awal, buat "faktur virtual"
        if ($sisaSaldoAwal > 0.01) { // Toleransi untuk presisi float
            $fakturSaldoAwal = [
                'id_penjualan' => 'SA-' . $id_pelanggan, // ID khusus untuk Saldo Awal
                'no_faktur' => 'SALDO AWAL',
                'tanggal_faktur' => 'N/A',
                'sisa_tagihan' => $sisaSaldoAwal
            ];
            array_unshift($fakturNyata, $fakturSaldoAwal); // Tambahkan ke awal daftar
        }

        return $fakturNyata;
    }

    /**
     * Menyimpan transaksi penerimaan pembayaran secara lengkap dan aman.
     */
    public function simpanPenerimaan($data) {
        // Buat instance model lain dan berikan koneksi DB yang sama
        $jurnalModel = new Jurnal_model($this->db);
        
        $this->db->beginTransaction();
        try {
            $totalDiterima = array_sum($data['details']['jumlah_bayar']);
            if ($totalDiterima <= 0) {
                throw new Exception("Total pembayaran tidak boleh nol.");
            }

            // 1. Ambil Akun Piutang Usaha dari pengaturan
            $this->db->query("SELECT akun_piutang_default FROM perusahaan WHERE id = 1");
            $akun_piutang_usaha = $this->db->single()['akun_piutang_default'];
            if (empty($akun_piutang_usaha)) {
                throw new Exception("Akun Piutang Usaha default belum diatur.");
            }

            // 2. Buat Jurnal: (D) Kas/Bank, (K) Piutang Usaha
            $jurnalData = [
                'no_transaksi' => $data['no_bukti'],
                'tanggal' => $data['tanggal'],
                'deskripsi' => 'Penerimaan dari ' . $data['nama_pelanggan'] . ' (Bukti #' . $data['no_bukti'] . ')',
                'sumber_jurnal' => 'Penjualan',
                'details' => [
                    ['kode_akun' => $data['akun_kas_bank'], 'debit' => $totalDiterima, 'kredit' => 0],
                    ['kode_akun' => $akun_piutang_usaha, 'debit' => 0, 'kredit' => $totalDiterima]
                ]
            ];
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal penerimaan.");

            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();

            // 3. Simpan Header Penerimaan
            $queryHeader = "INSERT INTO penerimaan_pelanggan (id_pelanggan, id_jurnal, no_bukti, tanggal, akun_kas_bank, total_diterima, keterangan) 
                            VALUES (:id_pelanggan, :id_jurnal, :no_bukti, :tanggal, :akun_kas, :total, :ket)";
            $this->db->query($queryHeader);
            $this->db->bind('id_pelanggan', $data['id_pelanggan']);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('no_bukti', $data['no_bukti']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('akun_kas', $data['akun_kas_bank']);
            $this->db->bind('total', $totalDiterima);
            $this->db->bind('ket', $data['keterangan']);
            $this->db->execute();
            $id_penerimaan = $this->db->lastInsertId();

            // 4. Simpan Detail Penerimaan & Update Faktur Penjualan
            $queryDetail = "INSERT INTO penerimaan_pelanggan_detail (id_penerimaan, id_penjualan, jumlah_bayar) VALUES (:id_penerimaan, :id_penjualan, :jumlah)";
            $queryUpdateFaktur = "UPDATE penjualan SET sisa_tagihan = sisa_tagihan - :jumlah, status_pembayaran = IF(sisa_tagihan <= 0.01, 'Lunas', 'Lunas Sebagian') WHERE id_penjualan = :id_penjualan";

            foreach ($data['details']['id_penjualan'] as $index => $id_penjualan) {
                $jumlah_bayar = (float)$data['details']['jumlah_bayar'][$index];
                if ($jumlah_bayar > 0) {
                    // Jika ini BUKAN pembayaran untuk Saldo Awal, catat detail dan update faktur
                    if (strpos($id_penjualan, 'SA-') !== 0) {
                        $this->db->query($queryDetail);
                        $this->db->bind('id_penerimaan', $id_penerimaan);
                        $this->db->bind('id_penjualan', $id_penjualan);
                        $this->db->bind('jumlah', $jumlah_bayar);
                        $this->db->execute();

                        $this->db->query($queryUpdateFaktur);
                        $this->db->bind('jumlah', $jumlah_bayar);
                        $this->db->bind('id_penjualan', $id_penjualan);
                        $this->db->execute();
                    }
                }
            }

            // 5. Update Saldo Terkini Pelanggan
            $this->db->query("UPDATE pelanggan SET saldo_terkini_piutang = saldo_terkini_piutang - :total WHERE id_pelanggan = :id");
            $this->db->bind('total', $totalDiterima);
            $this->db->bind('id', $data['id_pelanggan']);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
        
    }
    public function hapusPenerimaan($data) {
            if ($this->model('Penerimaan')->hapusPenerimaan($id)) {
        Flash::setFlash('Penerimaan berhasil dibatalkan.', 'success');}
     }
}
