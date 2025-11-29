<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Form Tambah Pengguna</h3>
        <a href="<?php echo BASEURL; ?>/user" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/user/simpan" method="post">
            <div class="mb-3">
                <label for="nama_user" class="form-label">Nama</label>
                <input type="text" id="nama_user" name="nama_user" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan</label>
                <input type="text" id="jabatan" name="jabatan" class="form-control">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="Staff">Staff</option>
                    <option value="Manager">Manager</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="<?php echo BASEURL; ?>/user" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

