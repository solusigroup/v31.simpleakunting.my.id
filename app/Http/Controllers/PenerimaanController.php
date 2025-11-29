<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function index()
    {
        $penerimaan = Jurnal::where('sumber_jurnal', 'Penerimaan Kas')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('penerimaan.index', compact('penerimaan'));
    }

    public function create()
    {
        // Akun Kas/Bank untuk Debit
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        
        // Akun Pendapatan/Piutang untuk Kredit
        $akunPendapatan = Akun::whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya', 'Piutang'])->orderBy('kode_akun')->get();
        
        $pelanggan = Pelanggan::orderBy('nama_pelanggan')->get();

        // Generate No Transaksi (CR-xxxx)
        $lastTrans = Jurnal::where('sumber_jurnal', 'Penerimaan Kas')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastTrans && preg_match('/CR-(\d+)/', $lastTrans->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'CR-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        return view('penerimaan.create', compact('akunKas', 'akunPendapatan', 'pelanggan', 'noTransaksi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date',
            'akun_kas' => 'required|exists:akun,kode_akun', // Debit
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'keterangan' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun', // Kredit
            'details.*.jumlah' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalTerima = collect($request->details)->sum('jumlah');

            // 1. Buat Jurnal Header
            $jurnal = Jurnal::create([
                'no_transaksi' => $request->no_transaksi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->keterangan,
                'sumber_jurnal' => 'Penerimaan Kas',
                'is_locked' => 0
            ]);

            // 2. Jurnal Detail: Debit Kas/Bank
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->akun_kas,
                'debit' => $totalTerima,
                'kredit' => 0
            ]);

            // 3. Jurnal Detail: Kredit Akun Lawan (Pendapatan/Piutang)
            foreach ($request->details as $detail) {
                if ($detail['jumlah'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => 0,
                        'kredit' => $detail['jumlah']
                    ]);

                    // Jika akun Piutang dan ada Pelanggan, update saldo piutang pelanggan
                    $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                    if ($akun && $akun->tipe_akun == 'Piutang' && $request->id_pelanggan) {
                        $pelanggan = Pelanggan::find($request->id_pelanggan);
                        $pelanggan->saldo_terkini_piutang -= $detail['jumlah'];
                        $pelanggan->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan kas berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $jurnal = Jurnal::with('details.akun')->findOrFail($id);
        return view('penerimaan.show', compact('jurnal'));
    }
}
