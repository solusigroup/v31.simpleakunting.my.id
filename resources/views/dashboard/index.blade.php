@extends('layouts.app')

@section('title', 'Dashboard - Simple Akunting')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Dashboard</h2>
            <p class="text-muted">Welcome back, {{ Auth::user()->nama_user }}</p>
        </div>
    </div>

    <!-- Summary Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Piutang</div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h3>
                    <p class="card-text small">Total tagihan ke pelanggan</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Utang</div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($totalUtang, 0, ',', '.') }}</h3>
                    <p class="card-text small">Total kewajiban ke pemasok</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Nilai Persediaan</div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($nilaiPersediaan, 0, ',', '.') }}</h3>
                    <p class="card-text small">Total aset persediaan saat ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Row 2 - Koperasi -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>🏦 Total Simpanan</span>
                    <span class="badge bg-light text-primary">{{ $simpananByType->count() }} Jenis</span>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h3>
                    <p class="card-text small">Total dana simpanan anggota</p>
                </div>
            </div>
            @if($simpananByType->count() > 0)
            <div class="card mb-3">
                <div class="card-header">Rincian Simpanan per Jenis</div>
                <ul class="list-group list-group-flush">
                    @foreach($simpananByType as $simpanan)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $simpanan->nama_simpanan }}
                            <small class="text-muted">({{ ucfirst($simpanan->tipe) }})</small>
                        </span>
                        <span class="fw-bold {{ $simpanan->saldo >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($simpanan->saldo, 0, ',', '.') }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>💰 Total Pinjaman Aktif</span>
                    <span class="badge bg-light text-warning">{{ $pinjamanByType->sum('jumlah_aktif') }} Pinjaman</span>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Rp {{ number_format($totalPinjamanAktif, 0, ',', '.') }}</h3>
                    <p class="card-text small">Total sisa pokok pinjaman aktif</p>
                </div>
            </div>
            @if($pinjamanByType->count() > 0)
            <div class="card mb-3">
                <div class="card-header">Rincian Pinjaman per Jenis</div>
                <ul class="list-group list-group-flush">
                    @foreach($pinjamanByType as $pinjaman)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $pinjaman->nama_pinjaman }}
                            <small class="text-muted">({{ $pinjaman->jumlah_aktif }} aktif)</small>
                        </span>
                        <span class="fw-bold text-warning">
                            Rp {{ number_format($pinjaman->sisa_pokok, 0, ',', '.') }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- Trend Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    📈 Tren Penjualan vs Pembelian (6 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    💵 Pendapatan vs Biaya (6 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <canvas id="pendapatanBiayaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart 1: Penjualan vs Pembelian
        const ctx1 = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx1, {
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
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });

        // Chart 2: Pendapatan vs Biaya
        const ctx2 = document.getElementById('pendapatanBiayaChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: {!! $chartPendapatan !!},
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Biaya',
                        data: {!! $chartBiaya !!},
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush

