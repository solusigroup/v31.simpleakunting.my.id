<div class="card shadow-sm">
    <div class="card-header">
        <h3>Pengaturan Perusahaan</h3>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/perusahaan/update" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="<?php echo htmlspecialchars(string: $data['perusahaan']['nama_perusahaan'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars(string: $data['perusahaan']['alamat'] ?? ''); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars(string: $data['perusahaan']['telepon'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars(string: $data['perusahaan']['email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kota_laporan" class="form-label">Kota</label>
                            <input type="text" class="form-control" id="kota_laporan" name="kota_laporan" value="<?php echo htmlspecialchars(string: $data['perusahaan']['kota_laporan'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo Perusahaan</label>
                        <?php if (!empty($data['perusahaan']['path_logo'])): ?>
                            <img src="<?php echo BASEURL . '/' . htmlspecialchars(string: $data['perusahaan']['path_logo']); ?>" class="img-thumbnail mb-2" alt="Logo Saat Ini">
                        <?php endif; ?>
                        <input class="form-control" type="file" name="logo" id="logo" accept="image/png, image/jpeg">
                        <div class="form-text">Unggah logo baru untuk mengganti. Format: PNG, JPG.</div>
                    </div>
                </div>
            </div>
            <hr>
            <h5>Akun Kontrol Default</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="akun_piutang_default" class="form-label">Akun Piutang Usaha</label>
                    <select class="form-select" id="akun_piutang_default" name="akun_piutang_default" required>
                        <?php foreach($data['akun'] as $akun){ if(substr(string: $akun['kode_akun'],offset: 0,length: 3)=='1.1' && $akun['tipe_akun']=='Detail'){ $selected = ($akun['kode_akun'] == $data['perusahaan']['akun_piutang_default']) ? 'selected' : ''; echo "<option value='{$akun['kode_akun']}' {$selected}>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="akun_utang_default" class="form-label">Akun Utang Usaha</label>
                    <select class="form-select" id="akun_utang_default" name="akun_utang_default" required>
                        <?php foreach($data['akun'] as $akun){ if(substr(string: $akun['kode_akun'],offset: 0,length: 3)=='2.1' && $akun['tipe_akun']=='Detail'){ $selected = ($akun['kode_akun'] == $data['perusahaan']['akun_utang_default']) ? 'selected' : ''; echo "<option value='{$akun['kode_akun']}' {$selected}>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
            </div>
            <hr>
            <h5>Penandatangan Laporan</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="penandatangan_1_id" class="form-label">Penandatangan 1 (Kiri)</label>
                    <select class="form-select" id="penandatangan_1_id" name="penandatangan_1_id">
                        <option value="">-- Tidak Ada --</option>
                        <?php foreach($data['users'] as $user): ?>
                            <?php $selected = ($user['id_user'] == $data['perusahaan']['penandatangan_1_id']) ? 'selected' : ''; ?>
                            <option value="<?php echo $user['id_user']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars(string: $user['nama_user']); ?> (<?php echo htmlspecialchars(string: $user['jabatan']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="penandatangan_2_id" class="form-label">Penandatangan 2 (Kanan)</label>
                    <select class="form-select" id="penandatangan_2_id" name="penandatangan_2_id">
                        <option value="">-- Tidak Ada --</option>
                         <?php foreach($data['users'] as $user): ?>
                            <?php $selected = ($user['id_user'] == $data['perusahaan']['penandatangan_2_id']) ? 'selected' : ''; ?>
                            <option value="<?php echo $user['id_user']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars(string: $user['nama_user']); ?> (<?php echo htmlspecialchars(string: $user['jabatan']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr>
            <h5>Akun Proses Tutup Buku</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="akun_laba_ditahan" class="form-label">Akun Laba Ditahan</label>
                    <select class="form-select" id="akun_laba_ditahan" name="akun_laba_ditahan" required>
                        <?php foreach($data['akun'] as $akun){ if(substr(string: $akun['kode_akun'],offset: 0,length: 1)=='3' && $akun['tipe_akun']=='Detail'){ $selected = ($akun['kode_akun'] == $data['perusahaan']['akun_laba_ditahan']) ? 'selected' : ''; echo "<option value='{$akun['kode_akun']}' {$selected}>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="akun_ikhtisar_lr" class="form-label">Akun Ikhtisar Laba/Rugi</label>
                    <select class="form-select" id="akun_ikhtisar_lr" name="akun_ikhtisar_lr" required>
                        <?php foreach($data['akun'] as $akun){ if(substr(string: $akun['kode_akun'],offset: 0,length: 1)=='3' && $akun['tipe_akun']=='Detail'){ $selected = ($akun['kode_akun'] == $data['perusahaan']['akun_ikhtisar_lr']) ? 'selected' : ''; echo "<option value='{$akun['kode_akun']}' {$selected}>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

