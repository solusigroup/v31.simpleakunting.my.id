@extends('layouts.app')

@section('title', 'Daftar Penjualan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Penjualan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('penjualan.create') }}" class="btn btn-sm btn-primary">
                Buat Faktur Baru
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th>
                    <th scope="col">No Faktur</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col">Total</th>
                    <th scope="col">Pembayaran</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualan as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_faktur)->format('d/m/Y') }}</td>
                        <td>{{ $p->no_faktur }}</td>
                        <td>{{ $p->pelanggan->nama_pelanggan ?? '-' }}</td>
                        <td>Rp {{ number_format($p->total, 2, ',', '.') }}</td>
                        <td>{{ $p->metode_pembayaran }}</td>
                        <td>
                            <span class="badge bg-{{ $p->status_pembayaran == 'Lunas' ? 'success' : 'warning' }}">
                                {{ $p->status_pembayaran }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('penjualan.show', $p->id_penjualan) }}" class="btn btn-sm btn-info text-white">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada transaksi penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
