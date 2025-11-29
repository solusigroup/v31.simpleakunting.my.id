@extends('layouts.app')

@section('title', 'Dashboard - Simple Akunting')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Dashboard</h2>
            <p class="text-muted">Welcome back, {{ Auth::user()->nama_user }}</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Piutang</div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($totalPiutang, 2, ',', '.') }}</h3>
                    <p class="card-text">Total tagihan ke pelanggan</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Utang</div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($totalUtang, 2, ',', '.') }}</h3>
                    <p class="card-text">Total kewajiban ke pemasok</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Nilai Persediaan</div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($nilaiPersediaan, 2, ',', '.') }}</h3>
                    <p class="card-text">Total aset persediaan saat ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    Tren Penjualan vs Pembelian (6 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [
                    {
                        label: 'Penjualan',
                        data: {!! $chartSales !!},
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Pembelian',
                        data: {!! $chartPurchases !!},
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Grafik Keuangan'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
