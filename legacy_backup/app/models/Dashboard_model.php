<?php

class Dashboard_model {
    private $db;

    /**
     * Constructor yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Mengambil ringkasan data utama (Total Piutang, Utang, dan Nilai Persediaan).
     * @return array Ringkasan data.
     */
    public function getSummary() {
        $this->db->query("SELECT 
            (SELECT SUM(saldo_terkini_piutang) FROM pelanggan) as total_piutang,
            (SELECT SUM(saldo_terkini_hutang) FROM pemasok) as total_utang,
            (SELECT SUM(stok_saat_ini * harga_beli) FROM master_persediaan) as nilai_persediaan
        ");
        return $this->db->single();
    }

    /**
     * Mengambil data tren penjualan vs pembelian untuk 6 bulan terakhir.
     * @return array Data tren bulanan.
     */
    public function getSalesPurchasesTrend() {
        $query = "
            SELECT 
                CONCAT(YEAR(tanggal), '-', LPAD(MONTH(tanggal), 2, '0')) as periode,
                SUM(CASE WHEN tipe = 'penjualan' THEN total ELSE 0 END) as total_penjualan,
                SUM(CASE WHEN tipe = 'pembelian' THEN total ELSE 0 END) as total_pembelian
            FROM (
                SELECT tanggal_faktur as tanggal, total, 'penjualan' as tipe FROM penjualan
                WHERE tanggal_faktur >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                UNION ALL
                SELECT tanggal_faktur as tanggal, total, 'pembelian' as tipe FROM pembelian
                WHERE tanggal_faktur >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            ) as transaksi
            GROUP BY periode
            ORDER BY periode ASC
        ";
        $this->db->query($query);
        return $this->db->resultSet();
    }
}

