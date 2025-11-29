<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Form Edit Pengguna</h3>
        <a href="<?php echo BASEURL; ?>/user" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/user/update" method="post">
            <input type="hidden" name="id_user" value="<?php echo $data['user']['id_user']; ?>">
            <div class="mb-3">
                <label for="nama_user" class="form-label">Nama</label>
                <input type="text" id="nama_user" name="nama_user" class="form-control" value="<?php echo htmlspecialchars($data['user']['nama_user']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
            </div>
            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan</label>
                <input type="text" id="jabatan" name="jabatan" class="form-control" value="<?php echo htmlspecialchars($data['user']['jabatan']); ?>">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="Staff" <?php echo ($data['user']['role'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                    <option value="Manager" <?php echo ($data['user']['role'] == 'Manager') ? 'selected' : ''; ?>>Manager</option>
                    <option value="Admin" <?php echo ($data['user']['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="<?php echo BASEURL; ?>/user" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

