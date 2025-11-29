<div class="card shadow-sm">
    <div class="card-header">
        <h3>Analisis Rasio Keuangan</h3>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/analisis" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>">
                </div>
                <div class="col-md-5">
                    <label for="tanggal_selesai" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Analisis</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="mt-4">
    <h4 class="mb-3">Hasil Analisis untuk Periode: <?php echo $data['periode']; ?></h4>
    
    <!-- Rasio Likuiditas -->
    <h5><i class="bi bi-droplet-fill text-primary"></i> Rasio Likuiditas</h5>
    <p class="text-muted">Mengukur kemampuan perusahaan membayar utang jangka pendeknya.</p>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Rasio Lancar (Current Ratio)</h6>
                    <h4 class="card-title"><?php echo number_format($data['rasio']['likuiditas']['rasio_lancar'], 2); ?> : 1</h4>
                    <small>Aset Lancar / Utang Lancar. Idealnya > 1.5.</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Rasio Cepat (Quick Ratio)</h6>
                    <h4 class="card-title"><?php echo number_format($data['rasio']['likuiditas']['rasio_cepat'], 2); ?> : 1</h4>
                    <small>(Aset Lancar - Persediaan) / Utang Lancar. Idealnya > 1.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Rasio Profitabilitas -->
    <h5 class="mt-4"><i class="bi bi-graph-up-arrow text-success"></i> Rasio Profitabilitas</h5>
    <p class="text-muted">Mengukur kemampuan perusahaan menghasilkan laba.</p>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Margin Laba Bersih (NPM)</h6>
                    <h4 class="card-title"><?php echo number_format($data['rasio']['profitabilitas']['npm'], 2); ?> %</h4>
                    <small>Laba Bersih / Total Pendapatan.</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Return on Assets (ROA)</h6>
                    <h4 class="card-title"><?php echo number_format($data['rasio']['profitabilitas']['roa'], 2); ?> %</h4>
                    <small>Laba Bersih / Total Aset.</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Return on Equity (ROE)</h6>
                    <h4 class="card-title"><?php echo number_format($data['rasio']['profitabilitas']['roe'], 2); ?> %</h4>
                    <small>Laba Bersih / Total Ekuitas.</small>
                </div>
            </div>
        </div>
    </div>
</div>

