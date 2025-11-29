<div class="row">
    <!-- Opsi Tutup Buku Bulanan -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-info text-white">
                <h4><i class="bi bi-calendar-month"></i> Tutup Buku Bulanan</h4>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="flex-grow-1">Proses ini akan menutup akun pendapatan & beban hanya untuk bulan yang dipilih dan memindahkan laba/rugi bulan tersebut ke Laba Ditahan.</p>
                <!-- **PERBAIKAN: Form action menunjuk ke /tutupBuku/index** -->
                <form action="<?php echo BASEURL; ?>/tutupBuku/index" method="post">
                    <input type="hidden" name="tipe_proses" value="Bulanan">
                    <div class="mb-3">
                        <label for="periode_bulanan" class="form-label">Pilih Periode Bulan & Tahun</label>
                        <input type="month" name="periode" id="periode_bulanan" class="form-control" value="<?php echo htmlspecialchars($data['periode_bulanan_next'] ?? ''); ?>" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info">Pratinjau Jurnal Penutup Bulanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Opsi Tutup Buku Tahunan -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h4><i class="bi bi-calendar-date"></i> Tutup Buku Tahunan</h4>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="flex-grow-1">Proses ini akan menutup total saldo akumulatif akun pendapatan & beban selama setahun penuh. Ini adalah proses akhir tahun standar.</p>
                <!-- **PERBAIKAN: Form action menunjuk ke /tutupBuku/index** -->
                <form action="<?php echo BASEURL; ?>/tutupBuku/index" method="post">
                    <input type="hidden" name="tipe_proses" value="Tahunan">
                    <div class="mb-3">
                        <label for="periode_tahunan" class="form-label">Pilih Tahun</label>
                        <select name="periode" id="periode_tahunan" class="form-select" required>
                            <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Pratinjau Jurnal Penutup Tahunan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Area Pratinjau Jurnal -->
<?php if (isset($data['preview']) && $data['preview'] !== null): ?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h5 class="mb-0">Pratinjau Jurnal Penutup <?php echo htmlspecialchars($data['tipe_proses'] ?? ''); ?> untuk Periode <?php echo htmlspecialchars($data['periode_label'] ?? ''); ?></h5>
    </div>
    <div class="card-body">
        <?php 
            $totalPendapatan = $data['preview']['total_pendapatan_1'] ?? 0;
            $totalBeban = $data['preview']['total_beban_1'] ?? 0;
            $labaRugi = $totalPendapatan - $totalBeban;
        ?>
        <div class="alert alert-warning">
            <p class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Proses</p>
            <p class="mb-1">Jurnal berikut akan dibuat secara otomatis. Total Laba (Rugi) Bersih yang akan dipindahkan ke Laba Ditahan adalah <strong><?php echo number_format($labaRugi, 2, ',', '.'); ?></strong>.</p>
            <p class="mb-0"><strong>Perhatian:</strong> Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <form action="<?php echo BASEURL; ?>/tutupBuku/proses" method="post" onsubmit="return confirm('ANDA YAKIN INGIN MELANJUTKAN PROSES TUTUP BUKU?');">
                <input type="hidden" name="periode" value="<?php echo htmlspecialchars($data['periode_raw'] ?? ''); ?>">
                <input type="hidden" name="tipe_proses" value="<?php echo htmlspecialchars($data['tipe_proses'] ?? ''); ?>">
                <a href="<?php echo BASEURL; ?>/tutupBuku" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle-fill"></i> Ya, Proses dan Tutup Buku
                </button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

