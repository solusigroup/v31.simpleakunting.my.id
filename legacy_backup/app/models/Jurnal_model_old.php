<?php

class Jurnal_model {
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllJurnal()
    {
        $query = "SELECT 
                    ju.id_jurnal, ju.no_transaksi, ju.tanggal, ju.deskripsi, ju.sumber_jurnal, ju.is_locked, 
                    COALESCE(SUM(jd.debit), 0) as total,
                    pjl.id_penjualan,
                    pbl.id_pembelian,
                    kt.id_transaksi as id_kas_transaksi
                  FROM jurnal_umum ju
                  LEFT JOIN jurnal_detail jd ON ju.id_jurnal = jd.id_jurnal
                  LEFT JOIN penjualan pjl ON ju.id_jurnal = pjl.id_jurnal
                  LEFT JOIN pembelian pbl ON ju.id_jurnal = pbl.id_jurnal
                  LEFT JOIN kas_transaksi kt ON ju.id_jurnal = kt.id_jurnal
                  GROUP BY ju.id_jurnal
                  ORDER BY ju.tanggal DESC, ju.no_transaksi DESC";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function simpanJurnal($data)
    {
        $isTransactionActive = $this->db->inTransaction();
        if (!$isTransactionActive) {
            $this->db->beginTransaction();
        }
        
        try {
            $queryHeader = "INSERT INTO jurnal_umum (no_transaksi, tanggal, deskripsi, sumber_jurnal) 
                            VALUES (:no_transaksi, :tanggal, :deskripsi, :sumber_jurnal)";
            $this->db->query($queryHeader);
            $this->db->bind('no_transaksi', $data['no_transaksi']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('sumber_jurnal', $data['sumber_jurnal'] ?? 'Jurnal Umum');
            $this->db->execute();
            $id_jurnal = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO jurnal_detail (id_jurnal, kode_akun, debit, kredit)
                            VALUES (:id_jurnal, :kode_akun, :debit, :kredit)";
            foreach ($data['details'] as $detail) {
                if(!empty($detail['kode_akun']) && (isset($detail['debit']) && $detail['debit'] > 0 || isset($detail['kredit']) && $detail['kredit'] > 0)){
                    $this->db->query($queryDetail);
                    $this->db->bind('id_jurnal', $id_jurnal);
                    $this->db->bind('kode_akun', $detail['kode_akun']);
                    $this->db->bind('debit', (float)($detail['debit'] ?? 0));
                    $this->db->bind('kredit', (float)($detail['kredit'] ?? 0));
                    $this->db->execute();
                }
            }
            
            if (!$isTransactionActive) {
                $this->db->commit();
            }
            return $id_jurnal;

        } catch (\PDOException $e) {
            if (!$isTransactionActive) {
                $this->db->rollBack();
            }
            throw $e; 
        }
    }
    
    public function updateJurnal($data) {
        if ($this->isJurnalLocked($data['id_jurnal'])) {
            return -1;
        }
        $isTransactionActive = $this->db->inTransaction();
        if (!$isTransactionActive) {
            $this->db->beginTransaction();
        }
        try {
            $queryHeader = "UPDATE jurnal_umum SET no_transaksi = :no_transaksi, tanggal = :tanggal, deskripsi = :deskripsi WHERE id_jurnal = :id_jurnal";
            $this->db->query($queryHeader);
            $this->db->bind('no_transaksi', $data['no_transaksi']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('id_jurnal', $data['id_jurnal']);
            $this->db->execute();
            $this->db->query("DELETE FROM jurnal_detail WHERE id_jurnal = :id_jurnal");
            $this->db->bind('id_jurnal', $data['id_jurnal']);
            $this->db->execute();
            $queryDetail = "INSERT INTO jurnal_detail (id_jurnal, kode_akun, debit, kredit) VALUES (:id_jurnal, :kode_akun, :debit, :kredit)";
            foreach ($data['details'] as $detail) {
                if(!empty($detail['kode_akun']) && ($detail['debit'] > 0 || $detail['kredit'] > 0)){
                    $this->db->query($queryDetail);
                    $this->db->bind('id_jurnal', $data['id_jurnal']);
                    $this->db->bind('kode_akun', $detail['kode_akun']);
                    $this->db->bind('debit', (float)$detail['debit']);
                    $this->db->bind('kredit', (float)$detail['kredit']);
                    $this->db->execute();
                }
            }
            if (!$isTransactionActive) {
                $this->db->commit();
            }
            return 1;
        } catch (\PDOException $e) {
            if (!$isTransactionActive) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function hapusJurnal($id, $force = false) {
        if ($this->isJurnalLocked($id) && !$force) {
            return 0;
        }
        $query = "DELETE FROM jurnal_umum WHERE id_jurnal = :id_jurnal";
        $this->db->query($query);
        $this->db->bind('id_jurnal', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getJurnalWithDetailsById($id) {
        $sql = "SELECT ju.*, jd.kode_akun, jd.debit, jd.kredit, a.nama_akun
                FROM jurnal_umum ju
                LEFT JOIN jurnal_detail jd ON ju.id_jurnal = jd.id_jurnal
                LEFT JOIN akun a ON jd.kode_akun = a.kode_akun
                WHERE ju.id_jurnal = :id_jurnal";
        $this->db->query($sql);
        $this->db->bind('id_jurnal', $id);
        $results = $this->db->resultSet();
        if(empty($results)){ return null; }
        $jurnal = [
            'id_jurnal' => $results[0]['id_jurnal'],
            'no_transaksi' => $results[0]['no_transaksi'],
            'tanggal' => $results[0]['tanggal'],
            'deskripsi' => $results[0]['deskripsi'],
            'sumber_jurnal' => $results[0]['sumber_jurnal'],
            'is_locked' => $results[0]['is_locked'],
            'details' => []
        ];
        if (!is_null($results[0]['kode_akun'])) {
            foreach($results as $row){
                $jurnal['details'][] = [
                    'kode_akun' => $row['kode_akun'],
                    'nama_akun' => $row['nama_akun'],
                    'debit' => $row['debit'],
                    'kredit' => $row['kredit']
                ];
            }
        }
        return $jurnal;
    }
    
    public function isJurnalLocked($id) {
        $this->db->query("SELECT is_locked FROM jurnal_umum WHERE id_jurnal = :id");
        $this->db->bind('id', $id);
        $result = $this->db->single();
        return ($result && $result['is_locked'] == 1);
    }
    
    public function getBukuBesar($kode_akun, $tanggal_mulai, $tanggal_selesai) {
        $this->db->query("SELECT saldo_awal, posisi_saldo_normal FROM akun WHERE kode_akun = :kode_akun");
        $this->db->bind('kode_akun', $kode_akun);
        $akunInfo = $this->db->single();
        if (!$akunInfo) {
            return ['saldo_awal_periode' => 0, 'posisi_saldo_normal' => 'Debit', 'transaksi' => []];
        }
        $querySaldoSebelum = "SELECT COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit
                             FROM jurnal_detail jd
                             JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                             WHERE jd.kode_akun = :kode_akun AND ju.tanggal < :tanggal_mulai";
        $this->db->query($querySaldoSebelum);
        $this->db->bind('kode_akun', $kode_akun);
        $this->db->bind('tanggal_mulai', $tanggal_mulai);
        $saldoSebelum = $this->db->single();
        $saldo_awal_periode = (float)$akunInfo['saldo_awal'];
        if ($akunInfo['posisi_saldo_normal'] == 'Debit') {
            $saldo_awal_periode += ((float)$saldoSebelum['total_debit'] - (float)$saldoSebelum['total_kredit']);
        } else {
            $saldo_awal_periode += ((float)$saldoSebelum['total_kredit'] - (float)$saldoSebelum['total_debit']);
        }
        $queryTransaksi = "SELECT ju.tanggal, ju.no_transaksi, ju.deskripsi, jd.debit, jd.kredit
                           FROM jurnal_detail jd
                           JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                           WHERE jd.kode_akun = :kode_akun
                           AND ju.tanggal BETWEEN :tanggal_mulai AND :tanggal_selesai
                           ORDER BY ju.tanggal ASC, ju.id_jurnal ASC";
        $this->db->query($queryTransaksi);
        $this->db->bind('kode_akun', $kode_akun);
        $this->db->bind('tanggal_mulai', $tanggal_mulai);
        $this->db->bind('tanggal_selesai', $tanggal_selesai);
        $transaksi = $this->db->resultSet();
        return [
            'saldo_awal_periode' => $saldo_awal_periode,
            'posisi_saldo_normal' => $akunInfo['posisi_saldo_normal'],
            'transaksi' => $transaksi
        ];
    }

    public function getNeracaSaldo($tanggal_selesai)
    {
        $sql = "
            SELECT a.kode_akun, a.nama_akun, a.posisi_saldo_normal, a.saldo_awal,
                   COALESCE(SUM(CASE WHEN ju.tanggal <= :tanggal_selesai THEN jd.debit ELSE 0 END), 0) as total_debit,
                   COALESCE(SUM(CASE WHEN ju.tanggal <= :tanggal_selesai THEN jd.kredit ELSE 0 END), 0) as total_kredit
            FROM akun a
            LEFT JOIN jurnal_detail jd ON a.kode_akun = jd.kode_akun
            LEFT JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
            WHERE a.tipe_akun = 'Detail'
            GROUP BY a.kode_akun ORDER BY a.kode_akun ASC";
        $this->db->query($sql);
        $this->db->bind('tanggal_selesai', $tanggal_selesai);
        $results = $this->db->resultSet();
        $neraca_saldo = [];
        foreach ($results as $row) {
            $saldo_akhir = (float)$row['saldo_awal'];
            if ($row['posisi_saldo_normal'] == 'Debit') {
                $saldo_akhir += (float)$row['total_debit'] - (float)$row['total_kredit'];
            } else {
                $saldo_akhir += (float)$row['total_kredit'] - (float)$row['total_debit'];
            }
            $neraca_saldo[] = [
                'kode_akun' => $row['kode_akun'], 'nama_akun' => $row['nama_akun'],
                'debit' => ($row['posisi_saldo_normal'] == 'Debit') ? $saldo_akhir : 0,
                'kredit' => ($row['posisi_saldo_normal'] == 'Kredit') ? $saldo_akhir : 0
            ];
        }
        return $neraca_saldo;
    }

    public function getLabaRugi($tgl_mulai_1, $tgl_selesai_1, $tgl_mulai_2 = null, $tgl_selesai_2 = null) 
    {
        // PERBAIKAN: Query ini menggunakan subquery untuk performa dan akurasi yang lebih baik.
        $sql = "
            SELECT 
                a.kode_akun, a.nama_akun, a.posisi_saldo_normal,
                COALESCE(trx1.total_debit, 0) as total_debit_1,
                COALESCE(trx1.total_kredit, 0) as total_kredit_1,
                COALESCE(trx2.total_debit, 0) as total_debit_2,
                COALESCE(trx2.total_kredit, 0) as total_kredit_2
            FROM akun a
            LEFT JOIN (
                SELECT jd.kode_akun, SUM(jd.debit) AS total_debit, SUM(jd.kredit) AS total_kredit
                FROM jurnal_detail jd
                JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                WHERE ju.tanggal BETWEEN :tgl_mulai_1 AND :tgl_selesai_1
                GROUP BY jd.kode_akun
            ) trx1 ON a.kode_akun = trx1.kode_akun
            LEFT JOIN (
                SELECT jd.kode_akun, SUM(jd.debit) AS total_debit, SUM(jd.kredit) AS total_kredit
                FROM jurnal_detail jd
                JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                WHERE ju.tanggal BETWEEN :tgl_mulai_2 AND :tgl_selesai_2
                GROUP BY jd.kode_akun
            ) trx2 ON a.kode_akun = trx2.kode_akun
            WHERE 
                a.tipe_akun = 'Detail' AND (a.kode_akun LIKE '4-%' OR a.kode_akun LIKE '5-%' OR a.kode_akun LIKE '6-%' OR a.kode_akun LIKE '7-%' OR a.kode_akun LIKE '8-%')
            ORDER BY a.kode_akun ASC";
            
        $this->db->query($sql);
        $this->db->bind('tgl_mulai_1', $tgl_mulai_1);
        $this->db->bind('tgl_selesai_1', $tgl_selesai_1);
        // Bind periode pembanding hanya jika ada
        $this->db->bind('tgl_mulai_2', $tgl_mulai_2 ?? $tgl_mulai_1);
        $this->db->bind('tgl_selesai_2', $tgl_selesai_2 ?? $tgl_selesai_1);
        $results = $this->db->resultSet();
        
        $laporan = ['pendapatan' => [], 'beban' => [], 'total_pendapatan_1' => 0, 'total_beban_1' => 0, 'total_pendapatan_2' => 0, 'total_beban_2' => 0];
        foreach ($results as $row) {
            $saldo1 = ($row['posisi_saldo_normal'] == 'Kredit') ? ((float)$row['total_kredit_1'] - (float)$row['total_debit_1']) : ((float)$row['total_debit_1'] - (float)$row['total_kredit_1']);
            $saldo2 = ($row['posisi_saldo_normal'] == 'Kredit') ? ((float)$row['total_kredit_2'] - (float)$row['total_debit_2']) : ((float)$row['total_debit_2'] - (float)$row['total_kredit_2']);
            
            if ($saldo1 != 0 || $saldo2 != 0) {
                $item = ['kode_akun' => $row['kode_akun'], 'nama_akun' => $row['nama_akun'], 'total_1' => $saldo1, 'total_2' => $saldo2];
                if (substr($row['kode_akun'], 0, 1) == '4' || substr($row['kode_akun'], 0, 1) == '7') {
                    $laporan['pendapatan'][$row['kode_akun']] = $item;
                    $laporan['total_pendapatan_1'] += $saldo1;
                    $laporan['total_pendapatan_2'] += $saldo2;
                } else {
                    $laporan['beban'][$row['kode_akun']] = $item;
                    $laporan['total_beban_1'] += $saldo1;
                    $laporan['total_beban_2'] += $saldo2;
                }
            }
        }
        return $laporan;
    }

    public function getNeracaLajur($tanggal_selesai) {
        $neracaSaldo = $this->getNeracaSaldo($tanggal_selesai);
        $worksheet = [];
        $totals = ['ns_debit' => 0, 'ns_kredit' => 0, 'lr_debit' => 0, 'lr_kredit' => 0, 'neraca_debit' => 0, 'neraca_kredit' => 0];
        foreach ($neracaSaldo as $akun) {
            $row = ['kode_akun' => $akun['kode_akun'], 'nama_akun' => $akun['nama_akun'], 'ns_debit' => $akun['debit'], 'ns_kredit' => $akun['kredit'], 'lr_debit' => 0, 'lr_kredit' => 0, 'neraca_debit' => 0, 'neraca_kredit' => 0];
            $kode_awal = substr($akun['kode_akun'], 0, 1);
            if (in_array($kode_awal, ['4', '5', '6', '7', '8'])) {
                $row['lr_debit'] = $akun['debit'];
                $row['lr_kredit'] = $akun['kredit'];
            } else {
                $row['neraca_debit'] = $akun['debit'];
                $row['neraca_kredit'] = $akun['kredit'];
            }
            $worksheet[] = $row;
            $totals['ns_debit'] += $akun['debit'];
            $totals['ns_kredit'] += $akun['kredit'];
            $totals['lr_debit'] += $row['lr_debit'];
            $totals['lr_kredit'] += $row['lr_kredit'];
            $totals['neraca_debit'] += $row['neraca_debit'];
            $totals['neraca_kredit'] += $row['neraca_kredit'];
        }
        $labaRugi = $totals['lr_kredit'] - $totals['lr_debit'];
        return ['data' => $worksheet, 'totals' => $totals, 'laba_rugi_bersih' => $labaRugi];
    }

    public function getPosisiKeuangan($tgl_selesai_1, $tgl_selesai_2 = null)
    {
        // Fungsi helper untuk memproses satu periode
        $processPeriod = function($tanggal) {
            $neracaLajur = $this->getNeracaLajur($tanggal);
            $result = [
                'aset' => [], 'kewajiban' => [], 'modal' => [],
                'total_aset' => 0, 'total_kewajiban' => 0, 'total_modal' => 0,
            ];
            if (empty($neracaLajur['data'])) return $result;

            foreach ($neracaLajur['data'] as $akun) {
                if ($akun['neraca_debit'] > 0 || $akun['neraca_kredit'] > 0) {
                    $item = ['kode_akun' => $akun['kode_akun'], 'nama_akun' => $akun['nama_akun'], 'total' => ($akun['neraca_debit'] > 0) ? $akun['neraca_debit'] : $akun['neraca_kredit']];
                    $kode_awal = substr($akun['kode_akun'], 0, 1);
                    if ($kode_awal == '1') { $result['aset'][] = $item; $result['total_aset'] += $item['total']; } 
                    elseif ($kode_awal == '2') { $result['kewajiban'][] = $item; $result['total_kewajiban'] += $item['total']; } 
                    elseif ($kode_awal == '3') { $result['modal'][] = $item; $result['total_modal'] += $item['total']; }
                }
            }
            $labaRugiBersih = $neracaLajur['laba_rugi_bersih'];
            $result['modal'][] = ['kode_akun' => '', 'nama_akun' => 'Laba (Rugi) Periode Berjalan', 'total' => $labaRugiBersih];
            $result['total_modal'] += $labaRugiBersih;
            return $result;
        };

        $laporan_1 = $processPeriod($tgl_selesai_1);
        $laporan_2 = $tgl_selesai_2 ? $processPeriod($tgl_selesai_2) : null;

        return ['periode_1' => $laporan_1, 'periode_2' => $laporan_2];
    }

    public function getPerubahanEkuitas($tgl_mulai_1, $tgl_selesai_1, $tgl_mulai_2 = null, $tgl_selesai_2 = null) {
        $calculatePeriod = function($start_date, $end_date) {
            $neracaSaldoAwal = $this->getNeracaSaldo(date('Y-m-d', strtotime($start_date . ' -1 day')));
            $modalAwal = 0;
            foreach ($neracaSaldoAwal as $akun) {
                if (substr($akun['kode_akun'], 0, 1) == '3') {
                    $modalAwal += $akun['kredit'] - $akun['debit'];
                }
            }
            $labaRugiData = $this->getLabaRugi($start_date, $end_date);
            $labaRugiPeriodeBerjalan = ($labaRugiData['total_pendapatan_1'] ?? 0) - ($labaRugiData['total_beban_1'] ?? 0);
            $queryPerubahanLangsung = "
                SELECT COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0) as net_change
                FROM jurnal_detail jd
                JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                WHERE jd.kode_akun LIKE '3-%' AND ju.tanggal BETWEEN :start_date AND :end_date";
            $this->db->query($queryPerubahanLangsung);
            $this->db->bind('start_date', $start_date);
            $this->db->bind('end_date', $end_date);
            $netPerubahanModalLangsung = (float)$this->db->single()['net_change'];
            $modalAkhir = $modalAwal + $netPerubahanModalLangsung + $labaRugiPeriodeBerjalan;
            return [
                'modal_awal' => $modalAwal,
                'perubahan_modal_langsung' => $netPerubahanModalLangsung,
                'laba_rugi_periode_berjalan' => $labaRugiPeriodeBerjalan,
                'modal_akhir' => $modalAkhir
            ];
        };
        $laporan_1 = $calculatePeriod($tgl_mulai_1, $tgl_selesai_1);
        $laporan_2 = ($tgl_mulai_2 && $tgl_selesai_2) ? $calculatePeriod($tgl_mulai_2, $tgl_selesai_2) : null;
        return ['periode_1' => $laporan_1, 'periode_2' => $laporan_2];
    }

    public function getArusKas($tanggal_mulai, $tanggal_selesai) {
        $labaRugiData = $this->getLabaRugi($tanggal_mulai, $tanggal_selesai);
        $labaBersih = ($labaRugiData['total_pendapatan_1'] ?? 0) - ($labaRugiData['total_beban_1'] ?? 0);
        $neracaSaldoAwal = $this->getNeracaSaldo(date('Y-m-d', strtotime($tanggal_mulai . ' -1 day')));
        $neracaSaldoAkhir = $this->getNeracaSaldo($tanggal_selesai);
        $saldoAkun = [];
        foreach ($neracaSaldoAwal as $akun) {
            $saldoAkun[$akun['kode_akun']]['awal'] = $akun['debit'] - $akun['kredit'];
        }
        foreach ($neracaSaldoAkhir as $akun) {
            $saldoAkun[$akun['kode_akun']]['akhir'] = $akun['debit'] - $akun['kredit'];
        }
        $perubahanPiutang = ($saldoAkun['1-1103']['akhir'] ?? 0) - ($saldoAkun['1-1103']['awal'] ?? 0);
        $perubahanPersediaan = ($saldoAkun['1-1200']['akhir'] ?? 0) - ($saldoAkun['1-1200']['awal'] ?? 0);
        $perubahanUtang = ($saldoAkun['2-1100']['akhir'] ?? 0) - ($saldoAkun['2-1100']['awal'] ?? 0);
        $kasAwal = 0; $kasAkhir = 0;
        foreach ($neracaSaldoAwal as $akun) { if (substr($akun['kode_akun'], 0, 4) == '1-11') $kasAwal += $akun['debit'] - $akun['kredit']; }
        foreach ($neracaSaldoAkhir as $akun) { if (substr($akun['kode_akun'], 0, 4) == '1-11') $kasAkhir += $akun['debit'] - $akun['kredit']; }
        return [
            'kas_awal' => $kasAwal,
            'kas_akhir' => $kasAkhir,
            'laba_bersih' => $labaBersih,
            'penyesuaian' => [
                ['label' => 'Kenaikan/Penurunan Piutang Usaha', 'jumlah' => -$perubahanPiutang],
                ['label' => 'Kenaikan/Penurunan Persediaan', 'jumlah' => -$perubahanPersediaan],
                ['label' => 'Kenaikan/Penurunan Utang Usaha', 'jumlah' => $perubahanUtang]
            ]
        ];
    }
}