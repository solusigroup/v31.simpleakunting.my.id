@extends('layouts.app')

@section('title', 'Data Persediaan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Persediaan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('persediaan.create') }}" class="btn btn-sm btn-primary">
                Tambah Barang
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Kode</th>
                    <th scope="col">Barcode</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Stok</th>
                    <th scope="col">Harga Beli</th>
                    <th scope="col">Harga Jual</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($persediaan as $item)
                    <tr>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->barcode }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ number_format($item->stok_saat_ini, 2, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->harga_beli, 2, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->harga_jual, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('persediaan.edit', $item->id_barang) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('persediaan.destroy', $item->id_barang) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data persediaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
