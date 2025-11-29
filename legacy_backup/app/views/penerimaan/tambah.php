<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Penerimaan Pelanggan</h3>
        <a href="<?php echo BASEURL; ?>/penerimaan" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/penerimaan/simpan" method="post">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="id_pelanggan" class="form-label">Pelanggan</label>
                    <select id="id_pelanggan" name="id_pelanggan" class="form-select" required>
                        <option value="">Pilih Pelanggan...</option>
                        <?php foreach($data['pelanggan'] as $pelanggan): ?>
                            <option value="<?php echo $pelanggan['id_pelanggan']; ?>"><?php echo $pelanggan['nama_pelanggan']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="akun_kas_bank" class="form-label">Setor ke Akun</label>
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
                    <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Penerimaan piutang dari..." required>
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
                        <td colspan="4" class="text-center text-muted">Pilih pelanggan terlebih dahulu untuk menampilkan faktur.</td>
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
                <a href="<?php echo BASEURL; ?>/penerimaan" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Penerimaan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('id_pelanggan').addEventListener('change', function() {
        const idPelanggan = this.value;
        const tbody = document.getElementById('faktur-list');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>';

        if (!idPelanggan) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Pilih pelanggan terlebih dahulu...</td></tr>';
            return;
        }

        fetch('<?php echo BASEURL; ?>/penerimaan/getFaktur/' + idPelanggan)
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
                            <td>${faktur.no_faktur}</td>
                            <td>${faktur.tanggal_faktur}</td>
                            <td class="text-end">${new Intl.NumberFormat('id-ID').format(faktur.sisa_tagihan)}</td>
                            <td>
                                <input type="hidden" name="details[id_penjualan][]" value="${faktur.id_penjualan}">
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