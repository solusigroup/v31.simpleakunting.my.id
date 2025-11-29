<div class="card shadow-sm">
    <div class="card-header">
        <h3>Neraca Lajur (Worksheet)</h3>
    </div>
    <div class="card-body">
        <form id="laporan-form" action="<?php echo BASEURL; ?>/laporan/neracaLajur" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-4"><label for="tanggal_mulai" class="form-label">Dari Tanggal</label><input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>"></div>
                <div class="col-md-4"><label for="tanggal_selesai" class="form-label">Sampai Tanggal</label><input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>"></div>
                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Tampilkan</button></div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header text-center">
        <h5 class="mb-0"><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h5>
        <h6 class="mb-0">Neraca Lajur</h6>
        <p class="mb-0">Untuk Periode <?php echo htmlspecialchars($data['periode_1'] ?? ''); ?></p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="font-size: 0.8em;">
                <thead class="table-dark text-center align-middle">
                    <tr>
                        <th rowspan="2">Kode Akun</th>
                        <th rowspan="2">Nama Akun</th>
                        <th colspan="2">Saldo Awal</th>
                        <th colspan="2">Mutasi Periode</th>
                        <th colspan="2">Neraca Saldo</th>
                        <th colspan="2">Penyesuaian</th>
                        <th colspan="2">NS Disesuaikan</th>
                        <th colspan="2">Laba Rugi</th>
                        <th colspan="2">Posisi Keuangan</th>
                    </tr>
                    <tr>
                        <th>Debit</th><th>Kredit</th>
                        <th>Debit</th><th>Kredit</th>
                        <th>Debit</th><th>Kredit</th>
                        <th>Debit</th><th>Kredit</th>
                        <th>Debit</th><th>Kredit</th>
                        <th>Debit</th><th>Kredit</th>
                        <th>Debit</th><th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $totals = array_fill_keys(['sa_debit', 'sa_kredit', 'mutasi_debit', 'mutasi_kredit', 'ns_debit', 'ns_kredit', 'penyesuaian_debit', 'penyesuaian_kredit', 'nsd_debit', 'nsd_kredit', 'lr_debit', 'lr_kredit', 'poskeu_debit', 'poskeu_kredit'], 0);
                        if (!empty($data['laporan'])):
                            foreach($data['laporan'] as $row):
                                foreach($totals as $key => &$total) { $total += $row[$key]; }
                    ?>
                    <tr>
                        <td><?php echo $row['kode_akun']; ?></td>
                        <td><?php echo $row['nama_akun']; ?></td>
                        <td class="text-end"><?php echo ($row['sa_debit'] != 0) ? number_format($row['sa_debit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['sa_kredit'] != 0) ? number_format($row['sa_kredit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['mutasi_debit'] != 0) ? number_format($row['mutasi_debit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['mutasi_kredit'] != 0) ? number_format($row['mutasi_kredit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['ns_debit'] != 0) ? number_format($row['ns_debit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['ns_kredit'] != 0) ? number_format($row['ns_kredit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['penyesuaian_debit'] != 0) ? number_format($row['penyesuaian_debit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['penyesuaian_kredit'] != 0) ? number_format($row['penyesuaian_kredit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['nsd_debit'] != 0) ? number_format($row['nsd_debit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['nsd_kredit'] != 0) ? number_format($row['nsd_kredit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['lr_debit'] != 0) ? number_format($row['lr_debit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['lr_kredit'] != 0) ? number_format($row['lr_kredit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['poskeu_debit'] != 0) ? number_format($row['poskeu_debit'], 2) : ''; ?></td>
                        <td class="text-end"><?php echo ($row['poskeu_kredit'] != 0) ? number_format($row['poskeu_kredit'], 2) : ''; ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">Total</td>
                        <?php foreach($totals as $total): ?>
                        <td class="text-end"><?php echo number_format($total, 2); ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php 
                        $labaRugi = $totals['lr_kredit'] - $totals['lr_debit'];
                    ?>
                    <tr>
                        <td colspan="12" class="text-end">Laba (Rugi) Bersih</td>
                        <?php if ($labaRugi >= 0): ?>
                            <td class="text-end"><?php echo number_format($labaRugi, 2); ?></td><td></td>
                            <td></td><td class="text-end"><?php echo number_format($labaRugi, 2); ?></td>
                        <?php else: ?>
                            <td></td><td class="text-end"><?php echo number_format(abs($labaRugi), 2); ?></td>
                            <td class="text-end"><?php echo number_format(abs($labaRugi), 2); ?></td><td></td>
                        <?php endif; ?>
                    </tr>
                    <tr class="table-dark">
                        <td colspan="12" class="text-end">Total Akhir</td>
                        <td class="text-end"><?php echo number_format($totals['lr_debit'] + (($labaRugi >= 0) ? $labaRugi : 0), 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['lr_kredit'] + (($labaRugi < 0) ? abs($labaRugi) : 0), 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['poskeu_debit'] + (($labaRugi < 0) ? abs($labaRugi) : 0), 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['poskeu_kredit'] + (($labaRugi >= 0) ? $labaRugi : 0), 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

