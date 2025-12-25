<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\JenisSimpanan;
use App\Models\JenisPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Summary Cards - Existing
        $totalPiutang = Pelanggan::sum('saldo_terkini_piutang');
        $totalUtang = Pemasok::sum('saldo_terkini_hutang');
        
        // Calculate inventory value: sum(stok_saat_ini * harga_beli)
        $nilaiPersediaan = Persediaan::select(DB::raw('SUM(stok_saat_ini * harga_beli) as total'))->value('total') ?? 0;

        // 2. Simpanan Summary by Type
        $simpananByType = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.id_jenis_simpanan', '=', 'jenis_simpanan.id_jenis_simpanan')
            ->select(
                'jenis_simpanan.nama_simpanan',
                'jenis_simpanan.tipe',
                DB::raw("SUM(CASE WHEN simpanan.jenis_transaksi = 'setor' THEN simpanan.jumlah ELSE -simpanan.jumlah END) as saldo")
            )
            ->groupBy('jenis_simpanan.id_jenis_simpanan', 'jenis_simpanan.nama_simpanan', 'jenis_simpanan.tipe')
            ->get();

        $totalSimpanan = $simpananByType->sum('saldo');

        // 3. Pinjaman Summary by Type (Active loans only)
        $pinjamanByType = DB::table('pinjaman')
            ->join('jenis_pinjaman', 'pinjaman.id_jenis_pinjaman', '=', 'jenis_pinjaman.id_jenis_pinjaman')
            ->select(
                'jenis_pinjaman.nama_pinjaman',
                'jenis_pinjaman.kategori',
                DB::raw('SUM(pinjaman.sisa_pokok) as sisa_pokok'),
                DB::raw('COUNT(pinjaman.id_pinjaman) as jumlah_aktif')
            )
            ->whereIn('pinjaman.status', ['active', 'disbursed'])
            ->groupBy('jenis_pinjaman.id_jenis_pinjaman', 'jenis_pinjaman.nama_pinjaman', 'jenis_pinjaman.kategori')
            ->get();

        $totalPinjamanAktif = $pinjamanByType->sum('sisa_pokok');

        // 4. Trend Chart Data - Penjualan vs Pembelian (Last 6 Months)
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        $penjualan = Penjualan::select(
                DB::raw("DATE_FORMAT(tanggal_faktur, '%Y-%m') as periode"),
                DB::raw('SUM(total) as total')
            )
            ->where('tanggal_faktur', '>=', $sixMonthsAgo)
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        $pembelian = Pembelian::select(
                DB::raw("DATE_FORMAT(tanggal_faktur, '%Y-%m') as periode"),
                DB::raw('SUM(total) as total')
            )
            ->where('tanggal_faktur', '>=', $sixMonthsAgo)
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        // Merge and format for Chart.js
        $labels = [];
        $salesData = [];
        $purchasesData = [];
        
        $currentDate = $sixMonthsAgo->copy();
        $now = now()->endOfMonth();

        while ($currentDate <= $now) {
            $periode = $currentDate->format('Y-m');
            $label = $currentDate->format('M Y');
            
            $labels[] = $label;
            $salesData[] = $penjualan[$periode]->total ?? 0;
            $purchasesData[] = $pembelian[$periode]->total ?? 0;
            
            $currentDate->addMonth();
        }

        // 5. Pendapatan vs Biaya Chart (From jurnal_detail with akun klasifikasi)
        $pendapatanBiaya = DB::table('jurnal_detail')
            ->join('jurnal', 'jurnal_detail.id_jurnal', '=', 'jurnal.id_jurnal')
            ->join('akun', 'jurnal_detail.kode_akun', '=', 'akun.kode_akun')
            ->select(
                DB::raw("DATE_FORMAT(jurnal.tanggal, '%Y-%m') as periode"),
                'akun.klasifikasi',
                DB::raw('SUM(jurnal_detail.kredit - jurnal_detail.debit) as saldo')
            )
            ->where('jurnal.tanggal', '>=', $sixMonthsAgo)
            ->whereIn('akun.klasifikasi', ['4', '5']) // 4=Pendapatan, 5=Biaya
            ->groupBy('periode', 'akun.klasifikasi')
            ->get();

        $pendapatanData = [];
        $biayaData = [];

        $currentDate = $sixMonthsAgo->copy();
        while ($currentDate <= $now) {
            $periode = $currentDate->format('Y-m');
            
            $pendapatan = $pendapatanBiaya->where('periode', $periode)->where('klasifikasi', '4')->first();
            $biaya = $pendapatanBiaya->where('periode', $periode)->where('klasifikasi', '5')->first();
            
            // Pendapatan is normally credit (positive), Biaya is normally debit (negative in our calc, so negate)
            $pendapatanData[] = abs($pendapatan->saldo ?? 0);
            $biayaData[] = abs($biaya->saldo ?? 0);
            
            $currentDate->addMonth();
        }

        return view('dashboard.index', [
            'totalPiutang' => $totalPiutang,
            'totalUtang' => $totalUtang,
            'nilaiPersediaan' => $nilaiPersediaan,
            'simpananByType' => $simpananByType,
            'totalSimpanan' => $totalSimpanan,
            'pinjamanByType' => $pinjamanByType,
            'totalPinjamanAktif' => $totalPinjamanAktif,
            'chartLabels' => json_encode($labels),
            'chartSales' => json_encode($salesData),
            'chartPurchases' => json_encode($purchasesData),
            'chartPendapatan' => json_encode($pendapatanData),
            'chartBiaya' => json_encode($biayaData),
        ]);
    }
}

