@extends('layouts.app')

@section('title', 'Tambah Pemasok - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Pemasok</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('pemasok.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pemasok.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_pemasok" class="form-label">Nama Pemasok <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_pemasok') is-invalid @enderror" id="nama_pemasok" name="nama_pemasok" value="{{ old('nama_pemasok') }}" required>
                            @error('nama_pemasok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon') }}">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="saldo_awal_hutang" class="form-label">Saldo Awal Hutang</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('saldo_awal_hutang') is-invalid @enderror" id="saldo_awal_hutang" name="saldo_awal_hutang" value="{{ old('saldo_awal_hutang', 0) }}" min="0" step="0.01" required>
                            </div>
                            <div class="form-text">Masukkan 0 jika tidak ada saldo awal.</div>
                            @error('saldo_awal_hutang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
