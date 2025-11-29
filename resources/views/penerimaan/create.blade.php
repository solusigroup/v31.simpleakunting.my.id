@extends('layouts.app')

@section('title', 'Buat Penerimaan Kas - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Penerimaan Baru</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('penerimaan.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('penerimaan.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="no_transaksi" class="form-label">No Transaksi</label>
                <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" value="{{ $noTransaksi }}" readonly>
            </div>
            <div class="col-md-4">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label for="akun_kas" class="form-label">Masuk ke Akun (Debit)</label>
                <select class="form-select" id="akun_kas" name="akun_kas" required>
                    <option value="">-- Pilih Kas/Bank --</option>
                    @foreach($akunKas as $a)
                        <option value="{{ $a->kode_akun }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="id_pelanggan" class="form-label">Diterima Dari (Pelanggan) - Opsional</label>
                <select class="form-select" id="id_pelanggan" name="id_pelanggan">
                    <option value="">-- Umum --</option>
                    @foreach($pelanggan as $p)
                        <option value="{{ $p->id_pelanggan }}">{{ $p->nama_pelanggan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Rincian Penerimaan (Kredit)</div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60%">Akun Pendapatan/Piutang</th>
                            <th width="30%">Jumlah</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_detail">
                        <!-- Rows via JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end fw-bold">Total Penerimaan</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_display" readonly>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">+ Tambah Baris</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-3 mb-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Simpan Transaksi</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let akunData = {!! json_encode($akunPendapatan) !!};
    let rowCount = 0;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        let html = `
            <tr id="row_${rowCount}">
                <td>
                    <select class="form-select form-select-sm" name="details[${rowCount}][kode_akun]" required>
                        <option value="">-- Pilih Akun --</option>
                        ${akunData.map(a => `<option value="${a.kode_akun}">${a.kode_akun} - ${a.nama_akun}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm input-jumlah" name="details[${rowCount}][jumlah]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${rowCount})">X</button>
                </td>
            </tr>
        `;
        document.getElementById('container_detail').insertAdjacentHTML('beforeend', html);
        rowCount++;
    }

    function hapusBaris(id) {
        document.getElementById(`row_${id}`).remove();
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.input-jumlah').forEach(input => total += parseFloat(input.value) || 0);
        document.getElementById('total_display').value = formatRupiah(total);
    }

    tambahBaris();
</script>
@endpush
