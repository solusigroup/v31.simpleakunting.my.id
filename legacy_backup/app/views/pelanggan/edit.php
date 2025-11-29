    <div class="card">
        <div class="card-header"><h3>Form Edit Pelanggan</h3></div>
        <div class="card-body">
            <form action="<?php echo BASEURL; ?>/pelanggan/update" method="post">
                <input type="hidden" name="id_pelanggan" value="<?php echo $data['pelanggan']['id_pelanggan']; ?>">
                <div class="mb-3"><label>Nama Pelanggan</label><input type="text" name="nama_pelanggan" class="form-control" value="<?php echo $data['pelanggan']['nama_pelanggan']; ?>" required></div>
                <div class="mb-3"><label>Alamat</label><textarea name="alamat" class="form-control"><?php echo $data['pelanggan']['alamat']; ?></textarea></div>
                <div class="mb-3"><label>Telepon</label><input type="text" name="telepon" class="form-control" value="<?php echo $data['pelanggan']['telepon']; ?>"></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo $data['pelanggan']['email']; ?>"></div>
                <div class="mb-3"><label>Saldo Awal Piutang</label><input type="number" step="0.01" name="saldo_awal_piutang" class="form-control" value="<?php echo $data['pelanggan']['saldo_awal_piutang']; ?>"></div>
                <hr>
                <a href="<?php echo BASEURL; ?>/pelanggan" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
    

