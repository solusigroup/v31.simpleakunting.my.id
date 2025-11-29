<div class="card shadow-sm">
    <div class="card-header">
        <h3>Laporan Posisi Keuangan (Neraca)</h3>
    </div>
    <div class="card-body">
        <form id="laporan-form" action="<?php echo BASEURL; ?>/laporan/posisiKeuangan" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="tanggal_selesai_1" class="form-label">Tanggal Laporan Utama</label>
                    <input type="date" name="tanggal_selesai_1" id="tanggal_selesai_1" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_selesai_1'] ?? ''); ?>">
                </div>
                <div class="col-md-5">
                    <label for="tanggal_selesai_2" class="form-label">Tanggal Pembanding (Kosongkan untuk periode tunggal)</label>
                    <input type="date" name="tanggal_selesai_2" id="tanggal_selesai_2" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_selesai_2'] ?? ''); ?>">
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
            <input type="hidden" name="tanggal_selesai_1_export" id="tanggal_selesai_1_export">
            <input type="hidden" name="tanggal_selesai_2_export" id="tanggal_selesai_2_export">
        </form>
    </div>
</div>

<?php if (isset($data['laporan']) && $data['laporan'] !== null): ?>
<div class="card shadow-sm mt-4">
    <div class="card-header text-center">
        <h5 class="mb-0"><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h5>
        <h6 class="mb-0">Laporan Posisi Keuangan <?php if(!empty($data['periode_2'])) echo "Komparatif"; ?></h6>
        <p class="mb-0">Untuk Periode yang Berakhir pada <?php echo $data['periode_1'] . (!empty($data['periode_2']) ? ' dan ' . $data['periode_2'] : ''); ?></p>
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
                    <tr><td colspan="5"><strong>ASET</strong></td></tr>
                    <?php 
                        foreach($data['laporan']['periode_1']['aset'] as $item): 
                        $total1 = $item['total'];
                        $key = array_search($item['kode_akun'], array_column($data['laporan']['periode_2']['aset'] ?? [], 'kode_akun'));
                        $total2 = ($key !== false) ? $data['laporan']['periode_2']['aset'][$key]['total'] : 0;
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
                    <tr class="fw-bold"><td class="border-top">TOTAL ASET</td><td class="text-end border-top"><?php echo number_format($data['laporan']['periode_1']['total_aset'] ?? 0, 2, ',', '.'); ?></td><?php if(!empty($data['periode_2'])): ?><td class="text-end border-top"><?php echo number_format($data['laporan']['periode_2']['total_aset'] ?? 0, 2, ',', '.'); ?></td><td colspan="2"></td><?php endif; ?></tr>

                    <tr class="pt-3"><td colspan="5"><strong>KEWAJIBAN DAN EKUITAS</strong></td></tr>
                    <tr><td colspan="5" class="ps-2"><strong>Kewajiban</strong></td></tr>
                    <?php foreach($data['laporan']['periode_1']['kewajiban'] ?? [] as $item): 
                        $total1 = $item['total'];
                        $key = array_search($item['kode_akun'], array_column($data['laporan']['periode_2']['kewajiban'] ?? [], 'kode_akun'));
                        $total2 = ($key !== false) ? $data['laporan']['periode_2']['kewajiban'][$key]['total'] : 0;
                        $perubahan_rp = $total1 - $total2;
                        $perubahan_persen = ($total2 != 0) ? ($perubahan_rp / abs($total2)) * 100 : 0;
                    ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                        <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                         <?php if(!empty($data['periode_2'])): ?>
                            <td class="text-end"><?php echo number_format($total2, 2, ',', '.'); ?></td>
                            <td class="text-end"><?php echo number_format($perubahan_rp, 2, ',', '.'); ?></td>
                            <td class="text-end"><span class="<?php echo ($perubahan_rp >= 0) ? 'text-danger' : 'text-success'; ?>"><?php echo ($total2 != 0) ? number_format($perubahan_persen, 2) . '%' : 'N/A'; ?></span></td>
                         <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold"><td class="ps-4 border-top">Total Kewajiban</td><td class="text-end border-top"><?php echo number_format($data['laporan']['periode_1']['total_kewajiban'] ?? 0, 2, ',', '.'); ?></td><?php if(!empty($data['periode_2'])): ?><td class="text-end border-top"><?php echo number_format($data['laporan']['periode_2']['total_kewajiban'] ?? 0, 2, ',', '.'); ?></td><td colspan="2"></td><?php endif; ?></tr>
                    
                    <tr><td colspan="5" class="ps-2 pt-3"><strong>Ekuitas</strong></td></tr>
                    <?php foreach($data['laporan']['periode_1']['modal'] ?? [] as $item): 
                        $total1 = $item['total'];
                        $key = array_search($item['kode_akun'], array_column($data['laporan']['periode_2']['modal'] ?? [], 'kode_akun'));
                        $total2 = ($key !== false) ? $data['laporan']['periode_2']['modal'][$key]['total'] : 0;
                        $perubahan_rp = $total1 - $total2;
                        $perubahan_persen = ($total2 != 0) ? ($perubahan_rp / abs($total2)) * 100 : 0;
                    ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                        <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                         <?php if(!empty($data['periode_2'])): ?>
                            <td class="text-end"><?php echo number_format($total2, 2, ',', '.'); ?></td>
                            <td class="text-end"><?php echo number_format($perubahan_rp, 2, ',', '.'); ?></td>
                            <td class="text-end"><span class="<?php echo ($perubahan_rp >= 0) ? 'text-success' : 'text-danger'; ?>"><?php echo ($total2 != 0) ? number_format($perubahan_persen, 2) . '%' : 'N/A'; ?></span></td>
                         <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold"><td class="ps-4 border-top">Total Ekuitas</td><td class="text-end border-top"><?php echo number_format($data['laporan']['periode_1']['total_modal'] ?? 0, 2, ',', '.'); ?></td><?php if(!empty($data['periode_2'])): ?><td class="text-end border-top"><?php echo number_format($data['laporan']['periode_2']['total_modal'] ?? 0, 2, ',', '.'); ?></td><td colspan="2"></td><?php endif; ?></tr>
                </tbody>
                <tfoot class="table-dark fw-bold">
                    <tr>
                        <td>TOTAL KEWAJIBAN DAN EKUITAS</td>
                        <td class="text-end"><?php echo number_format(($data['laporan']['periode_1']['total_kewajiban'] ?? 0) + ($data['laporan']['periode_1']['total_modal'] ?? 0), 2, ',', '.'); ?></td>
                        <?php if(!empty($data['periode_2'])): ?><td class="text-end"><?php echo number_format(($data['laporan']['periode_2']['total_kewajiban'] ?? 0) + ($data['laporan']['periode_2']['total_modal'] ?? 0), 2, ',', '.'); ?></td><td colspan="2"></td><?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
                <!-- Blok Tanda Tangan -->
        <div class="row mt-5" style="page-break-inside: avoid;">
            <div class="col-6 text-center">
                <br>
                <p><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? ''); ?></p>
                <br><br><br>
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
            document.getElementById('tanggal_selesai_1_export').value = document.getElementById('tanggal_selesai_1').value;
            document.getElementById('tanggal_selesai_2_export').value = document.getElementById('tanggal_selesai_2').value;
        }

        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPosisiKeuangan";
                form.target = "_self";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/posisiKeuangan";
            });
        }
        
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPdfPosisiKeuangan";
                form.target = "_blank";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/posisiKeuangan";
                form.target = "_self";
            });
        }
    });
</script>