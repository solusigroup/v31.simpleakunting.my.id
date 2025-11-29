<!DOCTYPE html>
<html>
<head>
    <title>Perubahan Ekuitas</title>
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
        <h2>Laporan Perubahan Ekuitas</h2>
        <p>Untuk Periode <?php echo $data['periode_1']; ?></p>
    </div>
    <table>
        <!-- (Isi tabel Arus Kas sama seperti view utama, tapi tanpa kolom komparatif) -->
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
