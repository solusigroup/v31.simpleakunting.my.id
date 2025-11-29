    <div class="card">
        <div class="card-header"><h3>Form Edit Pemasok</h3></div>
        <div class="card-body">
            <form action="<?php echo BASEURL; ?>/pemasok/update" method="post">
                <input type="hidden" name="id_pemasok" value="<?php echo $data['pemasok']['id_pemasok']; ?>">
                <div class="mb-3"><label>Nama Pemasok</label><input type="text" name="nama_pemasok" class="form-control" value="<?php echo $data['pemasok']['nama_pemasok']; ?>" required></div>
                <div class="mb-3"><label>Alamat</label><textarea name="alamat" class="form-control"><?php echo $data['pemasok']['alamat']; ?></textarea></div>
                <div class="mb-3"><label>Telepon</label><input type="text" name="telepon" class="form-control" value="<?php echo $data['pemasok']['telepon']; ?>"></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo $data['pemasok']['email']; ?>"></div>
                <div class="mb-3"><label>Saldo Awal Hutang</label><input type="number" step="0.01" name="saldo_awal_hutang" class="form-control" value="<?php echo $data['pemasok']['saldo_awal_hutang']; ?>"></div>
                <hr>
                <a href="<?php echo BASEURL; ?>/pemasok" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
    

