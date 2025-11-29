<div class="card shadow-sm">
    <div class="card-header">
        <h3>Laporan Laba Rugi Komparatif</h3>
    </div>
    <div class="card-body">
        <form id="laporan-form" action="<?php echo BASEURL; ?>/laporan/labaRugi" method="post">
            <div class="row g-3">
                <div class="col-md-5 border-end">
                    <h6>Periode Laporan Utama</h6>
                    <div class="row">
                        <div class="col-md-6"><label for="tanggal_mulai_1" class="form-label">Dari Tanggal</label><input type="date" name="tanggal_mulai_1" id="tanggal_mulai_1" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_mulai_1'] ?? ''); ?>"></div>
                        <div class="col-md-6"><label for="tanggal_selesai_1" class="form-label">Sampai Tanggal</label><input type="date" name="tanggal_selesai_1" id="tanggal_selesai_1" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_selesai_1'] ?? ''); ?>"></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <h6>Periode Pembanding (Kosongkan untuk periode tunggal)</h6>
                     <div class="row">
                        <div class="col-md-6"><label for="tanggal_mulai_2" class="form-label">Dari Tanggal</label><input type="date" name="tanggal_mulai_2" id="tanggal_mulai_2" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_mulai_2'] ?? ''); ?>"></div>
                        <div class="col-md-6"><label for="tanggal_selesai_2" class="form-label">Sampai Tanggal</label><input type="date" name="tanggal_selesai_2" id="tanggal_selesai_2" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_selesai_2'] ?? ''); ?>"></div>
                    </div>
                </div>
                <div class="col-md-2 align-self-end">
                     <div class="d-flex">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                        <div class="btn-group ms-2">
                            <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Ekspor</button>
                            <ul class="dropdown-menu">
                                <li><button type="button" id="export-excel" class="dropdown-item">ke Excel</button></li>
                                <li><button type="button" id="export-pdf" class="dropdown-item">ke PDF</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Input tersembunyi untuk ekspor -->
            <input type="hidden" name="tanggal_mulai_1_export" id="tanggal_mulai_1_export">
            <input type="hidden" name="tanggal_selesai_1_export" id="tanggal_selesai_1_export">
            <input type="hidden" name="tanggal_mulai_2_export" id="tanggal_mulai_2_export">
            <input type="hidden" name="tanggal_selesai_2_export" id="tanggal_selesai_2_export">
        </form>
    </div>
</div>

<?php if ($data['laporan'] !== null): ?>
<div class="card shadow-sm mt-4">
    <div class="card-header text-center">
        <h5 class="mb-0"><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h5>
        <h6 class="mb-0">Laporan Laba Rugi <?php if(!empty($data['periode_2'])) echo "Komparatif"; ?></h6>
        <p class="mb-0">Untuk Periode yang Berakhir pada <?php echo $data['periode_1']; ?></p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Keterangan</th>
                        <th class="text-end"><?php echo $data['periode_1']; ?></th>
                        <?php if(!empty($data['periode_2'])): ?>
                            <th class="text-end"><?php echo $data['periode_2']; ?></th>
                            <th class="text-end">Perubahan (Rp)</th>
                            <th class="text-end">Perubahan (%)</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="<?php echo (!empty($data['periode_2'])) ? '5' : '2'; ?>"><strong>Pendapatan</strong></td></tr>
                    <?php 
                        foreach($data['laporan']['pendapatan'] as $item): 
                        $total1 = $item['total_1'];
                        $total2 = $item['total_2'];
                        $perubahan_rp = $total1 - $total2;
                        $perubahan_persen = ($total2 != 0) ? ($perubahan_rp / abs($total2)) * 100 : 0;
                    ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                        <td class="text-end"><?php echo number_format($total1, 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?>
                            <td class="text-end"><?php echo number_format($total2, 2, ',', '.'); ?></td>
                            <td class="text-end"><?php echo number_format($perubahan_rp, 2, ',', '.'); ?></td>
                            <td class="text-end"><span class="<?php echo ($perubahan_rp >= 0) ? 'text-success' : 'text-danger'; ?>"><?php echo ($total2 != 0) ? number_format($perubahan_persen, 2) . '%' : 'N/A'; ?></span></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold">
                        <td>Total Pendapatan</td>
                        <td class="text-end border-top"><?php echo number_format($data['laporan']['total_pendapatan_1'], 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?><td class="text-end border-top"><?php echo number_format($data['laporan']['total_pendapatan_2'], 2, ',', '.'); ?></td><td colspan="2"></td><?php endif; ?>
                    </tr>

                    <tr class="pt-3"><td colspan="<?php echo (!empty($data['periode_2'])) ? '5' : '2'; ?>"><strong>Beban-Beban</strong></td></tr>
                    <?php foreach($data['laporan']['beban'] as $item): 
                        $total1 = $item['total_1'];
                        $total2 = $item['total_2'];
                        $perubahan_rp = $total1 - $total2;
                        $perubahan_persen = ($total2 != 0) ? ($perubahan_rp / abs($total2)) * 100 : 0;
                    ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                        <td class="text-end"><?php echo number_format($total1, 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?>
                            <td class="text-end"><?php echo number_format($total2, 2, ',', '.'); ?></td>
                            <td class="text-end"><?php echo number_format($perubahan_rp, 2, ',', '.'); ?></td>
                            <td class="text-end"><span class="<?php echo ($perubahan_rp >= 0) ? 'text-danger' : 'text-success'; ?>"><?php echo ($total2 != 0) ? number_format($perubahan_persen, 2) . '%' : 'N/A'; ?></span></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold">
                        <td>Total Beban</td>
                        <td class="text-end border-top"><?php echo number_format($data['laporan']['total_beban_1'], 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?><td class="text-end border-top"><?php echo number_format($data['laporan']['total_beban_2'], 2, ',', '.'); ?></td><td colspan="2"></td><?php endif; ?>
                    </tr>
                </tbody>
                <tfoot class="table-dark">
                    <?php 
                        $labaRugi1 = $data['laporan']['total_pendapatan_1'] - $data['laporan']['total_beban_1'];
                        $labaRugi2 = $data['laporan']['total_pendapatan_2'] - $data['laporan']['total_beban_2'];
                    ?>
                    <tr class="fw-bold fs-5">
                        <td>LABA (RUGI) BERSIH</td>
                        <td class="text-end"><?php echo number_format($labaRugi1, 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?><td class="text-end"><?php echo number_format($labaRugi2, 2, ',', '.'); ?></td><td colspan="2"></td><?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Blok Tanda Tangan -->
        <div class="row mt-5" style="page-break-inside: avoid;">
            <div class="col-6 text-center">
                <p><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? ''); ?></p>
                <br><br><br><br>
                <p class="fw-bold mb-0"><u><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? ''); ?></u></p>
            </div>
            <div class="col-6 text-center">
                <p><?php echo htmlspecialchars($data['kota_laporan'] ?? 'Kota Anda'); ?>, <?php echo date('d F Y'); ?></p>
                <p><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? ''); ?></p>
                <br><br>
                <p class="fw-bold mb-0"><u><?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? ''); ?></u></p>
            </div>
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
            document.getElementById('tanggal_mulai_1_export').value = document.getElementById('tanggal_mulai_1').value;
            document.getElementById('tanggal_selesai_1_export').value = document.getElementById('tanggal_selesai_1').value;
            document.getElementById('tanggal_mulai_2_export').value = document.getElementById('tanggal_mulai_2').value;
            document.getElementById('tanggal_selesai_2_export').value = document.getElementById('tanggal_selesai_2').value;
        }

        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporLabaRugi";
                form.target = "_self";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/labaRugi";
            });
        }
        
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPdfLabaRugi";
                form.target = "_blank"; // Buka PDF di tab baru
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/labaRugi";
                form.target = "_self";
            });
        }
    });
</script>

