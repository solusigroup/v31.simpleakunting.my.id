<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalDetail;
use App\Models\Persediaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function neraca(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $bandingTanggal = $request->input('banding_tanggal'); // Tanggal pembanding (opsional)

        $perusahaan = DB::table('perusahaan')->find(1);

        // Ambil semua akun Neraca
        $akunNeraca = Akun::whereIn('tipe_akun', [
            'Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap',
            'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas'
        ])->orderBy('kode_akun')->get();

        // Helper untuk hitung saldo per tanggal
        $hitungSaldo = function ($tanggal) use ($akunNeraca) {
            return $akunNeraca->map(function ($akun) use ($tanggal) {
                // Clone akun agar tidak merubah referensi asli saat loop kedua
                $akunClone = clone $akun;
                
                $saldo = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function ($q) use ($tanggal) {
                        $q->where('tanggal', '<=', $tanggal);
                    })
                    ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
                    ->first();

                $totalDebit = $saldo->total_debit ?? 0;
                $totalKredit = $saldo->total_kredit ?? 0;

                if ($akun->saldo_normal == 'Debit') {
                    $akunClone->saldo_akhir = $totalDebit - $totalKredit;
                } else {
                    $akunClone->saldo_akhir = $totalKredit - $totalDebit;
                }
                return $akunClone;
            });
        };

        // Data Utama
        $laporan = $hitungSaldo($perTanggal);
        
        // Data Pembanding (jika ada)
        $laporanBanding = $bandingTanggal ? $hitungSaldo($bandingTanggal) : collect([]);

        // Grouping Data Utama
        $asetLancar = $laporan->whereIn('tipe_akun', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya']);
        $asetTetap = $laporan->where('tipe_akun', 'Aset Tetap');
        $kewajiban = $laporan->whereIn('tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang']);
        $ekuitas = $laporan->where('tipe_akun', 'Ekuitas');

        // Laba Rugi Berjalan
        $labaRugiBerjalan = $this->hitungLabaRugi($perTanggal);
        $labaRugiBerjalanBanding = $bandingTanggal ? $this->hitungLabaRugi($bandingTanggal) : 0;

        return view('laporan.neraca', compact(
            'perusahaan', 'perTanggal', 'bandingTanggal', 
            'asetLancar', 'asetTetap', 'kewajiban', 'ekuitas', 
            'labaRugiBerjalan', 'labaRugiBerjalanBanding', 'laporanBanding'
        ));
    }

    public function labaRugi(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        
        $startBanding = $request->input('start_banding');
        $endBanding = $request->input('end_banding');

        $perusahaan = DB::table('perusahaan')->find(1);

        $akunLabaRugi = Akun::whereIn('tipe_akun', [
            'Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'
        ])->orderBy('kode_akun')->get();

        $hitungPeriode = function ($start, $end) use ($akunLabaRugi) {
            return $akunLabaRugi->map(function ($akun) use ($start, $end) {
                $akunClone = clone $akun;
                $saldo = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function ($q) use ($start, $end) {
                        $q->whereBetween('tanggal', [$start, $end]);
                    })
                    ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
                    ->first();

                $totalDebit = $saldo->total_debit ?? 0;
                $totalKredit = $saldo->total_kredit ?? 0;

                if ($akun->saldo_normal == 'Kredit') {
                    $akunClone->saldo_periode = $totalKredit - $totalDebit;
                } else {
                    $akunClone->saldo_periode = $totalDebit - $totalKredit;
                }
                return $akunClone;
            });
        };

        // Periode Utama
        $laporan = $hitungPeriode($startDate, $endDate);
        
        // Periode Pembanding
        $laporanBanding = ($startBanding && $endBanding) ? $hitungPeriode($startBanding, $endBanding) : collect([]);

        $pendapatan = $laporan->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
        $hpp = $laporan->where('tipe_akun', 'HPP');
        $beban = $laporan->whereIn('tipe_akun', ['Beban', 'Beban Lainnya']);

        return view('laporan.labarugi', compact(
            'perusahaan', 'startDate', 'endDate', 'startBanding', 'endBanding',
            'pendapatan', 'hpp', 'beban', 'laporanBanding'
        ));
    }

    public function arusKasLangsung(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $perusahaan = DB::table('perusahaan')->find(1);

        // Helper untuk mendapatkan total arus kas berdasarkan tipe akun lawan
        $getFlow = function ($tipeAkunLawan, $isMasuk) use ($startDate, $endDate) {
            // Cari jurnal detail yang melibatkan Kas & Bank
            // Dan lawan transaksinya adalah tipe akun tertentu
            
            // Logic:
            // 1. Ambil semua ID Jurnal yang memiliki detail akun Kas & Bank dalam range tanggal
            $jurnalIds = JurnalDetail::whereHas('akun', function($q) {
                    $q->where('tipe_akun', 'Kas & Bank');
                })
                ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->pluck('id_jurnal');

            // 2. Dari ID Jurnal tersebut, cari detail yang BUKAN Kas & Bank (Lawannya)
            // Dan tipe akun lawannya sesuai parameter
            $query = JurnalDetail::whereIn('id_jurnal', $jurnalIds)
                ->whereHas('akun', function($q) use ($tipeAkunLawan) {
                    if (is_array($tipeAkunLawan)) {
                        $q->whereIn('tipe_akun', $tipeAkunLawan);
                    } else {
                        $q->where('tipe_akun', $tipeAkunLawan);
                    }
                });

            // 3. Jika Arus Masuk (Cash Debit), maka Lawan adalah Kredit.
            // Jika Arus Keluar (Cash Kredit), maka Lawan adalah Debit.
            // Namun, di simple accounting ini, kita bisa sum amount lawannya.
            // Jika isMasuk = true (Penerimaan), kita cari total Kredit dari akun lawan.
            // Jika isMasuk = false (Pengeluaran), kita cari total Debit dari akun lawan.
            
            if ($isMasuk) {
                return $query->sum('kredit');
            } else {
                return $query->sum('debit');
            }
        };

        // --- AKTIVITAS OPERASI ---
        // Masuk: Dari Pelanggan (Piutang, Pendapatan)
        $terimaPelanggan = $getFlow(['Piutang', 'Pendapatan', 'Pendapatan Lainnya'], true);
        
        // Keluar: Ke Pemasok (Utang, HPP, Beban, Persediaan, Aset Lancar Lainnya)
        $bayarPemasok = $getFlow(['Utang Usaha', 'HPP', 'Beban', 'Beban Lainnya', 'Kewajiban Lancar Lainnya', 'Persediaan', 'Aset Lancar Lainnya'], false);

        $arusKasOperasi = $terimaPelanggan - $bayarPemasok;

        // --- AKTIVITAS INVESTASI ---
        // Masuk: Jual Aset Tetap
        $jualAset = $getFlow('Aset Tetap', true);
        // Keluar: Beli Aset Tetap
        $beliAset = $getFlow('Aset Tetap', false);

        $arusKasInvestasi = $jualAset - $beliAset;

        // --- AKTIVITAS PENDANAAN ---
        // Masuk: Modal, Utang Jangka Panjang
        $terimaPendanaan = $getFlow(['Ekuitas', 'Kewajiban Jangka Panjang'], true);
        // Keluar: Prive, Bayar Utang Jangka Panjang
        $bayarPendanaan = $getFlow(['Ekuitas', 'Kewajiban Jangka Panjang'], false);

        $arusKasPendanaan = $terimaPendanaan - $bayarPendanaan;

        $kenaikanKas = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;

        // Saldo Awal Kas
        $saldoAwal = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Kas & Bank');
            })
            ->whereHas('jurnal', function($q) use ($startDate) {
                $q->where('tanggal', '<', $startDate);
            })
            ->sum(DB::raw('debit - kredit'));

        $saldoAkhir = $saldoAwal + $kenaikanKas;

        return view('laporan.aruskas_langsung', compact(
            'perusahaan', 'startDate', 'endDate',
            'terimaPelanggan', 'bayarPemasok', 'arusKasOperasi',
            'jualAset', 'beliAset', 'arusKasInvestasi',
            'terimaPendanaan', 'bayarPendanaan', 'arusKasPendanaan',
            'kenaikanKas', 'saldoAwal', 'saldoAkhir'
        ));
    }

    public function arusKasTidakLangsung(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $perusahaan = DB::table('perusahaan')->find(1);

        // 1. Laba Bersih
        // Hitung Pendapatan - Beban periode ini
        $pendapatan = JurnalDetail::whereHas('akun', function($q) {
            $q->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
        })->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
        })->sum(DB::raw('kredit - debit'));

        $beban = JurnalDetail::whereHas('akun', function($q) {
            $q->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
        })->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
        })->sum(DB::raw('debit - kredit'));

        $labaBersih = $pendapatan - $beban;

        // 2. Penyesuaian Non-Kas (Penyusutan)
        // Cari akun beban penyusutan (biasanya ada kata 'Penyusutan' atau 'Depreciation')
        // Untuk simplifikasi, kita ambil semua akun Beban yang namanya mengandung 'Penyusutan'
        $bebanPenyusutan = JurnalDetail::whereHas('akun', function($q) {
            $q->where('nama_akun', 'like', '%Penyusutan%')
              ->orWhere('nama_akun', 'like', '%Depresiasi%');
        })->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
        })->sum('debit');

        // 3. Perubahan Modal Kerja
        $getChange = function ($tipeAkun, $saldoNormal) use ($startDate, $endDate) {
            // Hitung selisih saldo akhir - saldo awal periode
            // Saldo Awal
            $awal = JurnalDetail::whereHas('akun', function($q) use ($tipeAkun) {
                $q->where('tipe_akun', $tipeAkun);
            })->whereHas('jurnal', function ($q) use ($startDate) {
                $q->where('tanggal', '<', $startDate);
            })->sum(DB::raw($saldoNormal == 'Debit' ? 'debit - kredit' : 'kredit - debit'));

            // Saldo Akhir
            $akhir = JurnalDetail::whereHas('akun', function($q) use ($tipeAkun) {
                $q->where('tipe_akun', $tipeAkun);
            })->whereHas('jurnal', function ($q) use ($endDate) {
                $q->where('tanggal', '<=', $endDate);
            })->sum(DB::raw($saldoNormal == 'Debit' ? 'debit - kredit' : 'kredit - debit'));

            return $akhir - $awal;
        };

        // Kenaikan Piutang (Mengurangi Kas)
        $kenaikanPiutang = $getChange('Piutang', 'Debit');
        
        // Kenaikan Persediaan (Mengurangi Kas)
        $kenaikanPersediaan = $getChange('Persediaan', 'Debit');
        
        // Kenaikan Utang Usaha (Menambah Kas)
        $kenaikanUtang = $getChange('Utang Usaha', 'Kredit');

        // Arus Kas Operasi
        // Rumus: Laba Bersih + Penyusutan - Kenaikan Piutang - Kenaikan Persediaan + Kenaikan Utang
        $arusKasOperasi = $labaBersih + $bebanPenyusutan - $kenaikanPiutang - $kenaikanPersediaan + $kenaikanUtang;

        // --- INVESTASI & PENDANAAN (Sama dengan Metode Langsung) ---
        // Kita copy logic getFlow dari metode langsung atau buat private method shared.
        // Untuk cepatnya, kita duplikasi logic querynya di sini tapi disesuaikan.
        
        $getFlowSimple = function ($tipeAkunLawan, $isMasuk) use ($startDate, $endDate) {
             $jurnalIds = JurnalDetail::whereHas('akun', function($q) {
                    $q->where('tipe_akun', 'Kas & Bank');
                })
                ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->pluck('id_jurnal');

            $query = JurnalDetail::whereIn('id_jurnal', $jurnalIds)
                ->whereHas('akun', function($q) use ($tipeAkunLawan) {
                     if (is_array($tipeAkunLawan)) {
                        $q->whereIn('tipe_akun', $tipeAkunLawan);
                    } else {
                        $q->where('tipe_akun', $tipeAkunLawan);
                    }
                });

            return $isMasuk ? $query->sum('kredit') : $query->sum('debit');
        };

        $arusKasInvestasi = $getFlowSimple('Aset Tetap', true) - $getFlowSimple('Aset Tetap', false);
        $arusKasPendanaan = $getFlowSimple(['Ekuitas', 'Kewajiban Jangka Panjang'], true) - $getFlowSimple(['Ekuitas', 'Kewajiban Jangka Panjang'], false);

        $kenaikanKas = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;

        // Saldo Awal Kas
        $saldoAwal = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Kas & Bank');
            })
            ->whereHas('jurnal', function($q) use ($startDate) {
                $q->where('tanggal', '<', $startDate);
            })
            ->sum(DB::raw('debit - kredit'));

        $saldoAkhir = $saldoAwal + $kenaikanKas;

        return view('laporan.aruskas_tidak_langsung', compact(
            'perusahaan', 'startDate', 'endDate',
            'labaBersih', 'bebanPenyusutan', 'kenaikanPiutang', 'kenaikanPersediaan', 'kenaikanUtang',
            'arusKasOperasi', 'arusKasInvestasi', 'arusKasPendanaan',
            'kenaikanKas', 'saldoAwal', 'saldoAkhir'
        ));
    }

    public function perubahanEkuitas(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $perusahaan = DB::table('perusahaan')->find(1);

        // 1. Saldo Awal Ekuitas (Sebelum Start Date)
        // = (Total Kredit - Total Debit Akun Ekuitas) + (Total Pendapatan - Total Beban sebelum periode)
        
        $saldoAwalAkunEkuitas = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Ekuitas');
            })
            ->whereHas('jurnal', function($q) use ($startDate) {
                $q->where('tanggal', '<', $startDate);
            })
            ->sum(DB::raw('kredit - debit'));

        $labaDitahanAwal = $this->hitungLabaRugi(date('Y-m-d', strtotime($startDate . ' -1 day')));

        $saldoAwal = $saldoAwalAkunEkuitas + $labaDitahanAwal;

        // 2. Perubahan Selama Periode
        // Laba Bersih Periode
        $labaBersih = $this->hitungLabaRugiPeriode($startDate, $endDate);

        // Setoran Modal (Kredit ke Ekuitas selama periode)
        $setoranModal = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Ekuitas');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->sum('kredit');

        // Prive / Penarikan (Debit ke Ekuitas selama periode)
        $prive = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Ekuitas');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->sum('debit');

        // Saldo Akhir
        // Saldo Awal + Laba Bersih + Setoran - Prive
        // Note: $setoranModal dan $prive diambil dari mutasi akun Ekuitas.
        // Jika akun Ekuitas bertambah di Kredit (Setoran) dan berkurang di Debit (Prive).
        // Namun, hitungLabaRugiPeriode sudah menghitung revenue-expense.
        // Jadi kita hanya perlu mutasi di akun Ekuitas murni.
        
        $saldoAkhir = $saldoAwal + $labaBersih + $setoranModal - $prive;

        return view('laporan.perubahan_ekuitas', compact(
            'perusahaan', 'startDate', 'endDate',
            'saldoAwal', 'labaBersih', 'setoranModal', 'prive', 'saldoAkhir'
        ));
    }

    private function hitungLabaRugiPeriode($startDate, $endDate)
    {
        $pendapatan = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->sum(DB::raw('kredit - debit'));

        $beban = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->sum(DB::raw('debit - kredit'));

        return $pendapatan - $beban;
    }

    private function hitungLabaRugi($perTanggal)
    {
        $pendapatan = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($perTanggal) {
                $q->where('tanggal', '<=', $perTanggal);
            })
            ->sum(DB::raw('kredit - debit'));

        $beban = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($perTanggal) {
                $q->where('tanggal', '<=', $perTanggal);
            })
            ->sum(DB::raw('debit - kredit'));

        return $pendapatan - $beban;
    }

    public function persediaan()
    {
        $perusahaan = DB::table('perusahaan')->find(1);
        $persediaan = Persediaan::orderBy('nama_barang')->get();
        
        // Hitung total nilai persediaan
        $totalNilai = $persediaan->sum(function($item) {
            return $item->stok_saat_ini * $item->harga_beli;
        });

        return view('laporan.persediaan', compact('perusahaan', 'persediaan', 'totalNilai'));
    }
}
