<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    use \App\Traits\CheckSaldoTrait;

    public function index()
    {
        // Ambil semua akun Kas & Bank
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();

        // Hitung saldo terkini untuk setiap akun kas
        foreach ($akunKas as $akun) {
            $debit = JurnalDetail::where('kode_akun', $akun->kode_akun)->sum('debit');
            $kredit = JurnalDetail::where('kode_akun', $akun->kode_akun)->sum('kredit');
            $akun->saldo_terkini = $debit - $kredit;
        }

        return view('kas.index', compact('akunKas'));
    }

    public function transfer()
    {
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        
        // Generate No Transaksi (TF-xxxx)
        $lastTrans = Jurnal::where('sumber_jurnal', 'Transfer Kas')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastTrans && preg_match('/TF-(\d+)/', $lastTrans->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'TF-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        return view('kas.transfer', compact('akunKas', 'noTransaksi'));
    }

    public function storeTransfer(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date',
            'dari_akun' => 'required|exists:akun,kode_akun',
            'ke_akun' => 'required|exists:akun,kode_akun|different:dari_akun',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Cek Saldo Akun Asal
            if (!$this->checkSaldoCukup($request->dari_akun, $request->jumlah)) {
                $saldo = $this->getSaldoSaatIni($request->dari_akun);
                throw new \Exception("Saldo akun asal tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
            }

            // 1. Buat Jurnal Header
            $jurnal = Jurnal::create([
                'no_transaksi' => $request->no_transaksi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->keterangan,
                'sumber_jurnal' => 'Transfer Kas',
                'is_locked' => 0
            ]);

            // 2. Kredit Akun Asal (Keluar)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->dari_akun,
                'debit' => 0,
                'kredit' => $request->jumlah
            ]);

            // 3. Debit Akun Tujuan (Masuk)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->ke_akun,
                'debit' => $request->jumlah,
                'kredit' => 0
            ]);

            DB::commit();
            return redirect()->route('kas.index')->with('success', 'Transfer kas berhasil dilakukan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan transfer: ' . $e->getMessage())->withInput();
        }
    }
}
