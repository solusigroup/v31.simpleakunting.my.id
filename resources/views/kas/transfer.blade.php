@extends('layouts.app')

@section('title', 'Transfer Kas - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Transfer Dana Antar Kas/Bank</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kas.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('kas.storeTransfer') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="no_transaksi" class="form-label">No Transaksi</label>
                            <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" value="{{ $noTransaksi }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="dari_akun" class="form-label">Dari Akun (Sumber)</label>
                            <select class="form-select" id="dari_akun" name="dari_akun" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->kode_akun }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ke_akun" class="form-label">Ke Akun (Tujuan)</label>
                            <select class="form-select" id="ke_akun" name="ke_akun" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->kode_akun }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Transfer</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Proses Transfer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
