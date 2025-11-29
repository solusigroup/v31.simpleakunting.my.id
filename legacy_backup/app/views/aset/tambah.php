    <div class="card">
        <div class="card-header"><h3>Form Tambah Aset Tetap</h3></div>
        <div class="card-body">
            <form action="<?php echo BASEURL; ?>/aset/simpan" method="post">
                <div class="row">
                    <div class="col-md-4 mb-3"><label>Kode Aset</label><input type="text" name="kode_aset" class="form-control" required></div>
                    <div class="col-md-8 mb-3"><label>Nama Aset</label><input type="text" name="nama_aset" class="form-control" required></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>Kelompok Aset</label><input type="text" name="kelompok_aset" class="form-control"></div>
                    <div class="col-md-4 mb-3"><label>Tanggal Perolehan</label><input type="date" name="tanggal_perolehan" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                    <div class="col-md-4 mb-3"><label>Harga Perolehan</label><input type="number" step="0.01" name="harga_perolehan" class="form-control" required></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>Masa Manfaat (Tahun)</label><input type="number" name="masa_manfaat" class="form-control" required></div>
                </div>
                <hr>
                <h5>Akun Terkait</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>Akun Aset</label><select name="akun_aset" class="form-select" required><?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,3)=='1-2' && $akun['tipe_akun']=='Detail'){ echo "<option value='{$akun['kode_akun']}'>{$akun['nama_akun']}</option>"; } } ?></select></div>
                    <div class="col-md-4 mb-3"><label>Akun Akumulasi Depresiasi</label><select name="akun_akumulasi_depresiasi" class="form-select" required><?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,3)=='1-2' && $akun['tipe_akun']=='Detail' && str_contains($akun['nama_akun'], 'Akumulasi')){ echo "<option value='{$akun['kode_akun']}'>{$akun['nama_akun']}</option>"; } } ?></select></div>
                    <div class="col-md-4 mb-3"><label>Akun Beban Depresiasi</label><select name="akun_beban_depresiasi" class="form-select" required><?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,3)=='6-4' && $akun['tipe_akun']=='Detail'){ echo "<option value='{$akun['kode_akun']}'>{$akun['nama_akun']}</option>"; } } ?></select></div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
    
