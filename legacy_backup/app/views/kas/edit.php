<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Form Edit Transaksi Kas & Bank</h3>
        <a href="<?php echo BASEURL; ?>/kas" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/kas/update" method="post">
            <input type="hidden" name="id_transaksi" value="<?php echo $data['transaksi']['id_transaksi']; ?>">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Tipe Transaksi</label>
                    <select name="tipe_transaksi" class="form-select" required>
                        <option value="Masuk" <?php echo ($data['transaksi']['tipe_transaksi'] == 'Masuk') ? 'selected' : ''; ?>>Kas Masuk</option>
                        <option value="Keluar" <?php echo ($data['transaksi']['tipe_transaksi'] == 'Keluar') ? 'selected' : ''; ?>>Kas Keluar</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo htmlspecialchars($data['transaksi']['tanggal']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>No. Bukti</label>
                    <input type="text" name="no_bukti" class="form-control" value="<?php echo htmlspecialchars($data['transaksi']['no_bukti']); ?>" required>
                </div>
            </div>
            <div class="row">
                 <div class="col-md-6 mb-3">
                    <label>Akun Kas / Bank</label>
                    <select name="akun_kas_bank" class="form-select" required>
                        <?php foreach($data['akun_kas_list'] as $akun): ?>
                            <option value="<?php echo $akun['kode_akun']; ?>" <?php echo ($akun['kode_akun'] == $data['transaksi']['akun_kas_bank']) ? 'selected' : ''; ?>>
                                <?php echo $akun['nama_akun']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Akun Lawan</label>
                    <select name="akun_lawan" class="form-select" required>
                        <?php foreach($data['akun_lawan_list'] as $grup => $akuns): ?>
                            <optgroup label="<?php echo htmlspecialchars($grup); ?>">
                                <?php foreach($akuns as $akun): ?>
                                    <option value="<?php echo $akun['kode_akun']; ?>" <?php echo ($akun['kode_akun'] == $data['transaksi']['akun_lawan']) ? 'selected' : ''; ?>>
                                        <?php echo $akun['nama_akun']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Jumlah</label>
                    <input type="number" step="0.01" name="jumlah" class="form-control" value="<?php echo htmlspecialchars($data['transaksi']['jumlah']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control" value="<?php echo htmlspecialchars($data['transaksi']['deskripsi']); ?>" required>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="<?php echo BASEURL; ?>/kas" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Update Transaksi</button>
            </div>
        </form>
    </div>
</div>

