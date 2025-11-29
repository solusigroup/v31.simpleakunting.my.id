@extends('layouts.app')

@section('title', 'Pengeluaran Kas - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Pengeluaran Kas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('pembayaran.create') }}" class="btn btn-sm btn-primary">
                Buat Pengeluaran Baru
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th>
                    <th scope="col">No Transaksi</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Total Bayar</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembayaran as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $p->no_transaksi }}</td>
                        <td>{{ Str::limit($p->deskripsi, 50) }}</td>
                        <td>Rp {{ number_format($p->details->where('kredit', '>', 0)->sum('kredit'), 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('pembayaran.show', $p->id_jurnal) }}" class="btn btn-sm btn-info text-white">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data pengeluaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $pembayaran->links() }}
        </div>
    </div>
@endsection
