<!-- Ringkasan Kartu -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="fs-1 text-primary me-3"><i class="bi bi-person-check-fill"></i></div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Total Piutang</h6>
                        <h4 class="card-title">Rp <?php echo number_format($data['summary']['total_piutang'] ?? 0, 2, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="fs-1 text-danger me-3"><i class="bi bi-truck"></i></div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Total Utang</h6>
                        <h4 class="card-title">Rp <?php echo number_format($data['summary']['total_utang'] ?? 0, 2, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="fs-1 text-success me-3"><i class="bi bi-box-seam-fill"></i></div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Nilai Persediaan</h6>
                        <h4 class="card-title">Rp <?php echo number_format($data['summary']['nilai_persediaan'] ?? 0, 2, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                Tren Penjualan vs Pembelian (6 Bulan Terakhir)
            </div>
            <div class="card-body">
                <canvas id="salesPurchasesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Memuat library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grafik Tren Penjualan vs Pembelian
    const ctx = document.getElementById('salesPurchasesChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $data['chart_trend']['labels']; ?>,
            datasets: [{
                label: 'Penjualan',
                data: <?php echo $data['chart_trend']['sales']; ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Pembelian',
                data: <?php echo $data['chart_trend']['purchases']; ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
