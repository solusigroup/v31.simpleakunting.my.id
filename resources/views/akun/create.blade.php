@extends('layouts.app')

@section('title', 'Tambah Akun - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Akun Baru</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('akun.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('akun.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="kode_akun" class="form-label">Kode Akun</label>
                            <input type="text" class="form-control @error('kode_akun') is-invalid @enderror" id="kode_akun" name="kode_akun" value="{{ old('kode_akun') }}" required>
                            @error('kode_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Contoh: 1-10001</div>
                        </div>
                        <div class="mb-3">
                            <label for="nama_akun" class="form-label">Nama Akun</label>
                            <input type="text" class="form-control @error('nama_akun') is-invalid @enderror" id="nama_akun" name="nama_akun" value="{{ old('nama_akun') }}" required>
                            @error('nama_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tipe_akun" class="form-label">Tipe Akun</label>
                            <select class="form-select @error('tipe_akun') is-invalid @enderror" id="tipe_akun" name="tipe_akun" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="Kas & Bank">Kas & Bank</option>
                                <option value="Piutang">Piutang</option>
                                <option value="Persediaan">Persediaan</option>
                                <option value="Aset Lancar Lainnya">Aset Lancar Lainnya</option>
                                <option value="Aset Tetap">Aset Tetap</option>
                                <option value="Utang Usaha">Utang Usaha</option>
                                <option value="Kewajiban Lancar Lainnya">Kewajiban Lancar Lainnya</option>
                                <option value="Kewajiban Jangka Panjang">Kewajiban Jangka Panjang</option>
                                <option value="Ekuitas">Ekuitas</option>
                                <option value="Pendapatan">Pendapatan</option>
                                <option value="HPP">HPP</option>
                                <option value="Beban">Beban</option>
                                <option value="Pendapatan Lainnya">Pendapatan Lainnya</option>
                                <option value="Beban Lainnya">Beban Lainnya</option>
                            </select>
                            @error('tipe_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="saldo_normal" class="form-label">Saldo Normal</label>
                            <select class="form-select @error('saldo_normal') is-invalid @enderror" id="saldo_normal" name="saldo_normal" required>
                                <option value="Debit">Debit</option>
                                <option value="Kredit">Kredit</option>
                            </select>
                            @error('saldo_normal')
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
