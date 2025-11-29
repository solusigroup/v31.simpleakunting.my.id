    <div class="card">
        <div class="card-header"><h3>Form Tambah Pemasok</h3></div>
        <div class="card-body">
            <form action="<?php echo BASEURL; ?>/pemasok/simpan" method="post">
                <div class="mb-3"><label>Nama Pemasok</label><input type="text" name="nama_pemasok" class="form-control" required></div>
                <div class="mb-3"><label>Alamat</label><textarea name="alamat" class="form-control"></textarea></div>
                <div class="mb-3"><label>Telepon</label><input type="text" name="telepon" class="form-control"></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
                <div class="mb-3"><label>Saldo Awal Hutang</label><input type="number" step="0.01" name="saldo_awal_hutang" class="form-control" value="0.00"></div>
                <hr>
                <a href="<?php echo BASEURL; ?>/pemasok" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
    

