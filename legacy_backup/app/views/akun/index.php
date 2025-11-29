<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Akun (Chart of Accounts)</h3>
    <div class="btn-group">
        <a href="<?php echo BASEURL; ?>/akun/ekspor" class="btn btn-outline-success">Ekspor ke Excel</a>
        <?php if (Auth::isAdmin() || Auth::isManager()): // Tampilkan tombol hanya untuk Admin & Manajer ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#imporModal">Impor</button>
            <a href="<?php echo BASEURL; ?>/akun/tambah" class="btn btn-primary">Tambah Akun Baru</a>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Kode Akun</th>
                        <th>Nama Akun</th>
                        <th>Tipe</th>
                        <th>Saldo Normal</th>
                        <th class="text-end">Saldo Awal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['akun'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada data akun.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['akun'] as $akun): ?>
                        <tr>
                            <td>
                                <span style="padding-left: <?php echo (strlen(preg_replace("/[^0-9]/", "", $akun['kode_akun'])) - 1) * 20 ?>px;">
                                    <?php echo htmlspecialchars($akun['kode_akun']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo ($akun['tipe_akun'] == 'Header') ? '<strong>' : '' ?>
                                <?php echo htmlspecialchars($akun['nama_akun']); ?>
                                <?php echo ($akun['tipe_akun'] == 'Header') ? '</strong>' : '' ?>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?php echo ($akun['tipe_akun'] == 'Header') ? 'text-bg-secondary' : 'text-bg-info' ?>">
                                    <?php echo htmlspecialchars($akun['tipe_akun']); ?>
                                </span>
                            </td>
                             <td>
                                <span class="badge rounded-pill <?php echo ($akun['posisi_saldo_normal'] == 'Debit') ? 'text-bg-primary' : 'text-bg-warning' ?>">
                                    <?php echo htmlspecialchars($akun['posisi_saldo_normal']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?php echo number_format($akun['saldo_awal'], 2, ',', '.'); ?>
                            </td>
                            <td class="text-end">
                                <?php if (Auth::isAdmin() || Auth::isManager()): // Tampilkan tombol hanya untuk Admin & Manajer ?>
                                    <a href="<?php echo BASEURL; ?>/akun/edit/<?php echo $akun['kode_akun']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?php echo BASEURL; ?>/akun/hapus/<?php echo $akun['kode_akun']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Form Impor Excel (Hanya dibuat jika user adalah Admin/Manager) -->
<?php if (Auth::isAdmin() || Auth::isManager()): ?>
<div class="modal fade" id="imporModal" tabindex="-1" aria-labelledby="imporModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imporModalLabel">Impor Data Akun dari Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo BASEURL; ?>/akun/impor" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <p>Siapkan file Excel Anda (.xlsx) dengan kolom: <br><strong>A: Kode Akun</strong>, <strong>B: Nama Akun</strong>, <strong>C: Tipe</strong>, <strong>D: Saldo Normal</strong>, <strong>E: Saldo Awal</strong>.</p>
            <a href="<?php echo BASEURL; ?>/template_akun_baru.xlsx" class="btn btn-link px-0" download>Unduh Template Format Excel</a>
            <hr>
            <div class="mb-3">
                <label for="file_excel" class="form-label">Pilih file Excel:</label>
                <input class="form-control" type="file" name="file_excel" id="file_excel" accept=".xlsx" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Mulai Proses Impor</button>
          </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

