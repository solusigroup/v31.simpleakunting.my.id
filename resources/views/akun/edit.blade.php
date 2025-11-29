@extends('layouts.app')

@section('title', 'Edit Akun - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Akun</h1>
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
                    <form action="{{ route('akun.update', $akun->kode_akun) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="kode_akun" class="form-label">Kode Akun</label>
                            <input type="text" class="form-control" id="kode_akun" value="{{ $akun->kode_akun }}" disabled>
                            <div class="form-text">Kode akun tidak dapat diubah.</div>
                        </div>
                        <div class="mb-3">
                            <label for="nama_akun" class="form-label">Nama Akun</label>
                            <input type="text" class="form-control @error('nama_akun') is-invalid @enderror" id="nama_akun" name="nama_akun" value="{{ old('nama_akun', $akun->nama_akun) }}" required>
                            @error('nama_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tipe_akun" class="form-label">Tipe Akun</label>
                            <select class="form-select @error('tipe_akun') is-invalid @enderror" id="tipe_akun" name="tipe_akun" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach(['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap', 'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas', 'Pendapatan', 'HPP', 'Beban', 'Pendapatan Lainnya', 'Beban Lainnya'] as $tipe)
                                    <option value="{{ $tipe }}" {{ old('tipe_akun', $akun->tipe_akun) == $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
                                @endforeach
                            </select>
                            @error('tipe_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="saldo_normal" class="form-label">Saldo Normal</label>
                            <select class="form-select @error('saldo_normal') is-invalid @enderror" id="saldo_normal" name="saldo_normal" required>
                                <option value="Debit" {{ old('saldo_normal', $akun->saldo_normal) == 'Debit' ? 'selected' : '' }}>Debit</option>
                                <option value="Kredit" {{ old('saldo_normal', $akun->saldo_normal) == 'Kredit' ? 'selected' : '' }}>Kredit</option>
                            </select>
                            @error('saldo_normal')
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
