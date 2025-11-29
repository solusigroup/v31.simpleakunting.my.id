<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Transaksi Kas & Bank</h3>
    <a href="<?php echo BASEURL; ?>/kas/tambah" class="btn btn-primary">Tambah Transaksi</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Bukti</th>
                        <th>Tipe</th>
                        <th>Akun Kas/Bank</th>
                        <th>Akun Lawan</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['transaksi'])): ?>
                        <tr><td colspan="8" class="text-center py-4">Belum ada transaksi kas & bank.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['transaksi'] as $trx): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($trx['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($trx['no_bukti']); ?></td>
                            <td>
                                <?php if($trx['tipe_transaksi'] == 'Masuk'): ?>
                                    <span class="badge text-bg-success">Masuk</span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($trx['nama_akun_kas']); ?></td>
                            <td><?php echo htmlspecialchars($trx['nama_akun_lawan']); ?></td>
                            <td><?php echo htmlspecialchars($trx['deskripsi']); ?></td>
                            <td class="text-end"><?php echo number_format($trx['jumlah'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                                    <a href="<?php echo BASEURL; ?>/kas/edit/<?php echo $trx['id_transaksi']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?php echo BASEURL; ?>/kas/hapus/<?php echo $trx['id_transaksi']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin membatalkan transaksi ini?');">Hapus</a>
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

