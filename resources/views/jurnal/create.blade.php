@extends('layouts.app')

@section('title', 'Buat Jurnal Umum - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Jurnal Manual</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('jurnal.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('jurnal.store') }}" method="POST" id="formJurnal">
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
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <input type="text" class="form-control" id="deskripsi" name="deskripsi" required>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Detail Jurnal</div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40%">Akun</th>
                            <th width="25%">Debit</th>
                            <th width="25%">Kredit</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_jurnal">
                        <!-- Rows via JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end fw-bold">Total</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_debit_display" readonly>
                                <input type="hidden" id="total_debit" value="0">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_kredit_display" readonly>
                                <input type="hidden" id="total_kredit" value="0">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">+ Tambah Baris</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-3 mb-5">
            <div id="balance_alert" class="alert alert-danger" style="display: none;">
                Jurnal tidak seimbang (Balance)! Selisih: <span id="selisih_display">0</span>
            </div>
            <button type="submit" class="btn btn-lg btn-primary w-100" id="btnSubmit" disabled>Simpan Jurnal</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let akunData = {!! json_encode($akun) !!};
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
                    <input type="number" class="form-control form-control-sm input-debit" name="details[${rowCount}][debit]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm input-kredit" name="details[${rowCount}][kredit]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${rowCount})">X</button>
                </td>
            </tr>
        `;
        document.getElementById('container_jurnal').insertAdjacentHTML('beforeend', html);
        rowCount++;
    }

    function hapusBaris(id) {
        document.getElementById(`row_${id}`).remove();
        hitungTotal();
    }

    function hitungTotal() {
        let totalDebit = 0;
        let totalKredit = 0;

        document.querySelectorAll('.input-debit').forEach(input => totalDebit += parseFloat(input.value) || 0);
        document.querySelectorAll('.input-kredit').forEach(input => totalKredit += parseFloat(input.value) || 0);

        document.getElementById('total_debit').value = totalDebit;
        document.getElementById('total_kredit').value = totalKredit;
        
        document.getElementById('total_debit_display').value = formatRupiah(totalDebit);
        document.getElementById('total_kredit_display').value = formatRupiah(totalKredit);

        let balance = Math.abs(totalDebit - totalKredit) < 0.01; // Tolerance for float
        let btn = document.getElementById('btnSubmit');
        let alert = document.getElementById('balance_alert');

        if (balance && totalDebit > 0) {
            btn.removeAttribute('disabled');
            alert.style.display = 'none';
        } else {
            btn.setAttribute('disabled', 'disabled');
            alert.style.display = 'block';
            document.getElementById('selisih_display').innerText = formatRupiah(Math.abs(totalDebit - totalKredit));
        }
    }

    // Init 2 rows
    tambahBaris();
    tambahBaris();
</script>
@endpush
