<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Penerimaan: <?php echo htmlspecialchars($data['penerimaan']['no_bukti']); ?></h4>
        <div>
            <a href="<?php echo BASEURL; ?>/penerimaan" class="btn btn-secondary btn-sm">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Bukti</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Diterima Dari:</h5>
                <p class="mb-1"><strong><?php echo htmlspecialchars($data['penerimaan']['nama_pelanggan']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($data['penerimaan']['alamat_pelanggan'] ?? '')); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($data['penerimaan']['tanggal'])); ?></p>
                <p class="mb-1"><strong>Setor Ke Akun:</strong> <?php echo htmlspecialchars($data['penerimaan']['nama_akun_kas']); ?></p>
            </div>
        </div>

        <h5>Detail Pembayaran Faktur</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal Faktur</th>
                        <th class="text-end">Jumlah Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['penerimaan']['details'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['no_faktur']); ?></td>
                        <td><?php echo ($item['tanggal_faktur'] !== 'N/A') ? date('d M Y', strtotime($item['tanggal_faktur'])) : 'N/A'; ?></td>
                        <td class="text-end"><?php echo number_format($item['jumlah_bayar'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="2" class="text-end"><h4>Total Diterima</h4></th>
                        <th class="text-end"><h4><?php echo number_format($data['penerimaan']['total_diterima'], 2, ',', '.'); ?></h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php if(!empty($data['penerimaan']['keterangan'])): ?>
        <div class="mt-4">
            <strong>Keterangan:</strong>
            <p><?php echo nl2br(htmlspecialchars($data['penerimaan']['keterangan'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

