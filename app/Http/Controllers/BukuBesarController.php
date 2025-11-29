<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $akunList = Akun::orderBy('kode_akun')->get();
        
        $kodeAkun = $request->input('kode_akun');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $transaksi = collect([]);
        $saldoAwal = 0;
        $selectedAkun = null;

        if ($kodeAkun) {
            $selectedAkun = Akun::find($kodeAkun);
            
            // Hitung Saldo Awal (Transaksi sebelum start_date)
            // Note: Ini simplifikasi. Idealnya ada tabel saldo_awal_periode atau hitung dari awal tahun.
            // Untuk sekarang kita hitung semua transaksi sebelum start_date.
            
            $prevTrans = JurnalDetail::where('kode_akun', $kodeAkun)
                ->whereHas('jurnal', function ($q) use ($startDate) {
                    $q->where('tanggal', '<', $startDate);
                })
                ->get();

            $debitAwal = $prevTrans->sum('debit');
            $kreditAwal = $prevTrans->sum('kredit');

            if ($selectedAkun->saldo_normal == 'Debit') {
                $saldoAwal = $debitAwal - $kreditAwal;
            } else {
                $saldoAwal = $kreditAwal - $debitAwal;
            }

            // Ambil Transaksi Periode Ini
            $transaksi = JurnalDetail::with('jurnal')
                ->where('kode_akun', $kodeAkun)
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->get()
                ->sortBy(function($detail) {
                    return $detail->jurnal->tanggal . $detail->jurnal->created_at;
                });
        }

        return view('bukubesar.index', compact('akunList', 'transaksi', 'saldoAwal', 'selectedAkun', 'startDate', 'endDate', 'kodeAkun'));
    }
}
