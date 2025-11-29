<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Pelanggan</h3>
    <a href="<?php echo BASEURL; ?>/pelanggan/tambah" class="btn btn-primary">Tambah Pelanggan</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th class="text-end">Saldo Awal Piutang</th>
                        <th class="text-end">Saldo Terkini Piutang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['pelanggan'])): ?>
                        <tr><td colspan="5" class="text-center">Belum ada data pelanggan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['pelanggan'] as $plg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plg['nama_pelanggan']); ?></td>
                            <td><?php echo htmlspecialchars($plg['telepon']); ?></td>
                            <td class="text-end"><?php echo number_format($plg['saldo_awal_piutang'], 2, ',', '.'); ?></td>
                            <td class="text-end"><strong><?php echo number_format($plg['saldo_terkini_piutang'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <a href="<?php echo BASEURL; ?>/pelanggan/edit/<?php echo $plg['id_pelanggan']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?php echo BASEURL; ?>/pelanggan/hapus/<?php echo $plg['id_pelanggan']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?');">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

