    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Pembayaran Pemasok</h3>
        <a href="<?php echo BASEURL; ?>/pembayaran/tambah" class="btn btn-primary">Catat Pembayaran</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr><th>Tanggal</th><th>No. Bukti</th><th>Pemasok</th><th class="text-end">Total Dibayar</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($data['pembayaran'] as $pby): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($pby['tanggal'])); ?></td>
                        <td><?php echo htmlspecialchars($pby['no_bukti']); ?></td>
                        <td><?php echo htmlspecialchars($pby['nama_pemasok']); ?></td>
                        <td class="text-end"><?php echo number_format($pby['total_dibayar'], 2, ',', '.'); ?></td>
                        <td><a href="#" class="btn btn-sm btn-info disabled">Lihat</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
