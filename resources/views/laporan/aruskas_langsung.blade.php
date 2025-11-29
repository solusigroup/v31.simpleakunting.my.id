@extends('layouts.app')

@section('title', 'Arus Kas (Metode Langsung) - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Arus Kas (Metode Langsung)</h1>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.aruskas_langsung') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('laporan.aruskas_langsung') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Header -->
    <div class="text-center mb-4">
        <h3>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan Belum Diset' }}</h3>
        <h4>Laporan Arus Kas (Metode Langsung)</h4>
        <p class="text-muted">
            Periode {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
        </p>
    </div>

    <!-- Report Content -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <tbody>
                        <!-- OPERASI -->
                        <tr class="fw-bold table-primary">
                            <td colspan="2">ARUS KAS DARI AKTIVITAS OPERASI</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Penerimaan dari Pelanggan</td>
                            <td class="text-end">Rp {{ number_format($terimaPelanggan, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Pembayaran kepada Pemasok & Beban</td>
                            <td class="text-end">(Rp {{ number_format($bayarPemasok, 2, ',', '.') }})</td>
                        </tr>
                        <tr class="fw-bold bg-light">
                            <td>Arus Kas Bersih dari Aktivitas Operasi</td>
                            <td class="text-end">Rp {{ number_format($arusKasOperasi, 2, ',', '.') }}</td>
                        </tr>

                        <!-- INVESTASI -->
                        <tr class="fw-bold table-warning mt-4">
                            <td colspan="2">ARUS KAS DARI AKTIVITAS INVESTASI</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Penjualan Aset Tetap</td>
                            <td class="text-end">Rp {{ number_format($jualAset, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Pembelian Aset Tetap</td>
                            <td class="text-end">(Rp {{ number_format($beliAset, 2, ',', '.') }})</td>
                        </tr>
                        <tr class="fw-bold bg-light">
                            <td>Arus Kas Bersih dari Aktivitas Investasi</td>
                            <td class="text-end">Rp {{ number_format($arusKasInvestasi, 2, ',', '.') }}</td>
                        </tr>

                        <!-- PENDANAAN -->
                        <tr class="fw-bold table-success mt-4">
                            <td colspan="2">ARUS KAS DARI AKTIVITAS PENDANAAN</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Penerimaan Modal / Utang Jangka Panjang</td>
                            <td class="text-end">Rp {{ number_format($terimaPendanaan, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Pembayaran Prive / Utang Jangka Panjang</td>
                            <td class="text-end">(Rp {{ number_format($bayarPendanaan, 2, ',', '.') }})</td>
                        </tr>
                        <tr class="fw-bold bg-light">
                            <td>Arus Kas Bersih dari Aktivitas Pendanaan</td>
                            <td class="text-end">Rp {{ number_format($arusKasPendanaan, 2, ',', '.') }}</td>
                        </tr>

                        <!-- SUMMARY -->
                        <tr class="fw-bold fs-5 mt-4">
                            <td>Kenaikan (Penurunan) Bersih Kas</td>
                            <td class="text-end">Rp {{ number_format($kenaikanKas, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Saldo Kas Awal Periode</td>
                            <td class="text-end">Rp {{ number_format($saldoAwal, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="fw-bold table-dark">
                            <td>Saldo Kas Akhir Periode</td>
                            <td class="text-end">Rp {{ number_format($saldoAkhir, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures -->
            <div class="row mt-5 text-center">
                <div class="col-md-4 offset-md-2">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">{{ $perusahaan->nama_direktur ?? '(....................)' }}</p>
                    <p>Direktur</p>
                </div>
                <div class="col-md-4">
                    <p>Dibuat Oleh,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">{{ $perusahaan->nama_akuntan ?? '(....................)' }}</p>
                    <p>Akuntan</p>
                </div>
            </div>
        </div>
    </div>
@endsection
