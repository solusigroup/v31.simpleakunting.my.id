@extends('layouts.app')

@section('title', 'Laporan Persediaan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Persediaan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <div class="text-center">
                <h4 class="mb-0 fw-bold">{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
                <h5 class="mb-0">Laporan Persediaan Barang</h5>
                <p class="text-muted mb-0">Per Tanggal: {{ date('d F Y') }}</p>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Barang</th>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Harga Beli (Rata-rata)</th>
                            <th class="text-end">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($persediaan as $item)
                            <tr>
                                <td>{{ $item->kode_barang }}</td>
                                <td>{{ $item->barcode }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td class="text-end">{{ number_format($item->stok_saat_ini, 2, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_beli, 2, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->stok_saat_ini * $item->harga_beli, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data persediaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Grand Total Nilai Persediaan</td>
                            <td class="text-end">Rp {{ number_format($totalNilai, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
