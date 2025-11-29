<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Posisi Keuangan</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        h1, h2, p { margin: 0; padding: 0; }
        h1 { font-size: 16px; }
        h2 { font-size: 14px; margin-top: 4px; }
        p { font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 5px; text-align: left; }
        .text-end { text-align: right; }
        .ps-4 { padding-left: 15px !important; }
        .fw-bold { font-weight: bold; }
        .border-top { border-top: 1px solid #000; }
        .border-bottom { border-bottom: 2px solid #000; }
        .signature-table { width: 100%; margin-top: 60px; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan'] ?? ''); ?></h1>
        <h2>Laporan Posisi Keuangan</h2>
        <p>Per <?php echo htmlspecialchars($data['periode_1'] ?? ''); ?></p>
    </div>

    <div style="width: 48%; float: left;">
        <table>
            <thead>
                <tr>
                    <th class="border-bottom">ASET</th>
                    <th class="border-bottom text-end"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['laporan']['periode_1']['aset'])): ?>
                    <?php foreach($data['laporan']['periode_1']['aset'] as $item): ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                        <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot style="border-top: 2px solid #000; border-bottom: 2px solid #000; background-color: #e9ecef;">
                <tr class="fw-bold">
                    <td>TOTAL ASET</td>
                    <td class="text-end"><?php echo number_format($data['laporan']['periode_1']['total_aset'] ?? 0, 2, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="width: 48%; float: right;">
        <table>
            <thead>
                <tr>
                    <th class="border-bottom">KEWAJIBAN DAN EKUITAS</th>
                    <th class="border-bottom text-end"></th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="2"><strong>Kewajiban</strong></td></tr>
                <?php if (!empty($data['laporan']['periode_1']['kewajiban'])): ?>
                    <?php foreach($data['laporan']['periode_1']['kewajiban'] as $item): ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                        <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr class="fw-bold">
                    <td class="ps-4">Total Kewajiban</td>
                    <td class="text-end border-top"><?php echo number_format($data['laporan']['periode_1']['total_kewajiban'] ?? 0, 2, ',', '.'); ?></td>
                </tr>
                
                <tr><td colspan="2" style="padding-top: 10px;"><strong>Ekuitas</strong></td></tr>
                <?php if (!empty($data['laporan']['periode_1']['modal'])): ?>
                    <?php foreach($data['laporan']['periode_1']['modal'] as $item): ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                        <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr class="fw-bold">
                    <td class="ps-4">Total Ekuitas</td>
                    <td class="text-end border-top"><?php echo number_format($data['laporan']['periode_1']['total_modal'] ?? 0, 2, ',', '.'); ?></td>
                </tr>
            </tbody>
            <tfoot style="border-top: 2px solid #000; border-bottom: 2px solid #000; background-color: #e9ecef;">
                <tr class="fw-bold">
                    <td>TOTAL KEWAJIBAN DAN EKUITAS</td>
                    <td class="text-end"><?php echo number_format(($data['laporan']['periode_1']['total_kewajiban'] ?? 0) + ($data['laporan']['periode_1']['total_modal'] ?? 0), 2, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div style="clear: both;"></div>

    <!-- Blok Tanda Tangan -->
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

