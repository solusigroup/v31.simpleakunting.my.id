@extends('layouts.app')

@section('title', 'Daftar Akun - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Akun (Chart of Accounts)</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('akun.create') }}" class="btn btn-sm btn-primary">
                Tambah Akun
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Kode Akun</th>
                    <th scope="col">Nama Akun</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Saldo Normal</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($akun as $a)
                    <tr>
                        <td>{{ $a->kode_akun }}</td>
                        <td>{{ $a->nama_akun }}</td>
                        <td>{{ $a->tipe_akun }}</td>
                        <td>
                            <span class="badge bg-{{ $a->saldo_normal == 'Debit' ? 'success' : 'danger' }}">
                                {{ $a->saldo_normal }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('akun.edit', $a->kode_akun) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('akun.destroy', $a->kode_akun) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data akun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
