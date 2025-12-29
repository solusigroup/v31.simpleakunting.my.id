@extends('layouts.app')

@section('title', 'Edit Barang - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Barang</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('persediaan.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('persediaan.update', $persediaan->id_barang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="kode_barang" class="form-label">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $persediaan->kode_barang) }}" required>
                            @error('kode_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode', $persediaan->barcode) }}">
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $persediaan->nama_barang) }}" required>
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan', $persediaan->satuan) }}" required>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stok_awal" class="form-label">Stok Awal</label>
                                <input type="number" class="form-control @error('stok_awal') is-invalid @enderror" id="stok_awal" name="stok_awal" value="{{ old('stok_awal', $persediaan->stok_awal) }}" min="0" step="0.01" required>
                                <div class="form-text text-warning">Perhatian: Mengubah stok awal akan mempengaruhi stok saat ini.</div>
                                @error('stok_awal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Saat Ini (Read Only)</label>
                                <input type="text" class="form-control" value="{{ number_format($persediaan->stok_saat_ini, 2, ',', '.') }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga_beli" class="form-label">Harga Beli</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga_beli') is-invalid @enderror" id="harga_beli" name="harga_beli" value="{{ old('harga_beli', $persediaan->harga_beli) }}" min="0" step="0.01" required>
                                </div>
                                @error('harga_beli')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="harga_jual" class="form-label">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $persediaan->harga_jual) }}" min="0" step="0.01" required>
                                </div>
                                @error('harga_jual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="mt-4">Pengaturan Akun (Opsional)</h5>
                        @if(($jenisUsaha ?? 'dagang') !== 'jasa')
                        <div class="mb-3">
                            <label for="akun_persediaan" class="form-label">Akun Persediaan</label>
                            <select class="form-select @error('akun_persediaan') is-invalid @enderror" id="akun_persediaan" name="akun_persediaan">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akun as $a)
                                    <option value="{{ $a->kode_akun }}" {{ old('akun_persediaan', $persediaan->akun_persediaan) == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                            @error('akun_persediaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="akun_hpp" class="form-label">Akun HPP</label>
                            <select class="form-select @error('akun_hpp') is-invalid @enderror" id="akun_hpp" name="akun_hpp">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akun as $a)
                                    <option value="{{ $a->kode_akun }}" {{ old('akun_hpp', $persediaan->akun_hpp) == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                            @error('akun_hpp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="akun_penjualan" class="form-label">Akun Penjualan</label>
                            <select class="form-select @error('akun_penjualan') is-invalid @enderror" id="akun_penjualan" name="akun_penjualan">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akun as $a)
                                    <option value="{{ $a->kode_akun }}" {{ old('akun_penjualan', $persediaan->akun_penjualan) == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                            @error('akun_penjualan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
