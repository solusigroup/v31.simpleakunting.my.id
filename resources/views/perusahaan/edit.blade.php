@extends('layouts.app')

@section('title', 'Profil Perusahaan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Profil Perusahaan</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('perusahaan.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan ?? '') }}" required>
                            @error('nama_perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $perusahaan->alamat ?? '') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $perusahaan->telepon ?? '') }}">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $perusahaan->email ?? '') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jenis_usaha" class="form-label">Jenis Usaha / Tipe COA <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_usaha') is-invalid @enderror" id="jenis_usaha" name="jenis_usaha" required>
                                <option value="dagang" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? 'dagang') == 'dagang' ? 'selected' : '' }}>
                                    Usaha Dagang (COA Dagang)
                                </option>
                                <option value="simpan_pinjam" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'simpan_pinjam' ? 'selected' : '' }}>
                                    Koperasi Simpan Pinjam (COA Simpan Pinjam)
                                </option>
                                <option value="serba_usaha" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'serba_usaha' ? 'selected' : '' }}>
                                    Koperasi Serba Usaha (COA Dagang + Simpan Pinjam)
                                </option>
                                <option value="jasa" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'jasa' ? 'selected' : '' }}>
                                    Usaha Jasa (Tanpa HPP)
                                </option>
                            </select>
                            @error('jenis_usaha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Pilih jenis usaha untuk menentukan Chart of Accounts (COA) yang digunakan.
                            </small>
                        </div>

                        <h5 class="mt-4">Pejabat Penandatangan</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_direktur" class="form-label">Nama Direktur</label>
                                <input type="text" class="form-control" id="nama_direktur" name="nama_direktur" value="{{ $perusahaan->nama_direktur ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_akuntan" class="form-label">Nama Akuntan / Bag. Keuangan</label>
                                <input type="text" class="form-control" id="nama_akuntan" name="nama_akuntan" value="{{ $perusahaan->nama_akuntan ?? '' }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
