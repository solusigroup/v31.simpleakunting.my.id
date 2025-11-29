@extends('layouts.app')

@section('title', 'Buku Kas & Bank - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buku Kas & Bank</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kas.transfer') }}" class="btn btn-sm btn-primary">
                Transfer Dana
            </a>
        </div>
    </div>

    <div class="row">
        @foreach($akunKas as $akun)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $akun->nama_akun }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $akun->kode_akun }}</h6>
                        <h2 class="card-text text-primary">
                            Rp {{ number_format($akun->saldo_terkini, 2, ',', '.') }}
                        </h2>
                        <a href="{{ route('bukubesar.index', ['kode_akun' => $akun->kode_akun]) }}" class="card-link">Lihat Mutasi</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
