    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Master Aset Tetap</h3>
        <a href="<?php echo BASEURL; ?>/aset/tambah" class="btn btn-primary">Tambah Aset</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr><th>Kode</th><th>Nama Aset</th><th>Tgl Perolehan</th><th class="text-end">Harga Perolehan</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($data['aset'] as $ast): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ast['kode_aset']); ?></td>
                        <td><?php echo htmlspecialchars($ast['nama_aset']); ?></td>
                        <td><?php echo date('d M Y', strtotime($ast['tanggal_perolehan'])); ?></td>
                        <td class="text-end"><?php echo number_format($ast['harga_perolehan'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="<?php echo BASEURL; ?>/aset/edit/<?php echo $ast['id_aset']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?php echo BASEURL; ?>/aset/hapus/<?php echo $ast['id_aset']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
