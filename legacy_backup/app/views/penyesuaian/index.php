<div class="card shadow-sm">
    <div class="card-header">
        <h3>Jurnal Penyesuaian - Beban Penyusutan Otomatis</h3>
    </div>
    <div class="card-body">
        <p class="text-muted">Modul ini akan secara otomatis menghitung dan membuat jurnal penyesuaian untuk beban penyusutan semua aset tetap yang terdaftar menggunakan metode Garis Lurus.</p>
        <form action="<?php echo BASEURL; ?>/penyesuaian/index" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="periode" class="form-label">Pilih Periode (Bulan & Tahun)</label>
                    <input type="month" name="periode" id="periode" class="form-control" value="<?php echo htmlspecialchars($data['periode']); ?>" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="preview" value="true" class="btn btn-info w-100">
                        <i class="bi bi-eye-fill"></i> Pratinjau Jurnal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($data['preview'] !== null): ?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h5 class="mb-0">Pratinjau Jurnal Penyesuaian untuk Periode <?php echo date('F Y', strtotime($data['periode'].'-01')); ?></h5>
    </div>
    <div class="card-body">
        <?php if (empty($data['preview'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> Tidak ada beban penyusutan yang perlu dicatat untuk periode ini.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i> Jurnal berikut akan dibuat secara otomatis. Pastikan data sudah benar sebelum diproses.
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Akun</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $totalDebit = 0;
                        $totalKredit = 0;
                        foreach($data['preview'] as $item): 
                        $beban = (float)($item['beban_bulanan'] ?? 0);
                        $totalDebit += $beban;
                        $totalKredit += $beban;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['akun_beban_depresiasi']); ?></td>
                            <td>Beban Penyusutan Aset</td>
                            <td class="text-end"><?php echo number_format($beban, 2, ',', '.'); ?></td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><?php echo htmlspecialchars($item['akun_akumulasi_depresiasi']); ?></td>
                            <td class="ps-4">Akumulasi Penyusutan Aset</td>
                            <td class="text-end"></td>
                            <td class="text-end"><?php echo number_format($beban, 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Total</td>
                            <td class="text-end"><?php echo number_format($totalDebit, 2, ',', '.'); ?></td>
                            <td class="text-end"><?php echo number_format($totalKredit, 2, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <form action="<?php echo BASEURL; ?>/penyesuaian/prosesDepresiasi" method="post" onsubmit="return confirm('Anda yakin ingin memproses dan membuat jurnal penyesuaian ini? Tindakan ini tidak dapat dibatalkan.');">
                    <input type="hidden" name="periode" value="<?php echo htmlspecialchars($data['periode']); ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle-fill"></i> Proses dan Buat Jurnal
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

