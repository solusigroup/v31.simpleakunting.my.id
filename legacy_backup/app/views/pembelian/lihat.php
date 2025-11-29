<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Faktur Pembelian: <?php echo htmlspecialchars($data['pembelian']['no_faktur_pembelian']); ?></h4>
        <div>
            <a href="<?php echo BASEURL; ?>/pembelian" class="btn btn-secondary btn-sm">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Faktur</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Dibeli Dari:</h5>
                <p class="mb-1"><strong><?php echo htmlspecialchars($data['pembelian']['nama_pemasok']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($data['pembelian']['alamat_pemasok'] ?? '')); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Tanggal Faktur:</strong> <?php echo date('d M Y', strtotime($data['pembelian']['tanggal_faktur'])); ?></p>
                <p class="mb-1"><strong>Jatuh Tempo:</strong> <?php echo $data['pembelian']['jatuh_tempo'] ? date('d M Y', strtotime($data['pembelian']['jatuh_tempo'])) : '-'; ?></p>
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
                    <?php foreach($data['pembelian']['details'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                        <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['kuantitas']); ?></td>
                        <td class="text-end"><?php echo number_format($item['harga'], 2, ',', '.'); ?></td>
                        <td class="text-end"><?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="4" class="text-end"><h4>Total</h4></th>
                        <th class="text-end"><h4><?php echo number_format($data['pembelian']['total'], 2, ',', '.'); ?></h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php if(!empty($data['pembelian']['keterangan'])): ?>
        <div class="mt-4">
            <strong>Keterangan:</strong>
            <p><?php echo nl2br(htmlspecialchars($data['pembelian']['keterangan'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

