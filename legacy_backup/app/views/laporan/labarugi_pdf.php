<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1, h2, h3 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px; }
        .text-end { text-align: right; }
        .ps-4 { padding-left: 20px; }
        .fw-bold { font-weight: bold; }
        .border-top { border-top: 1px solid #333; }
        .table-dark { background-color: #f2f2f2; }
        .signature { margin-top: 50px; page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h1>
        <h2>Laporan Laba Rugi</h2>
        <p>Untuk Periode <?php echo $data['periode_1']; ?></p>
    </div>
    <table>
        <!-- (Isi tabel Laba Rugi sama seperti view utama, tapi tanpa kolom komparatif) -->
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
    </table>
    <!-- (Blok Tanda Tangan) -->
     <table class="signature-table">
    <tbody>
        <tr>
            <td>
                <p><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? '(Jabatan 1)'); ?></p>
                <br><br><br><br>
                <p style="font-weight: bold; text-decoration: underline; margin: 0; padding: 0;">
                    <?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? '(Nama Penandatangan 1)'); ?>
                </p>
            </td>
            <td>
                <p><?php echo htmlspecialchars($data['kota_laporan'] ?? 'Kota Anda'); ?>, <?php echo date('d F Y'); ?></p>
                <p><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? '(Jabatan 2)'); ?></p>
                <br><br><br><br>
                <p style="font-weight: bold; text-decoration: underline; margin: 0; padding: 0;">
                    <?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? '(Nama Penandatangan 2)'); ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
