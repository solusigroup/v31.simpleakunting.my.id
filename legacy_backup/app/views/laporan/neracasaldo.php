<div class="card shadow-sm">
    <div class="card-header">
        <h3>Neraca Saldo (Trial Balance)</h3>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/laporan/neracasaldo" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="tanggal_selesai" class="form-label">Per Tanggal</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo $data['tanggal_selesai']; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan Laporan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h5 class="mb-0">Neraca Saldo</h5>
        <small class="text-muted">Periode per tanggal: <?php echo $data['periode_1'] ?></small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Kode Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $totalDebit = 0;
                        $totalKredit = 0;
                        if (!empty($data['laporan'])):
                            foreach($data['laporan'] as $row):
                                $totalDebit += $row['debit'];
                                $totalKredit += $row['kredit'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['kode_akun']) ?></td>
                        <td><?php echo htmlspecialchars($row['nama_akun']) ?></td>
                        <td class="text-end"><?php echo ($row['debit'] > 0) ? number_format($row['debit'], 2, ',', '.') : '-' ?></td>
                        <td class="text-end"><?php echo ($row['kredit'] > 0) ? number_format($row['kredit'], 2, ',', '.') : '-' ?></td>
                    </tr>
                    <?php 
                            endforeach;
                        endif;
                    ?>
                </tbody>
                 <tfoot class="table-dark">
                    <tr>
                        <th colspan="2" class="text-end">Total</th>
                        <th class="text-end"><?php echo number_format($totalDebit, 2, ',', '.') ?></th>
                        <th class="text-end"><?php echo number_format($totalKredit, 2, ',', '.') ?></th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-end">Status</th>
                        <th colspan="2" class="text-center">
                            <?php if (number_format($totalDebit, 2) == number_format($totalKredit, 2)): ?>
                                <span class="badge text-bg-success">BALANCE</span>
                            <?php else: ?>
                                <span class="badge text-bg-danger">UNBALANCE</span>
                            <?php endif; ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
      <div class="row mt-5" style="page-break-inside: avoid;">
           <div class="col-6 text-center">
            <br>
               <p><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? ''); ?></p>
               <br><br><br><br>
               <p class="fw-bold mb-0"><u><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? ''); ?></u></p>
           </div>
           <div class="col-6 text-center">
               <p><?php echo htmlspecialchars($data['kota_laporan'] ?? 'Kota Anda'); ?>, <?php echo date('d F Y'); ?></p>
               <p><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? ''); ?>
            </p><br><br><br>
               <p class="fw-bold mb-0"><u><?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? ''); ?></u></p>
        </div>
     </div>