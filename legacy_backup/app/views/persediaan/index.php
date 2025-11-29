<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Master Persediaan Barang</h3>
    <div class="btn-group">
        <a href="<?php echo BASEURL; ?>/persediaan/ekspor" class="btn btn-outline-success">Ekspor ke Excel</a>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#imporModal">Impor dari Excel</button>
        <a href="<?php echo BASEURL; ?>/persediaan/tambah" class="btn btn-primary">Tambah Barang Baru</a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="text-end">Stok Awal</th>
                        <th class="text-end">Stok Terkini</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Nilai Persediaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // **PERBAIKAN: Inisialisasi variabel di luar loop**
                        $totalNilaiPersediaan = 0;
                        if (empty($data['barang'])): 
                    ?>
                        <tr><td colspan="7" class="text-center">Belum ada data barang persediaan.</td></tr>
                    <?php else: ?>
                        <?php 
                            foreach ($data['barang'] as $brg): 
                            // Hitung nilai persediaan untuk setiap baris
                            $nilaiPersediaan = ($brg['stok_saat_ini'] ?? 0) * ($brg['harga_beli'] ?? 0);
                            $totalNilaiPersediaan += $nilaiPersediaan;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($brg['kode_barang'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($brg['nama_barang'] ?? ''); ?></td>
                            <td class="text-end"><?php echo number_format($brg['stok_awal'] ?? 0, 2, ',', '.'); ?> <?php echo htmlspecialchars($brg['satuan'] ?? ''); ?></td>
                            <td class="text-end fw-bold"><?php echo number_format($brg['stok_saat_ini'] ?? 0, 2, ',', '.'); ?> <?php echo htmlspecialchars($brg['satuan'] ?? ''); ?></td>
                            <td class="text-end"><?php echo number_format($brg['harga_beli'] ?? 0, 2, ',', '.'); ?></td>
                            <td class="text-end fw-bold"><?php echo number_format($nilaiPersediaan, 2, ',', '.'); ?></td>
                            <td>
                                <a href="<?php echo BASEURL; ?>/persediaan/edit/<?php echo $brg['id_barang']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?php echo BASEURL; ?>/persediaan/hapus/<?php echo $brg['id_barang']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin? Menghapus master data akan menyebabkan error jika sudah digunakan di transaksi.');">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end">Total Nilai Persediaan</td>
                        <td class="text-end"><?php echo number_format($totalNilaiPersediaan, 2, ',', '.'); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Impor (Hanya dibuat jika user adalah Admin/Manager) -->
<?php if (Auth::isAdmin() || Auth::isManager()): ?>
<div class="modal fade" id="imporModal" tabindex="-1" aria-labelledby="imporModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imporModalLabel">Impor Data Persediaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo BASEURL; ?>/persediaan/impor" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <p>Siapkan file Excel (.xlsx) dengan kolom: <br><strong>A: Kode Barang</strong>, <strong>B: Nama Barang</strong>, <strong>C: Satuan</strong>, <strong>D: Stok Awal</strong>, <strong>E: Harga Beli</strong>, <strong>F: Harga Jual</strong>, <strong>G: Akun Persediaan</strong>, <strong>H: Akun HPP</strong>, <strong>I: Akun Penjualan</strong>.</p>
            <a href="<?php echo BASEURL; ?>/template_persediaan.xlsx" class="btn btn-link px-0" download>Unduh Template Format Excel</a>
            <hr>
            <div class="mb-3">
                <label for="file_excel" class="form-label">Pilih File Excel:</label>
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

