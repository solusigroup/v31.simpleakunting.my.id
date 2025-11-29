<div class="card shadow-sm">
    <div class="card-header">
        <h3>Laporan Arus Kas (Metode Tidak Langsung)</h3>
    </div>
    <div class="card-body">
        <form id="laporan-form" action="<?php echo BASEURL; ?>/laporan/arusKas" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>">
                </div>
                <div class="col-md-5">
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
                                <li><button type="button" id="export-excel" class="dropdown-item disabled">ke Excel (Segera Hadir)</button></li>
                                <li><button type="button" id="export-pdf" class="dropdown-item">ke PDF</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Input tersembunyi untuk ekspor -->
            <input type="hidden" name="tanggal_mulai_export" id="tanggal_mulai_export">
            <input type="hidden" name="tanggal_selesai_export" id="tanggal_selesai_export">
        </form>
    </div>
</div>

<?php if (isset($data['laporan']) && $data['laporan'] !== null): ?>
<div class="card shadow-sm mt-4">
    <div class="card-header text-center">
        <h5 class="mb-0"><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan'] ?? 'Nama Perusahaan'); ?></h5>
        <h6 class="mb-0">Laporan Arus Kas</h6>
        <!-- **PERBAIKAN: Menggunakan variabel 'periode_1'** -->
        <p class="mb-0">Untuk Periode <?php echo htmlspecialchars($data['periode_1'] ?? ''); ?></p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <tbody>
                    <tr>
                        <td colspan="2"><strong>Arus Kas dari Aktivitas Operasi</strong></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Laba Bersih</td>
                        <td class="text-end"><?php echo number_format($data['laporan']['laba_bersih'] ?? 0, 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="ps-4"><em>Penyesuaian untuk merekonsiliasi laba bersih ke kas bersih:</em></td>
                    </tr>
                    <?php 
                        $kasDariOperasi = $data['laporan']['laba_bersih'] ?? 0;
                        foreach(($data['laporan']['penyesuaian'] ?? []) as $item): 
                        $kasDariOperasi += $item['jumlah'];
                    ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td>
                        <td class="text-end border-bottom"><?php echo number_format($item['jumlah'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold">
                        <td>Arus Kas Bersih dari Aktivitas Operasi</td>
                        <td class="text-end"><?php echo number_format($kasDariOperasi, 2, ',', '.'); ?></td>
                    </tr>
                    <!-- Aktivitas Investasi & Pendanaan (Placeholder) -->
                    <tr class="pt-3"><td colspan="2"><strong>Arus Kas dari Aktivitas Investasi</strong></td></tr>
                    <tr class="fw-bold"><td class="ps-4"><em>(Tidak ada aktivitas investasi)</em></td><td class="text-end">0.00</td></tr>
                    <tr class="pt-3"><td colspan="2"><strong>Arus Kas dari Aktivitas Pendanaan</strong></td></tr>
                    <tr class="fw-bold"><td class="ps-4"><em>(Tidak ada aktivitas pendanaan)</em></td><td class="text-end">0.00</td></tr>
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td>Kenaikan (Penurunan) Bersih Kas dan Setara Kas</td>
                        <td class="text-end"><?php echo number_format(($data['laporan']['kas_akhir'] ?? 0) - ($data['laporan']['kas_awal'] ?? 0), 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Kas dan Setara Kas di Awal Periode</td>
                        <td class="text-end"><?php echo number_format($data['laporan']['kas_awal'] ?? 0, 2, ',', '.'); ?></td>
                    </tr>
                    <tr class="table-dark fw-bold fs-5">
                        <td>KAS DAN SETARA KAS DI AKHIR PERIODE</td>
                        <td class="text-end"><?php echo number_format($data['laporan']['kas_akhir'] ?? 0, 2, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- ... (Blok Tanda Tangan) ... -->
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
        const exportPdfBtn = document.getElementById('export-pdf');

        function prepareExportData() {
            document.getElementById('tanggal_mulai_export').value = document.getElementById('tanggal_mulai').value;
            document.getElementById('tanggal_selesai_export').value = document.getElementById('tanggal_selesai').value;
        }
        
        if(exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPdfArusKas";
                form.target = "_blank";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/arusKas";
                form.target = "_self";
            });
        }
    });
</script>

