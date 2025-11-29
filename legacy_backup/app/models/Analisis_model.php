<?php

require_once 'Jurnal_model.php';

class Analisis_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getRasioKeuangan($tanggal_mulai, $tanggal_selesai) {
        $jurnalModel = new Jurnal_model($this->db);

        $labaRugi = $jurnalModel->getLabaRugi($tanggal_mulai, $tanggal_selesai);
        $posisiKeuangan = $jurnalModel->getPosisiKeuangan($tanggal_selesai);
        
        $labaBersih = ($labaRugi['total_pendapatan_1'] ?? 0) - ($labaRugi['total_beban_1'] ?? 0);
        $totalPendapatan = $labaRugi['total_pendapatan_1'] ?? 0;
        
        $asetLancar = 0; $persediaan = 0;
        foreach(($posisiKeuangan['periode_1']['aset'] ?? []) as $akun){
            if(substr($akun['kode_akun'], 0, 3) == '1-1'){ // Aset Lancar
                $asetLancar += $akun['total'];
            }
            if($akun['kode_akun'] == '1-1200'){ // Akun Persediaan
                $persediaan = $akun['total'];
            }
        }
        
        $utangLancar = 0;
        foreach(($posisiKeuangan['periode_1']['kewajiban'] ?? []) as $akun){
            if(substr($akun['kode_akun'], 0, 3) == '2-1'){ // Utang Lancar
                $utangLancar += $akun['total'];
            }
        }
        
        $totalAset = $posisiKeuangan['periode_1']['total_aset'] ?? 0;
        $totalEkuitas = $posisiKeuangan['periode_1']['total_modal'] ?? 0;

        $rasioLancar = ($utangLancar > 0) ? $asetLancar / $utangLancar : 0;
        $rasioCepat = ($utangLancar > 0) ? ($asetLancar - $persediaan) / $utangLancar : 0;
        $npm = ($totalPendapatan > 0) ? ($labaBersih / $totalPendapatan) * 100 : 0;
        $roa = ($totalAset > 0) ? ($labaBersih / $totalAset) * 100 : 0;
        $roe = ($totalEkuitas > 0) ? ($labaBersih / $totalEkuitas) * 100 : 0;

        return [
            'likuiditas' => [
                'rasio_lancar' => $rasioLancar,
                'rasio_cepat' => $rasioCepat
            ],
            'profitabilitas' => [
                'npm' => $npm,
                'roa' => $roa,
                'roe' => $roe
            ]
        ];
    }
}

