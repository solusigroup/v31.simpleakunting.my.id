<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Form Transaksi Kas & Bank</h3>
        <a href="<?php echo BASEURL; ?>/kas" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/kas/simpan" method="post">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Tipe Transaksi</label>
                    <select name="tipe_transaksi" class="form-select" required>
                        <option value="Masuk">Kas Masuk</option>
                        <option value="Keluar">Kas Keluar</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>No. Bukti</label>
                    <input type="text" name="no_bukti" class="form-control" required>
                </div>
            </div>
            <div class="row">
                 <div class="col-md-6 mb-3">
                    <label>Akun Kas / Bank</label>
                    <select name="akun_kas_bank" class="form-select" required>
                        <?php foreach($data['akun_kas_list'] as $akun): ?>
                            <option value="<?php echo $akun['kode_akun']; ?>"><?php echo $akun['nama_akun']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Akun Lawan (Pendapatan/Beban/Modal)</label>
                    <select name="akun_lawan" class="form-select" required>
                        <option value="">Pilih Akun...</option>
                        <?php foreach($data['akun_lawan_list'] as $grup => $akuns): ?>
                            <optgroup label="<?php echo htmlspecialchars($grup); ?>">
                                <?php foreach($akuns as $akun): ?>
                                    <option value="<?php echo $akun['kode_akun']; ?>"><?php echo $akun['nama_akun']; ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Jumlah</label>
                    <input type="number" step="0.01" name="jumlah" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control" required>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="<?php echo BASEURL; ?>/kas" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

