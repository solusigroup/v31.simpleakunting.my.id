<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Pemasok</h3>
    <?php if (Auth::isAdmin() || Auth::isManager()): // Tampilkan tombol hanya untuk Admin & Manajer ?>
        <a href="<?php echo BASEURL; ?>/pemasok/tambah" class="btn btn-primary">Tambah Pemasok</a>
    <?php endif; ?>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th class="text-end">Saldo Awal Hutang</th>
                        <th class="text-end">Saldo Terkini Hutang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['pemasok'])): ?>
                        <tr><td colspan="5" class="text-center">Belum ada data pemasok.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['pemasok'] as $pms): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pms['nama_pemasok']); ?></td>
                            <td><?php echo htmlspecialchars($pms['telepon']); ?></td>
                            <td class="text-end"><?php echo number_format($pms['saldo_awal_hutang'], 2, ',', '.'); ?></td>
                            <td class="text-end"><strong><?php echo number_format($pms['saldo_terkini_hutang'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <?php if (Auth::isAdmin() || Auth::isManager()): // Tampilkan tombol hanya untuk Admin & Manajer ?>
                                    <a href="<?php echo BASEURL; ?>/pemasok/edit/<?php echo $pms['id_pemasok']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?php echo BASEURL; ?>/pemasok/hapus/<?php echo $pms['id_pemasok']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?');">Hapus</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

