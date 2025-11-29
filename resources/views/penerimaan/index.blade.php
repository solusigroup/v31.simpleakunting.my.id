@extends('layouts.app')

@section('title', 'Penerimaan Kas - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Penerimaan Kas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('penerimaan.create') }}" class="btn btn-sm btn-primary">
                Buat Penerimaan Baru
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
                    <th scope="col">Total Terima</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penerimaan as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $p->no_transaksi }}</td>
                        <td>{{ Str::limit($p->deskripsi, 50) }}</td>
                        <td>Rp {{ number_format($p->details->where('debit', '>', 0)->sum('debit'), 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('penerimaan.show', $p->id_jurnal) }}" class="btn btn-sm btn-info text-white">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data penerimaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $penerimaan->links() }}
        </div>
    </div>
@endsection
