<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Jurnal Pembelian</h3>
    <a href="<?php echo BASEURL; ?>/pembelian/tambah" class="btn btn-primary">Tambah Pembelian</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Faktur</th>
                        <th>Pemasok</th>
                        <th class="text-end">Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['pembelian'])): ?>
                        <tr><td colspan="5" class="text-center py-4">Belum ada transaksi pembelian.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['pembelian'] as $pbl): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($pbl['tanggal_faktur'])); ?></td>
                            <td><?php echo htmlspecialchars($pbl['no_faktur_pembelian']); ?></td>
                            <td><?php echo htmlspecialchars($pbl['nama_pemasok']); ?></td>
                            <td class="text-end"><?php echo number_format($pbl['total'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="<?php echo BASEURL; ?>/pembelian/lihat/<?php echo $pbl['id_pembelian']; ?>" class="btn btn-sm btn-info">Lihat</a>
                                <?php if (Auth::isAdmin() || Auth::isManager()): // Tampilkan tombol Hapus hanya untuk Admin & Manajer ?>
                                    <a href="<?php echo BASEURL; ?>/pembelian/hapus/<?php echo $pbl['id_pembelian']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin membatalkan faktur ini? Tindakan ini akan membatalkan jurnal dan mengembalikan stok.');">Hapus</a>
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

