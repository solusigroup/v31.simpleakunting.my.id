<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\Penjualan;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Summary Cards
        $totalPiutang = Pelanggan::sum('saldo_terkini_piutang');
        $totalUtang = Pemasok::sum('saldo_terkini_hutang');
        
        // Calculate inventory value: sum(stok_saat_ini * harga_beli)
        $nilaiPersediaan = Persediaan::select(DB::raw('SUM(stok_saat_ini * harga_beli) as total'))->value('total') ?? 0;

        // 2. Trend Chart Data (Last 6 Months)
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

        return view('dashboard.index', [
            'totalPiutang' => $totalPiutang,
            'totalUtang' => $totalUtang,
            'nilaiPersediaan' => $nilaiPersediaan,
            'chartLabels' => json_encode($labels),
            'chartSales' => json_encode($salesData),
            'chartPurchases' => json_encode($purchasesData),
        ]);
    }
}
