    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Data Pengguna</h3>
        <a href="<?php echo BASEURL; ?>/user/tambah" class="btn btn-primary">Tambah Pengguna</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr><th>Nama</th><th>Jabatan</th><th>Role</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($data['users'] as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['nama_user']); ?></td>
                        <td><?php echo htmlspecialchars($user['jabatan']); ?></td>
                        <td><span class="badge <?php echo ($user['role'] == 'Admin') ? 'text-bg-danger' : 'text-bg-secondary'; ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                        <td>
                            <a href="<?php echo BASEURL; ?>/user/edit/<?php echo $user['id_user']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?php echo BASEURL; ?>/user/hapus/<?php echo $user['id_user']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
