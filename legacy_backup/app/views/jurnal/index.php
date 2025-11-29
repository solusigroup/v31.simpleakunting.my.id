<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Jurnal Umum</h3>
    <a href="<?php echo BASEURL; ?>/jurnal/tambah" class="btn btn-primary">Tambah Jurnal Baru</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Deskripsi</th>
                        <th>Sumber</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Cek peran user sekali saja untuk efisiensi
                        $isManagerOrAdmin = Auth::isAdmin() || Auth::isManager(); 
                        if (empty($data['jurnal'])): 
                    ?>
                        <tr><td colspan="6" class="text-center py-4">Belum ada entri jurnal.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['jurnal'] as $jurnal): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($jurnal['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($jurnal['no_transaksi']); ?></td>
                            <td><?php echo htmlspecialchars($jurnal['deskripsi']); ?></td>
                            <td>
                                <?php 
                                    $sumber = htmlspecialchars($jurnal['sumber_jurnal']);
                                    $badge_class = 'text-bg-info';
                                    if ($sumber == 'Penjualan') $badge_class = 'text-bg-success';
                                    if ($sumber == 'Pembelian') $badge_class = 'text-bg-warning';
                                ?>
                                <span class="badge rounded-pill <?php echo $badge_class; ?>"><?php echo $sumber; ?></span>
                            </td>
                            <td class="text-end"><?php echo number_format($jurnal['total'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <?php if ($jurnal['is_locked'] == 1): ?>
                                    <?php if ($isManagerOrAdmin): // Jika Manajer atau Admin, berikan opsi pembatalan ?>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-lock-fill"></i> Opsi
                                            </button>
                                            <ul class="dropdown-menu">
                                                <?php if ($jurnal['sumber_jurnal'] == 'Penjualan' && !empty($jurnal['id_penjualan'])): ?>
                                                    <li><a class="dropdown-item text-danger" href="<?php echo BASEURL; ?>/penjualan/hapus/<?php echo $jurnal['id_penjualan']; ?>" onclick="return confirm('Anda akan membatalkan FAKTUR PENJUALAN terkait. Lanjutkan?');">Batalkan Penjualan</a></li>
                                                <?php elseif ($jurnal['sumber_jurnal'] == 'Pembelian' && !empty($jurnal['id_pembelian'])): ?>
                                                    <li><a class="dropdown-item text-danger" href="<?php echo BASEURL; ?>/pembelian/hapus/<?php echo $jurnal['id_pembelian']; ?>" onclick="return confirm('Anda akan membatalkan FAKTUR PEMBELIAN terkait. Lanjutkan?');">Batalkan Pembelian</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    <?php else: // Jika Staff, tampilkan status terkunci ?>
                                        <span class="badge text-bg-secondary" title="Terkunci"><i class="bi bi-lock-fill"></i> Terkunci</span>
                                    <?php endif; ?>
                                <?php else: // Jika jurnal tidak terkunci (entri Jurnal Umum manual) ?>
                                    <?php if ($isManagerOrAdmin): ?>
                                        <a href="<?php echo BASEURL; ?>/jurnal/edit/<?php echo $jurnal['id_jurnal']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="<?php echo BASEURL; ?>/jurnal/hapus/<?php echo $jurnal['id_jurnal']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?');">Hapus</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
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

