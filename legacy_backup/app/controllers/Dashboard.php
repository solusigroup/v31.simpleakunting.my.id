<?php

class Dashboard extends Controller {
    public function index() {
        $data['judul'] = 'Dashboard';
        
        $dashboardModel = $this->model('Dashboard');
        
        // Ambil data ringkasan untuk kartu
        $data['summary'] = $dashboardModel->getSummary();

        // Ambil dan format data untuk grafik tren penjualan vs pembelian
        $trendData = $dashboardModel->getSalesPurchasesTrend();
        $labels = [];
        $salesData = [];
        $purchasesData = [];
        foreach($trendData as $row) {
            $labels[] = date('M Y', strtotime($row['periode'] . '-01'));
            $salesData[] = $row['total_penjualan'];
            $purchasesData[] = $row['total_pembelian'];
        }
        $data['chart_trend'] = [
            'labels' => json_encode($labels),
            'sales' => json_encode($salesData),
            'purchases' => json_encode($purchasesData)
        ];

        $this->view('templates/header', $data);
        $this->view('dashboard/index', $data);
        $this->view('templates/footer');
    }
}
