<div class="card shadow-sm">
    <div class="card-header">
        <h3>Laporan Perubahan Ekuitas</h3>
    </div>
    <div class="card-body">
        <form id="laporan-form" action="<?php echo BASEURL; ?>/laporan/perubahanEkuitas" method="post">
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

<?php if (isset($data['laporan']) && $data['laporan'] !== null): ?>
<div class="card shadow-sm mt-4">
    <div class="card-header text-center">
        <h5 class="mb-0"><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h5>
        <h6 class="mb-0">Laporan Perubahan Ekuitas <?php if(!empty($data['periode_2'])) echo "Komparatif"; ?></h6>
        <p class="mb-0">Untuk Periode yang Dibandingkan</p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm" style="max-width: 800px; margin: auto;">
                <thead class="table-light">
                    <tr>
                        <th>Keterangan</th>
                        <th class="text-end"><?php echo $data['periode_1']; ?></th>
                        <?php if(!empty($data['periode_2'])): ?><th class="text-end"><?php echo $data['periode_2']; ?></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Modal Awal Periode</td>
                        <td class="text-end"><?php echo number_format($data['laporan']['periode_1']['modal_awal'] ?? 0, 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?><td class="text-end"><?php echo number_format($data['laporan']['periode_2']['modal_awal'] ?? 0, 2, ',', '.'); ?></td><?php endif; ?>
                    </tr>
                    <tr>
                        <td class="ps-4">Setoran (Penarikan) Modal</td>
                        <td class="text-end"><?php echo number_format($data['laporan']['periode_1']['perubahan_modal_langsung'] ?? 0, 2, ',', '.'); ?></td>
                         <?php if(!empty($data['periode_2'])): ?><td class="text-end"><?php echo number_format($data['laporan']['periode_2']['perubahan_modal_langsung'] ?? 0, 2, ',', '.'); ?></td><?php endif; ?>
                    </tr>
                    <tr>
                        <td class="ps-4">Laba (Rugi) Periode Berjalan</td>
                        <td class="text-end border-bottom"><?php echo number_format($data['laporan']['periode_1']['laba_rugi_periode_berjalan'] ?? 0, 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?><td class="text-end border-bottom"><?php echo number_format($data['laporan']['periode_2']['laba_rugi_periode_berjalan'] ?? 0, 2, ',', '.'); ?></td><?php endif; ?>
                    </tr>
                </tbody>
                <tfoot class="table-dark">
                    <tr class="fw-bold fs-5">
                        <td>MODAL AKHIR PERIODE</td>
                        <td class="text-end"><?php echo number_format($data['laporan']['periode_1']['modal_akhir'] ?? 0, 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?><td class="text-end"><?php echo number_format($data['laporan']['periode_2']['modal_akhir'] ?? 0, 2, ',', '.'); ?></td><?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Blok Tanda Tangan -->
        <div class="row mt-5" style="page-break-inside: avoid;">
            <div class="col-6 text-center">
                <p><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? ''); ?></p><br><br><br>
                <p class="fw-bold mb-0"><u><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? ''); ?></u></p>
            </div>
            <div class="col-6 text-center">
                <p><?php echo htmlspecialchars($data['kota_laporan'] ?? 'Kota Anda'); ?>, <?php echo date('d F Y'); ?></p>
                <p><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? ''); ?></p><br><br><br>
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
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPerubahanEkuitas";
                form.target = "_self";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/perubahanEkuitas";
            });
        }

        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPdfPerubahanEkuitas";
                form.target = "_blank";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/perubahanEkuitas";
                form.target = "_self";
            });
        }
    });
</script>

