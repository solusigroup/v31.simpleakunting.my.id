<div class="card shadow-sm">
    <div class="card-header">
        <h3>Edit Entri Jurnal</h3>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/jurnal/update" method="post">
            <input type="hidden" name="id_jurnal" value="<?php echo htmlspecialchars($data['jurnal']['id_jurnal']); ?>">
            <!-- Header Jurnal -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($data['jurnal']['tanggal']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="no_transaksi" class="form-label">No. Transaksi / Bukti</label>
                    <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" value="<?php echo htmlspecialchars($data['jurnal']['no_transaksi']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <input type="text" class="form-control" id="deskripsi" name="deskripsi" value="<?php echo htmlspecialchars($data['jurnal']['deskripsi']); ?>" required>
                </div>
            </div>

            <!-- Detail Jurnal -->
            <hr>
            <h5>Detail Transaksi</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="35%">Akun</th>
                        <th width="25%">Debit</th>
                        <th width="25%">Kredit</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="jurnal-details-body">
                    <?php foreach($data['jurnal']['details'] as $detail): ?>
                    <tr>
                        <td>
                            <select name="details[kode_akun][]" class="form-select" required>
                                <option value="">Pilih Akun...</option>
                                <?php foreach($data['akun'] as $akun): ?>
                                    <?php if($akun['tipe_akun'] == 'Detail'): ?>
                                        <option value="<?php echo htmlspecialchars($akun['kode_akun']); ?>" <?php echo ($akun['kode_akun'] == $detail['kode_akun']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($akun['kode_akun'] . ' - ' . $akun['nama_akun']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="details[debit][]" class="form-control debit-input" value="<?php echo htmlspecialchars($detail['debit']); ?>" required></td>
                        <td><input type="number" step="0.01" name="details[kredit][]" class="form-control kredit-input" value="<?php echo htmlspecialchars($detail['kredit']); ?>" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th class="text-end">Total</th>
                        <th><input type="text" id="total-debit" class="form-control" readonly></th>
                        <th><input type="text" id="total-kredit" class="form-control" readonly></th>
                        <td id="balance-status" class="text-center align-middle"></td>
                    </tr>
                </tfoot>
            </table>
            <button type="button" id="add-row" class="btn btn-success btn-sm">Tambah Baris</button>

            <!-- Tombol Simpan -->
            <hr>
            <a href="<?php echo BASEURL; ?>/jurnal" class="btn btn-secondary">Batal</a>
            <button type="submit" id="save-button" class="btn btn-primary">Update Jurnal</button>
        </form>
    </div>
</div>

<!-- Template baris baru disembunyikan -->
<template id="jurnal-row-template">
    <tr>
        <td>
            <select name="details[kode_akun][]" class="form-select" required>
                <option value="">Pilih Akun...</option>
                <?php
                // PERBAIKAN: Menambahkan pengecekan untuk memastikan $data['akun'] ada dan merupakan array
                if (isset($data['akun']) && is_array($data['akun'])) {
                    foreach($data['akun'] as $akun) {
                        if($akun['tipe_akun'] == 'Detail') {
                            echo '<option value="' . htmlspecialchars($akun['kode_akun']) . '">' . htmlspecialchars($akun['kode_akun'] . ' - ' . $akun['nama_akun']) . '</option>';
                        }
                    }
                }
                ?>
            </select>
        </td>
        <td><input type="number" step="0.01" name="details[debit][]" class="form-control debit-input" value="0.00" required></td>
        <td><input type="number" step="0.01" name="details[kredit][]" class="form-control kredit-input" value="0.00" required></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
    </tr>
</template>

<script>
    // Script ini tidak perlu diubah, karena logikanya sudah benar.
    // Masalahnya ada pada pembuatan template oleh PHP.
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('jurnal-details-body');
        const template = document.getElementById('jurnal-row-template');
        const addRowBtn = document.getElementById('add-row');
        const saveBtn = document.getElementById('save-button');
        const totalDebitEl = document.getElementById('total-debit');
        const totalKreditEl = document.getElementById('total-kredit');
        const balanceStatusEl = document.getElementById('balance-status');

        function addRow() {
            const clone = template.content.cloneNode(true);
            tbody.appendChild(clone);
        }

        function calculateTotals() {
            let totalDebit = 0;
            let totalKredit = 0;
            document.querySelectorAll('.debit-input').forEach(input => totalDebit += parseFloat(input.value) || 0);
            document.querySelectorAll('.kredit-input').forEach(input => totalKredit += parseFloat(input.value) || 0);

            totalDebitEl.value = totalDebit.toFixed(2);
            totalKreditEl.value = totalKredit.toFixed(2);

            if (totalDebit === totalKredit && totalDebit > 0) {
                balanceStatusEl.innerHTML = '<span class="badge text-bg-success">Balance</span>';
                saveBtn.disabled = false;
            } else {
                balanceStatusEl.innerHTML = '<span class="badge text-bg-danger">Unbalance</span>';
                saveBtn.disabled = true;
            }
        }

        addRowBtn.addEventListener('click', addRow);
        
        tbody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });

        tbody.addEventListener('input', function(e) {
            if (e.target.classList.contains('debit-input') || e.target.classList.contains('kredit-input')) {
                const row = e.target.closest('tr');
                const debitInput = row.querySelector('.debit-input');
                const kreditInput = row.querySelector('.kredit-input');
                
                if (e.target === debitInput && parseFloat(debitInput.value) > 0) {
                    kreditInput.value = '0.00';
                } else if (e.target === kreditInput && parseFloat(kreditInput.value) > 0) {
                    debitInput.value = '0.00';
                }
                calculateTotals();
            }
        });

        calculateTotals();
    });
</script>

