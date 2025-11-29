<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Penerimaan Pelanggan</h3>
    <a href="<?php echo BASEURL; ?>/penerimaan/tambah" class="btn btn-primary">Catat Penerimaan</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr><th>Tanggal</th><th>No. Bukti</th><th>Pelanggan</th><th class="text-end">Total Diterima</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($data['penerimaan'])): ?>
                        <tr><td colspan="5" class="text-center py-4">Belum ada data penerimaan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['penerimaan'] as $pnr): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($pnr['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($pnr['no_bukti']); ?></td>
                            <td><?php echo htmlspecialchars($pnr['nama_pelanggan']); ?></td>
                            <td class="text-end"><?php echo number_format($pnr['total_diterima'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <a href="<?php echo BASEURL; ?>/penerimaan/lihat/<?php echo $pnr['id_penerimaan']; ?>" class="btn btn-sm btn-info">Lihat</a>
                                <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                                    <a href="#" class="btn btn-sm btn-danger disabled" onclick="return confirm('Yakin ingin membatalkan penerimaan ini?');">Hapus</a>
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

