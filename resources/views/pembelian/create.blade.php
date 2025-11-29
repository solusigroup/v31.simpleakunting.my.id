@extends('layouts.app')

@section('title', 'Buat Faktur Pembelian - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Faktur Pembelian</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('pembelian.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Informasi Faktur -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Info Faktur</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="no_faktur" class="form-label">No Faktur</label>
                            <input type="text" class="form-control" id="no_faktur" name="no_faktur" value="{{ $noFaktur }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_faktur" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal_faktur" name="tanggal_faktur" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_pemasok" class="form-label">Pemasok</label>
                            <select class="form-select" id="id_pemasok" name="id_pemasok" required>
                                <option value="">-- Pilih Pemasok --</option>
                                @foreach($pemasok as $p)
                                    <option value="{{ $p->id_pemasok }}">{{ $p->nama_pemasok }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Pembayaran -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Pembayaran</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">Metode</label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required onchange="toggleAkunKas()">
                                <option value="Tunai">Tunai</option>
                                <option value="Kredit">Kredit</option>
                            </select>
                        </div>
                        <div class="mb-3" id="div_akun_kas">
                            <label for="akun_kas_bank" class="form-label">Akun Kas/Bank</label>
                            <select class="form-select" id="akun_kas_bank" name="akun_kas_bank">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->kode_akun }}">{{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total -->
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-center">
                        <h3>Total Faktur</h3>
                        <h1 class="display-4 fw-bold text-primary" id="display_total">Rp 0</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Detail Barang</span>
                <div class="input-group w-50">
                    <span class="input-group-text"><i class="bi bi-upc-scan"></i> Scan Barcode</span>
                    <input type="text" class="form-control" id="scan_barcode" placeholder="Scan barcode atau ketik kode barang disini..." autofocus>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="tabel_barang">
                    <thead class="table-light">
                        <tr>
                            <th width="40%">Barang</th>
                            <th width="15%">Harga Beli</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Subtotal</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_barang">
                        <!-- Rows will be added here via JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
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
    let barangData = {!! json_encode($barang) !!};
    let rowCount = 0;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        let html = `
            <tr id="row_${rowCount}">
                <td>
                    <select class="form-select form-select-sm" name="details[${rowCount}][id_barang]" onchange="updateHarga(${rowCount})" required>
                        <option value="">-- Pilih Barang --</option>
                        ${barangData.map(b => `<option value="${b.id_barang}" data-harga="${b.harga_beli}" data-barcode="${b.barcode || ''}" data-kode="${b.kode_barang}">${b.kode_barang} - ${b.nama_barang}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="details[${rowCount}][harga_beli]" id="harga_${rowCount}" onchange="hitungSubtotal(${rowCount})" onkeyup="hitungSubtotal(${rowCount})" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="details[${rowCount}][kuantitas]" id="qty_${rowCount}" min="1" value="1" onchange="hitungSubtotal(${rowCount})" onkeyup="hitungSubtotal(${rowCount})" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="subtotal_display_${rowCount}" readonly>
                    <input type="hidden" class="subtotal-input" id="subtotal_${rowCount}" value="0">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${rowCount})">X</button>
                </td>
            </tr>
        `;
        document.getElementById('container_barang').insertAdjacentHTML('beforeend', html);
        rowCount++;
    }

    // Barcode Scanner Logic
    document.getElementById('scan_barcode').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let code = this.value.trim();
            if (code) {
                processBarcode(code);
                this.value = '';
            }
        }
    });

    function processBarcode(code) {
        // Cari barang berdasarkan barcode atau kode barang
        let barang = barangData.find(b => (b.barcode === code) || (b.kode_barang === code));
        
        if (barang) {
            // Cek apakah barang sudah ada di list
            let existingRow = -1;
            for (let i = 0; i < rowCount; i++) {
                let select = document.querySelector(`select[name="details[${i}][id_barang]"]`);
                if (select && select.value == barang.id_barang) {
                    existingRow = i;
                    break;
                }
            }

            if (existingRow !== -1) {
                // Jika sudah ada, tambah qty
                let qtyInput = document.getElementById(`qty_${existingRow}`);
                qtyInput.value = parseInt(qtyInput.value) + 1;
                hitungSubtotal(existingRow);
            } else {
                // Jika belum ada, cari baris kosong atau tambah baru
                let emptyRow = -1;
                for (let i = 0; i < rowCount; i++) {
                    let select = document.querySelector(`select[name="details[${i}][id_barang]"]`);
                    if (select && select.value === "") {
                        emptyRow = i;
                        break;
                    }
                }

                if (emptyRow === -1) {
                    tambahBaris();
                    emptyRow = rowCount - 1;
                }

                // Set value
                let select = document.querySelector(`select[name="details[${emptyRow}][id_barang]"]`);
                select.value = barang.id_barang;
                updateHarga(emptyRow);
            }
        } else {
            alert('Barang tidak ditemukan!');
        }
    }

    function updateHarga(id) {
        let select = document.querySelector(`select[name="details[${id}][id_barang]"]`);
        let harga = select.options[select.selectedIndex].getAttribute('data-harga') || 0;
        document.getElementById(`harga_${id}`).value = harga;
        hitungSubtotal(id);
    }

    function hitungSubtotal(id) {
        let harga = parseFloat(document.getElementById(`harga_${id}`).value) || 0;
        let qty = parseFloat(document.getElementById(`qty_${id}`).value) || 0;
        let subtotal = harga * qty;
        
        document.getElementById(`subtotal_${id}`).value = subtotal;
        document.getElementById(`subtotal_display_${id}`).value = formatRupiah(subtotal);
        
        hitungTotalSemua();
    }

    function hitungTotalSemua() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('display_total').innerText = formatRupiah(total);
    }

    function hapusBaris(id) {
        document.getElementById(`row_${id}`).remove();
        hitungTotalSemua();
    }

    function toggleAkunKas() {
        let metode = document.getElementById('metode_pembayaran').value;
        let div = document.getElementById('div_akun_kas');
        let input = document.getElementById('akun_kas_bank');
        
        if (metode === 'Tunai') {
            div.style.display = 'block';
            input.setAttribute('required', 'required');
        } else {
            div.style.display = 'none';
            input.removeAttribute('required');
            input.value = '';
        }
    }

    // Init
    tambahBaris();
    toggleAkunKas();
</script>
@endpush
