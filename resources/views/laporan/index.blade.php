@extends('layouts.app')

@section('title', 'Laporan Keuangan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Keuangan</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Neraca (Balance Sheet)</h5>
                    <p class="card-text">Laporan posisi keuangan perusahaan pada tanggal tertentu.</p>
                    <a href="{{ route('laporan.neraca') }}" class="btn btn-primary">Lihat Neraca</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Laba Rugi (Income Statement)</h5>
                    <p class="card-text">Laporan kinerja keuangan selama periode tertentu.</p>
                    <a href="{{ route('laporan.labarugi') }}" class="btn btn-primary">Lihat Laba Rugi</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Laporan Persediaan</h5>
                    <p class="card-text">Laporan detail persediaan barang.</p>
                    <a href="{{ route('laporan.persediaan') }}" class="btn btn-primary">Lihat Laporan Persediaan</a>
                </div>
            </div>
        </div>
    </div>
@endsection
