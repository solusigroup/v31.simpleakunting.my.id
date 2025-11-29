<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        h1, h2, p { margin: 0; padding: 0; }
        h1 { font-size: 18px; }
        h2 { font-size: 16px; margin-top: 5px; }
        p { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 6px 8px; text-align: left; }
        .text-end { text-align: right; }
        .ps-4 { padding-left: 20px !important; }
        .fw-bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px solid #000; }
        .table-dark { background-color: #e9ecef; border-top: 2px solid #000; border-bottom: 2px solid #000; }
        .table-light { background-color: #f8f9fa; }
        .signature-block { margin-top: 60px; page-break-inside: avoid; width: 100%; }
        .signature-col { width: 45%; display: inline-block; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h1>
        <h2>Laporan Arus Kas</h2>
        <p>Untuk Periode <?php echo htmlspecialchars($data['periode_1']); ?></p>
    </div>

    <table>
        <tbody>
            <tr>
                <td colspan="2"><strong>Arus Kas dari Aktivitas Operasi</strong></td>
            </tr>
            <tr>
                <td class="ps-4">Laba Bersih</td>
                <td class="text-end"><?php echo number_format($data['laporan']['laba_bersih'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="2" class="ps-4"><em>Penyesuaian untuk merekonsiliasi laba bersih ke kas bersih:</em></td>
            </tr>
            <?php 
                $kasDariOperasi = $data['laporan']['laba_bersih'];
                foreach($data['laporan']['penyesuaian'] as $item): 
                $kasDariOperasi += $item['jumlah'];
            ?>
            <tr>
                <td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td>
                <td class="text-end"><?php echo number_format($item['jumlah'], 2, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
             <tr>
                <td></td>
                <td class="text-end border-bottom"></td>
            </tr>
            <tr class="fw-bold">
                <td>Arus Kas Bersih dari Aktivitas Operasi</td>
                <td class="text-end"><?php echo number_format($kasDariOperasi, 2, ',', '.'); ?></td>
            </tr>
            
            <!-- Aktivitas Investasi & Pendanaan (Placeholder) -->
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2"><strong>Arus Kas dari Aktivitas Investasi</strong></td></tr>
            <tr class="fw-bold"><td class="ps-4"><em>(Tidak ada aktivitas investasi)</em></td><td class="text-end border-bottom">0.00</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2"><strong>Arus Kas dari Aktivitas Pendanaan</strong></td></tr>
            <tr class="fw-bold"><td class="ps-4"><em>(Tidak ada aktivitas pendanaan)</em></td><td class="text-end border-bottom">0.00</td></tr>
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-bold">
                <td>Kenaikan (Penurunan) Bersih Kas dan Setara Kas</td>
                <td class="text-end"><?php echo number_format($data['laporan']['kas_akhir'] - $data['laporan']['kas_awal'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Kas dan Setara Kas di Awal Periode</td>
                <td class="text-end"><?php echo number_format($data['laporan']['kas_awal'], 2, ',', '.'); ?></td>
            </tr>
            <tr class="table-dark fw-bold">
                <td>KAS DAN SETARA KAS DI AKHIR PERIODE</td>
                <td class="text-end"><?php echo number_format($data['laporan']['kas_akhir'], 2, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

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
