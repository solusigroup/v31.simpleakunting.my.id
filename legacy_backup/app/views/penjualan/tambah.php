<div class="card">
    <div class="card-header"><h3>Form Tambah Penjualan</h3></div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/penjualan/simpan" method="post">
            <!-- Header -->
            <div class="row">
                <div class="col-md-4 mb-3"><label>Pelanggan</label><select name="id_pelanggan" class="form-select" required><?php foreach($data['pelanggan'] as $pl){ echo "<option value='{$pl['id_pelanggan']}'>{$pl['nama_pelanggan']}</option>"; } ?></select></div>
                <div class="col-md-4 mb-3"><label>Tanggal</label><input type="date" name="tanggal_faktur" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="col-md-4 mb-3"><label>No. Faktur</label><input type="text" name="no_faktur" class="form-control" required></div>
            </div>

            <!-- **PERUBAHAN: Pilihan Metode Pembayaran** -->
            <div class="row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="metode_pembayaran">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                        <option value="Kredit">Kredit (Menambah Piutang)</option>
                        <option value="Tunai">Tunai</option>
                    </select>
                </div>
                <div class="col-md-8 mb-3" id="akun_kas_container" style="display:none;">
                    <label for="akun_kas_bank">Setor Ke Akun Kas/Bank</label>
                    <select name="akun_kas_bank" id="akun_kas_bank" class="form-select">
                        <?php foreach($data['akun_kas'] as $akun){ 
                            // Filter hanya untuk akun Kas & Bank (biasanya seri 1.11XX)
                            if(substr($akun['kode_akun'], 0, 3) == '1.1' && $akun['tipe_akun'] == 'Detail'){ 
                                echo "<option value='{$akun['kode_akun']}'>{$akun['nama_akun']}</option>"; 
                            } 
                        } ?>
                    </select>
                </div>
            </div>

            <!-- Detail -->
            <table class="table table-bordered">
                <thead class="table-light"><tr><th>Nama Barang</th><th width="15%">Kuantitas</th><th width="20%">Harga Jual</th><th width="20%" class="text-end">Subtotal</th><th width="5%"></th></tr></thead>
                <tbody id="detail-body"></tbody>
                <tfoot><tr><th colspan="3" class="text-end">Total</th><th class="text-end" id="total-penjualan">0.00</th><th></th></tr></tfoot>
            </table>
            <button type="button" id="add-item" class="btn btn-success btn-sm">Tambah Item</button>
            <hr>
            <div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"></textarea></div>
            <a href="<?php echo BASEURL; ?>/penjualan" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
        </form>
    </div>
</div>

<template id="detail-row-template">
    <tr>
        <td>
            <select name="details[id_barang][]" class="form-select item-select" required>
                <option value="">Pilih Barang...</option>
                <?php foreach($data['barang'] as $brg): ?>
                    <option value="<?php echo $brg['id_barang']; ?>" data-harga="<?php echo $brg['harga_jual']; ?>">
                        <?php echo $brg['kode_barang'] . ' - ' . $brg['nama_barang']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="details[kuantitas][]" class="form-control qty" value="1" step="0.01"></td>
        <td><input type="number" name="details[harga][]" class="form-control price" value="0" step="0.01"></td>
        <td><input type="text" name="details[subtotal][]" class="form-control subtotal text-end" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-item">X</button></td>
    </tr>
</template>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // **JAVASCRIPT BARU UNTUK METODE PEMBAYARAN**
        const metodeSelect = document.getElementById('metode_pembayaran');
        const kasContainer = document.getElementById('akun_kas_container');
        const kasSelect = document.getElementById('akun_kas_bank');

        metodeSelect.addEventListener('change', function() {
            if (this.value === 'Tunai') {
                kasContainer.style.display = 'block';
                kasSelect.required = true;
            } else {
                kasContainer.style.display = 'none';
                kasSelect.required = false;
            }
        });

        // --- Sisa JavaScript (tidak berubah) ---
        const addBtn = document.getElementById('add-item');
        const tbody = document.getElementById('detail-body');
        const template = document.getElementById('detail-row-template');
        const totalEl = document.getElementById('total-penjualan');

        function calculateRow(row) {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const subtotal = qty * price;
            row.querySelector('.subtotal').value = subtotal.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            tbody.querySelectorAll('.subtotal').forEach(el => { total += parseFloat(el.value) || 0; });
            totalEl.textContent = total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function addRow() {
            const clone = template.content.cloneNode(true);
            tbody.appendChild(clone);
        }

        addBtn.addEventListener('click', addRow);
        
        tbody.addEventListener('change', e => {
            if (e.target.matches('.item-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const harga = selectedOption.getAttribute('data-harga');
                const row = e.target.closest('tr');
                row.querySelector('.price').value = harga || 0;
                calculateRow(row);
            }
        });

        tbody.addEventListener('input', e => {
            if (e.target.matches('.qty, .price')) {
                calculateRow(e.target.closest('tr'));
            }
        });
        tbody.addEventListener('click', e => {
            if (e.target.matches('.remove-item')) {
                e.target.closest('tr').remove();
                calculateTotal();
            }
        });
        addRow();
    });
</script>

