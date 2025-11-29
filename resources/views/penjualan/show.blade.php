@extends('layouts.app')

@section('title', 'Detail Penjualan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Faktur #{{ $penjualan->no_faktur }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Informasi Transaksi</span>
                    <span class="badge bg-{{ $penjualan->status_pembayaran == 'Lunas' ? 'success' : 'warning' }}">{{ $penjualan->status_pembayaran }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Tanggal</strong></td>
                                    <td>: {{ \Carbon\Carbon::parse($penjualan->tanggal_faktur)->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pelanggan</strong></td>
                                    <td>: {{ $penjualan->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Metode</strong></td>
                                    <td>: {{ $penjualan->metode_pembayaran }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4>Total Tagihan</h4>
                            <h2 class="text-primary">Rp {{ number_format($penjualan->total, 2, ',', '.') }}</h2>
                        </div>
                    </div>

                    <h5 class="mt-4">Rincian Barang</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan->details as $detail)
                                    <tr>
                                        <td>{{ $detail->barang->kode_barang ?? '-' }}</td>
                                        <td>{{ $detail->barang->nama_barang ?? 'Item Terhapus' }}</td>
                                        <td class="text-center">{{ $detail->kuantitas }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->harga, 2, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($penjualan->total, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
