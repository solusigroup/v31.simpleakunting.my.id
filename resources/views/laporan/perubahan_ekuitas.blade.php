@extends('layouts.app')

@section('title', 'Laporan Perubahan Ekuitas - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Perubahan Ekuitas</h1>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.perubahan_ekuitas') }}" method="GET" class="row g-3 align-items-end">
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
                    <a href="{{ route('laporan.perubahan_ekuitas') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Header -->
    <div class="text-center mb-4">
        <h3>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan Belum Diset' }}</h3>
        <h4>Laporan Perubahan Ekuitas</h4>
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
                        <tr class="fw-bold fs-5">
                            <td>Saldo Ekuitas Awal</td>
                            <td class="text-end">Rp {{ number_format($saldoAwal, 2, ',', '.') }}</td>
                        </tr>
                        
                        <tr class="fw-bold mt-4"><td colspan="2" class="table-light">Penambahan:</td></tr>
                        <tr>
                            <td class="ps-4">Laba Bersih Periode Berjalan</td>
                            <td class="text-end">Rp {{ number_format($labaBersih, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Setoran Modal (Investasi)</td>
                            <td class="text-end">Rp {{ number_format($setoranModal, 2, ',', '.') }}</td>
                        </tr>

                        <tr class="fw-bold mt-4"><td colspan="2" class="table-light">Pengurangan:</td></tr>
                        <tr>
                            <td class="ps-4">Prive (Penarikan Pemilik)</td>
                            <td class="text-end">(Rp {{ number_format($prive, 2, ',', '.') }})</td>
                        </tr>

                        <tr class="fw-bold fs-4 table-success">
                            <td>Saldo Ekuitas Akhir</td>
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
