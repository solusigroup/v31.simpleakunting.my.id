<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Faktur: <?php echo htmlspecialchars($data['penjualan']['no_faktur']); ?></h4>
        <div>
            <a href="<?php echo BASEURL; ?>/penjualan" class="btn btn-secondary btn-sm">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Faktur</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Ditagihkan Kepada:</h5>
                <p class="mb-1"><strong><?php echo htmlspecialchars($data['penjualan']['nama_pelanggan']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($data['penjualan']['alamat_pelanggan'] ?? '')); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Tanggal Faktur:</strong> <?php echo date('d M Y', strtotime($data['penjualan']['tanggal_faktur'])); ?></p>
                <p class="mb-1"><strong>Jatuh Tempo:</strong> <?php echo $data['penjualan']['jatuh_tempo'] ? date('d M Y', strtotime($data['penjualan']['jatuh_tempo'])) : '-'; ?></p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Kuantitas</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['penjualan']['details'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($item['nama_barang']); ?>
                            <br><small class="text-muted">(Akun Pendapatan: <?php echo htmlspecialchars($item['nama_akun']); ?>)</small>
                        </td>
                        <td class="text-center"><?php echo htmlspecialchars($item['kuantitas']); ?></td>
                        <td class="text-end"><?php echo number_format($item['harga'], 2, ',', '.'); ?></td>
                        <td class="text-end"><?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="4" class="text-end"><h4>Total</h4></th>
                        <th class="text-end"><h4><?php echo number_format($data['penjualan']['total'], 2, ',', '.'); ?></h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php if(!empty($data['penjualan']['keterangan'])): ?>
        <div class="mt-4">
            <strong>Keterangan:</strong>
            <p><?php echo nl2br(htmlspecialchars($data['penjualan']['keterangan'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

