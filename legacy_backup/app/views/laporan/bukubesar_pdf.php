<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Besar</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        h1, h2, h3, p { margin: 0; padding: 0; }
        h1 { font-size: 16px; }
        h2 { font-size: 14px; margin-top: 4px; }
        h3 { font-size: 12px; margin-top: 4px; }
        p { font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 5px; text-align: left; border: 1px solid #ddd; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .table-dark { background-color: #e9ecef; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan'] ?? ''); ?></h1>
        <h2>Laporan Buku Besar</h2>
        <h3>Akun: [<?php echo htmlspecialchars($data['kode_akun_terpilih'] ?? ''); ?>] <?php echo htmlspecialchars($data['nama_akun_terpilih'] ?? ''); ?></h3>
        <p>Untuk Periode <?php echo htmlspecialchars($data['periode_1'] ?? ''); ?></p>
    </div>

    <table>
        <thead class="table-dark">
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
                    } else {
                        $saldo = $saldo - $row['debit'] + $row['kredit'];
                    }
            ?>
            <tr>
                <td><?php echo date('d M Y', strtotime($row['tanggal'])) ?></td>
                <td><?php echo htmlspecialchars($row['no_transaksi']) ?></td>
                <td><?php echo htmlspecialchars($row['deskripsi']) ?></td>
                <td class="text-end"><?php echo ($row['debit'] > 0) ? number_format($row['debit'], 2, ',', '.') : '' ?></td>
                <td class="text-end"><?php echo ($row['kredit'] > 0) ? number_format($row['kredit'], 2, ',', '.') : '' ?></td>
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
</body>
</html>

