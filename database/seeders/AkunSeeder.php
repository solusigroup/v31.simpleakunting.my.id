<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [
            // ASET LANCAR (1-1xxxx)
            ['kode_akun' => '1-10001', 'nama_akun' => 'Kas Kecil', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-10002', 'nama_akun' => 'Bank BCA', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-10003', 'nama_akun' => 'Bank Mandiri', 'tipe_akun' => 'Kas & Bank', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-10100', 'nama_akun' => 'Piutang Usaha', 'tipe_akun' => 'Piutang', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-10200', 'nama_akun' => 'Persediaan Barang Dagang', 'tipe_akun' => 'Persediaan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-10300', 'nama_akun' => 'Perlengkapan', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-10400', 'nama_akun' => 'Sewa Dibayar Dimuka', 'tipe_akun' => 'Aset Lancar Lainnya', 'saldo_normal' => 'Debit'],

            // ASET TETAP (1-2xxxx)
            ['kode_akun' => '1-20100', 'nama_akun' => 'Peralatan Kantor', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-20101', 'nama_akun' => 'Akum. Peny. Peralatan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '1-20200', 'nama_akun' => 'Kendaraan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1-20201', 'nama_akun' => 'Akum. Peny. Kendaraan', 'tipe_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit'],

            // KEWAJIBAN (2-xxxxx)
            ['kode_akun' => '2-10100', 'nama_akun' => 'Utang Usaha', 'tipe_akun' => 'Utang Usaha', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-10200', 'nama_akun' => 'Utang Gaji', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-10300', 'nama_akun' => 'Utang Pajak', 'tipe_akun' => 'Kewajiban Lancar Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2-20100', 'nama_akun' => 'Utang Bank Jangka Panjang', 'tipe_akun' => 'Kewajiban Jangka Panjang', 'saldo_normal' => 'Kredit'],

            // EKUITAS (3-xxxxx)
            ['kode_akun' => '3-10000', 'nama_akun' => 'Modal Pemilik', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '3-20000', 'nama_akun' => 'Prive Pemilik', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '3-30000', 'nama_akun' => 'Laba Ditahan', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],

            // PENDAPATAN (4-xxxxx)
            ['kode_akun' => '4-10000', 'nama_akun' => 'Penjualan Barang', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4-20000', 'nama_akun' => 'Pendapatan Jasa', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4-30000', 'nama_akun' => 'Retur Penjualan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '4-40000', 'nama_akun' => 'Potongan Penjualan', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Debit'],

            // HARGA POKOK PENJUALAN (5-xxxxx)
            ['kode_akun' => '5-10000', 'nama_akun' => 'Harga Pokok Penjualan', 'tipe_akun' => 'HPP', 'saldo_normal' => 'Debit'],

            // BEBAN (6-xxxxx)
            ['kode_akun' => '6-10001', 'nama_akun' => 'Beban Gaji', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-10002', 'nama_akun' => 'Beban Sewa', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-10003', 'nama_akun' => 'Beban Listrik, Air & Telp', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-10004', 'nama_akun' => 'Beban Perlengkapan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-10005', 'nama_akun' => 'Beban Penyusutan', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-10006', 'nama_akun' => 'Beban Pemasaran', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6-10007', 'nama_akun' => 'Beban Lain-lain', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],

            // PENDAPATAN & BEBAN LAIN (8-xxxxx & 9-xxxxx)
            ['kode_akun' => '8-10000', 'nama_akun' => 'Pendapatan Bunga', 'tipe_akun' => 'Pendapatan Lainnya', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '9-10000', 'nama_akun' => 'Beban Administrasi Bank', 'tipe_akun' => 'Beban Lainnya', 'saldo_normal' => 'Debit'],
        ];

        foreach ($akun as $a) {
            DB::table('akun')->updateOrInsert(
                ['kode_akun' => $a['kode_akun']],
                [
                    'nama_akun' => $a['nama_akun'],
                    'tipe_akun' => $a['tipe_akun'],
                    'saldo_normal' => $a['saldo_normal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
