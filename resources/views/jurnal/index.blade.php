@extends('layouts.app')

@section('title', 'Jurnal Umum - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Jurnal Umum</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('jurnal.create') }}" class="btn btn-sm btn-primary">
                Buat Jurnal Manual
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th>
                    <th scope="col">No Transaksi</th>
                    <th scope="col">Deskripsi</th>
                    <th scope="col">Sumber</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jurnal as $j)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $j->no_transaksi }}</td>
                        <td>{{ Str::limit($j->deskripsi, 50) }}</td>
                        <td>
                            <span class="badge bg-{{ $j->sumber_jurnal == 'Manual' ? 'secondary' : 'info' }}">
                                {{ $j->sumber_jurnal }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('jurnal.show', $j->id_jurnal) }}" class="btn btn-sm btn-info text-white">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data jurnal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $jurnal->links() }}
        </div>
    </div>
@endsection
