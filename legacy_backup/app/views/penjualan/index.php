<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Jurnal Penjualan</h3>
    <a href="<?php echo BASEURL; ?>/penjualan/tambah" class="btn btn-primary">Tambah Penjualan</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Faktur</th>
                        <th>Pelanggan</th>
                        <th class="text-end">Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['penjualan'])): ?>
                        <tr><td colspan="5" class="text-center py-4">Belum ada transaksi penjualan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['penjualan'] as $pjl): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($pjl['tanggal_faktur'])); ?></td>
                            <td><?php echo htmlspecialchars($pjl['no_faktur']); ?></td>
                            <td><?php echo htmlspecialchars($pjl['nama_pelanggan']); ?></td>
                            <td class="text-end"><?php echo number_format($pjl['total'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="<?php echo BASEURL; ?>/penjualan/lihat/<?php echo $pjl['id_penjualan']; ?>" class="btn btn-sm btn-info">Lihat</a>
                                <?php if (Auth::isAdmin() || Auth::isManager()): // Tampilkan tombol Hapus hanya untuk Admin & Manajer ?>
                                    <a href="<?php echo BASEURL; ?>/penjualan/hapus/<?php echo $pjl['id_penjualan']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin membatalkan faktur ini? Tindakan ini akan membatalkan jurnal dan mengembalikan stok.');">Hapus</a>
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

