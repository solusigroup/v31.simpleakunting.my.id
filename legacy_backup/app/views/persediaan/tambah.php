<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Form Tambah Barang Persediaan</h3>
        <a href="<?php echo BASEURL; ?>/persediaan" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/persediaan/simpan" method="post">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="kode_barang" class="form-label">Kode Barang</label>
                    <input type="text" id="kode_barang" name="kode_barang" class="form-control" required autofocus>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="satuan" class="form-label">Satuan (Cth: Pcs, Unit, Kg)</label>
                    <input type="text" id="satuan" name="satuan" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="stok_awal" class="form-label">Stok Awal</label>
                    <input type="number" id="stok_awal" step="0.01" name="stok_awal" class="form-control" value="0.00" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="harga_beli" class="form-label">Harga Beli (Modal)</label>
                    <input type="number" id="harga_beli" step="0.01" name="harga_beli" class="form-control" value="0.00" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual</label>
                    <input type="number" id="harga_jual" step="0.01" name="harga_jual" class="form-control" value="0.00" required>
                </div>
            </div>
            <hr>
            <h5>Akun Terkait</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="akun_persediaan" class="form-label">Akun Persediaan</label>
                    <select id="akun_persediaan" name="akun_persediaan" class="form-select" required>
                        <option value="">Pilih Akun...</option>
                        <?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,1)=='1' && $akun['tipe_akun']=='Detail'){ echo "<option value='{$akun['kode_akun']}'>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="akun_hpp" class="form-label">Akun HPP</label>
                    <select id="akun_hpp" name="akun_hpp" class="form-select" required>
                         <option value="">Pilih Akun...</option>
                        <?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,1)=='5' && $akun['tipe_akun']=='Detail'){ echo "<option value='{$akun['kode_akun']}'>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="akun_penjualan" class="form-label">Akun Penjualan</label>
                    <select id="akun_penjualan" name="akun_penjualan" class="form-select" required>
                         <option value="">Pilih Akun...</option>
                        <?php foreach($data['akun'] as $akun){ if(substr($akun['kode_akun'],0,1)=='4' && $akun['tipe_akun']=='Detail'){ echo "<option value='{$akun['kode_akun']}'>{$akun['nama_akun']}</option>"; } } ?>
                    </select>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="<?php echo BASEURL; ?>/persediaan" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Barang</button>
            </div>
        </form>
    </div>
</div>

