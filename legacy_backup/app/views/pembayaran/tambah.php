<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Pembayaran Pemasok</h3>
        <a href="<?php echo BASEURL; ?>/pembayaran" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/pembayaran/simpan" method="post">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="id_pemasok" class="form-label">Pemasok</label>
                    <select id="id_pemasok" name="id_pemasok" class="form-select" required>
                        <option value="">Pilih Pemasok...</option>
                        <?php foreach($data['pemasok'] as $pemasok): ?>
                            <option value="<?php echo $pemasok['id_pemasok']; ?>"><?php echo $pemasok['nama_pemasok']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="akun_kas_bank" class="form-label">Bayar Dari Akun</label>
                    <select id="akun_kas_bank" name="akun_kas_bank" class="form-select" required>
                        <option value="">Pilih Akun Kas/Bank...</option>
                        <!-- **PERBAIKAN: Loop dari $data['akun_kas_list']** -->
                        <?php foreach($data['akun_kas_list'] as $akun): ?>
                            <option value="<?php echo $akun['kode_akun']; ?>"><?php echo $akun['nama_akun']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="no_bukti" class="form-label">No. Bukti</label>
                    <input type="text" class="form-control" id="no_bukti" name="no_bukti" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Pembayaran utang kepada..." required>
                </div>
            </div>
            
            <hr>
            
            <h5>Detail Pembayaran Faktur</h5>
            <table class="table table-sm" id="faktur-table">
                <thead class="table-light">
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal Faktur</th>
                        <th class="text-end">Sisa Tagihan</th>
                        <th class="text-end" style="width: 25%;">Jumlah Bayar</th>
                    </tr>
                </thead>
                <tbody id="faktur-list">
                    <tr>
                        <td colspan="4" class="text-center text-muted">Pilih pemasok terlebih dahulu untuk menampilkan faktur.</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <label for="total_pembayaran" class="form-label">Total Pembayaran</label>
                    <input type="number" class="form-control text-end" id="total_pembayaran" name="total_pembayaran" readonly>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="<?php echo BASEURL; ?>/pembayaran" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('id_pemasok').addEventListener('change', function() {
        const idPemasok = this.value;
        const tbody = document.getElementById('faktur-list');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>';

        if (!idPemasok) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Pilih pemasok terlebih dahulu...</td></tr>';
            return;
        }

        fetch('<?php echo BASEURL; ?>/pembayaran/getFaktur/' + idPemasok)
            .then(response => response.json())
            .then(fakturList => {
                tbody.innerHTML = '';
                if (fakturList.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Tidak ada faktur yang belum lunas.</td></tr>';
                    return;
                }
                
                fakturList.forEach(faktur => {
                    let row = `
                        <tr>
                            <td>${faktur.no_faktur_pembelian}</td>
                            <td>${faktur.tanggal_faktur}</td>
                            <td class="text-end">${new Intl.NumberFormat('id-ID').format(faktur.sisa_tagihan)}</td>
                            <td>
                                <input type="hidden" name="details[id_pembelian][]" value="${faktur.id_pembelian}">
                                <input type="number" class="form-control form-control-sm text-end bayar-input" name="details[jumlah_bayar][]" value="0" step="0.01" max="${faktur.sisa_tagihan}">
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
                
                document.querySelectorAll('.bayar-input').forEach(input => {
                    input.addEventListener('input', hitungTotal);
                });
                hitungTotal();
            });
    });

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.bayar-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('total_pembayaran').value = total.toFixed(2);
    }
</script>