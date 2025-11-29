<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    use \App\Traits\CheckSaldoTrait;

    public function index()
    {
        $jurnal = Jurnal::orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(20);
        return view('jurnal.index', compact('jurnal'));
    }

    public function create()
    {
        $akun = Akun::orderBy('kode_akun')->get();
        
        // Generate No Transaksi Otomatis (JU-xxxx)
        $lastJurnal = Jurnal::where('sumber_jurnal', 'Manual')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastJurnal && preg_match('/JU-(\d+)/', $lastJurnal->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'JU-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        return view('jurnal.create', compact('akun', 'noTransaksi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'details' => 'required|array|min:2',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.kredit' => 'required|numeric|min:0',
        ]);

        // Validasi Balance
        $totalDebit = collect($request->details)->sum('debit');
        $totalKredit = collect($request->details)->sum('kredit');

        if ($totalDebit != $totalKredit) {
            return back()->with('error', 'Jurnal tidak seimbang (Balance). Total Debit: ' . $totalDebit . ', Total Kredit: ' . $totalKredit)->withInput();
        }

        try {
            DB::beginTransaction();

            // Cek Saldo untuk setiap akun yang di-Kredit jika itu adalah Kas & Bank
            foreach ($request->details as $detail) {
                if ($detail['kredit'] > 0) {
                    if (!$this->checkSaldoCukup($detail['kode_akun'], $detail['kredit'])) {
                        $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                        $saldo = $this->getSaldoSaatIni($detail['kode_akun']);
                        throw new \Exception("Saldo akun " . $akun->nama_akun . " tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
                    }
                }
            }

            $jurnal = Jurnal::create([
                'no_transaksi' => $request->no_transaksi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
                'sumber_jurnal' => 'Manual',
                'is_locked' => 0
            ]);

            foreach ($request->details as $detail) {
                if ($detail['debit'] > 0 || $detail['kredit'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => $detail['debit'],
                        'kredit' => $detail['kredit'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal umum berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Jurnal $jurnal)
    {
        $jurnal->load('details.akun');
        return view('jurnal.show', compact('jurnal'));
    }
}
