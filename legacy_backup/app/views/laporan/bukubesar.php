<div class="card shadow-sm">
    <div class="card-header">
        <h3>Buku Besar (General Ledger)</h3>
    </div>
    <div class="card-body">
        <form id="laporan-form" action="<?php echo BASEURL; ?>/laporan/bukuBesar" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="kode_akun" class="form-label">Pilih Akun</label>
                    <select class="form-select" name="kode_akun" id="kode_akun" required>
                        <option value="">-- Pilih Akun Detail --</option>
                        <?php foreach($data['akun'] as $akun): ?>
                            <?php if($akun['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $akun['kode_akun'] ?>" <?php echo (($data['kode_akun_terpilih'] ?? '') == $akun['kode_akun']) ? 'selected' : '' ?>>
                                    <?php echo $akun['kode_akun'] . ' - ' . $akun['nama_akun'] ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="tanggal_selesai" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                        <div class="btn-group ms-2">
                            <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Ekspor
                            </button>
                            <ul class="dropdown-menu">
                                <li><button type="button" id="export-excel" class="dropdown-item">ke Excel</button></li>
                                <li><button type="button" id="export-pdf" class="dropdown-item">ke PDF</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Input tersembunyi untuk ekspor -->
            <input type="hidden" name="kode_akun_export" id="kode_akun_export">
            <input type="hidden" name="tanggal_mulai_export" id="tanggal_mulai_export">
            <input type="hidden" name="tanggal_selesai_export" id="tanggal_selesai_export">
        </form>
    </div>
</div>

<?php if (isset($data['laporan']) && $data['laporan'] !== null): ?>
    <div class="card shadow-sm mt-4">
        <div class="card-header text-center">
            <h5 class="mb-0"><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan'] ?? 'Nama Perusahaan'); ?></h5>
            <h6 class="mb-0">Laporan Buku Besar</h6>
            <p class="mb-0">Akun: [<?php echo htmlspecialchars($data['kode_akun_terpilih'] ?? ''); ?>] <?php echo htmlspecialchars($data['nama_akun_terpilih'] ?? ''); ?></p>
            <small class="text-muted">Periode: <?php echo htmlspecialchars($data['periode_1'] ?? ''); ?></small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Transaksi</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5"><strong>Saldo Awal Periode</strong></td>
                            <td class="text-end"><strong><?php echo number_format($data['laporan']['saldo_awal_periode'] ?? 0, 2, ',', '.') ?></strong></td>
                        </tr>
                        <?php
                            $saldo = $data['laporan']['saldo_awal_periode'] ?? 0;
                            foreach(($data['laporan']['transaksi'] ?? []) as $row):
                                if (($data['laporan']['posisi_saldo_normal'] ?? 'Debit') == 'Debit') {
                                    $saldo = $saldo + $row['debit'] - $row['kredit'];
                                } else { // Kredit
                                    $saldo = $saldo - $row['debit'] + $row['kredit'];
                                }
                        ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td><?php echo htmlspecialchars($row['no_transaksi']) ?></td>
                            <td><?php echo htmlspecialchars($row['deskripsi']) ?></td>
                            <td class="text-end"><?php echo ($row['debit'] > 0) ? number_format($row['debit'], 2, ',', '.') : '-' ?></td>
                            <td class="text-end"><?php echo ($row['kredit'] > 0) ? number_format($row['kredit'], 2, ',', '.') : '-' ?></td>
                            <td class="text-end"><?php echo number_format($saldo, 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                     <tfoot class="table-dark">
                        <tr>
                            <td colspan="5"><strong>Saldo Akhir Periode</strong></td>
                            <td class="text-end"><strong><?php echo number_format($saldo, 2, ',', '.'); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('laporan-form');
        const exportExcelBtn = document.getElementById('export-excel');
        const exportPdfBtn = document.getElementById('export-pdf');

        function prepareExportData() {
            document.getElementById('kode_akun_export').value = document.getElementById('kode_akun').value;
            document.getElementById('tanggal_mulai_export').value = document.getElementById('tanggal_mulai').value;
            document.getElementById('tanggal_selesai_export').value = document.getElementById('tanggal_selesai').value;
        }

        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporBukuBesar";
                form.target = "_self";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/bukuBesar";
            });
        }
        
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPdfBukuBesar";
                form.target = "_blank";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/bukuBesar";
                form.target = "_self";
            });
        }
    });
</script>

