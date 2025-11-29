<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Form Edit Aset Tetap</h3>
        <a href="<?php echo BASEURL; ?>/aset" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/aset/update" method="post">
            <input type="hidden" name="id_aset" value="<?php echo $data['aset']['id_aset']; ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="kode_aset" class="form-label">Kode Aset</label>
                    <input type="text" id="kode_aset" name="kode_aset" class="form-control" value="<?php echo htmlspecialchars($data['aset']['kode_aset']); ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="nama_aset" class="form-label">Nama Aset</label>
                    <input type="text" id="nama_aset" name="nama_aset" class="form-control" value="<?php echo htmlspecialchars($data['aset']['nama_aset']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="kelompok_aset" class="form-label">Kelompok Aset</label>
                    <input type="text" id="kelompok_aset" name="kelompok_aset" class="form-control" value="<?php echo htmlspecialchars($data['aset']['kelompok_aset']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan</label>
                    <input type="date" id="tanggal_perolehan" name="tanggal_perolehan" class="form-control" value="<?php echo htmlspecialchars($data['aset']['tanggal_perolehan']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="harga_perolehan" class="form-label">Harga Perolehan</label>
                    <input type="number" step="0.01" id="harga_perolehan" name="harga_perolehan" class="form-control" value="<?php echo htmlspecialchars($data['aset']['harga_perolehan']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="masa_manfaat" class="form-label">Masa Manfaat (Tahun)</label>
                    <input type="number" id="masa_manfaat" name="masa_manfaat" class="form-control" value="<?php echo htmlspecialchars($data['aset']['masa_manfaat']); ?>" required>
                </div>
            </div>
            <hr>
            <h5>Akun Terkait</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="akun_aset" class="form-label">Akun Aset</label>
                    <select id="akun_aset" name="akun_aset" class="form-select" required>
                        <?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,3)=='1.3' && $akun['tipe_akun']=='Detail'){ $selected = ($akun['kode_akun'] == $data['aset']['akun_aset']) ? 'selected' : ''; echo "<option value='{$akun['kode_akun']}' {$selected}>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="akun_akumulasi_depresiasi" class="form-label">Akun Akumulasi Depresiasi</label>
                    <select id="akun_akumulasi_depresiasi" name="akun_akumulasi_depresiasi" class="form-select" required>
                        <?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,3)=='1.3' && $akun['tipe_akun']=='Detail' && str_contains($akun['nama_akun'], 'Akumulasi')){ $selected = ($akun['kode_akun'] == $data['aset']['akun_akumulasi_depresiasi']) ? 'selected' : ''; echo "<option value='{$akun['kode_akun']}' {$selected}>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="akun_beban_depresiasi" class="form-label">Akun Beban Depresiasi</label>
                    <select id="akun_beban_depresiasi" name="akun_beban_depresiasi" class="form-select" required>
                        <?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,3)=='6.1' && $akun['tipe_akun']=='Detail'){ $selected = ($akun['kode_akun'] == $data['aset']['akun_beban_depresiasi']) ? 'selected' : ''; echo "<option value='{$akun['kode_akun']}' {$selected}>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="<?php echo BASEURL; ?>/aset" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Update Aset</button>
            </div>
        </form>
    </div>
</div>

