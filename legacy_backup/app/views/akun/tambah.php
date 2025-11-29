<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Form Tambah Data Akun</h3>
        <a href="<?php echo BASEURL; ?>/akun" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/akun/simpan" method="post">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="kode_akun" class="form-label">Kode Akun</label>
                    <input type="text" class="form-control" id="kode_akun" name="kode_akun" placeholder="Cth: 1-1101" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="nama_akun" class="form-label">Nama Akun</label>
                    <input type="text" class="form-control" id="nama_akun" name="nama_akun" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="grup_akun" class="form-label">Grup Akun</label>
                    <input type="text" class="form-control" id="grup_akun" name="grup_akun" placeholder="Cth: Aset">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="sub_grup_akun" class="form-label">Sub Grup Akun</label>
                    <input type="text" class="form-control" id="sub_grup_akun" name="sub_grup_akun" placeholder="Cth: Kas & Bank">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="level" class="form-label">Level Akun</label>
                    <select class="form-select" id="level" name="level" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4" selected>4 (Detail)</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tipe_akun" class="form-label">Tipe Akun</label>
                    <select class="form-select" id="tipe_akun" name="tipe_akun" required>
                        <option value="Detail">Detail</option>
                        <option value="Header">Header</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="posisi_saldo_normal" class="form-label">Saldo Normal</label>
                    <select class="form-select" id="posisi_saldo_normal" name="posisi_saldo_normal" required>
                        <option value="Debit">Debit</option>
                        <option value="Kredit">Kredit</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="saldo_awal" class="form-label">Saldo Awal</label>
                    <input type="number" step="0.01" class="form-control" id="saldo_awal" name="saldo_awal" value="0.00">
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="<?php echo BASEURL; ?>/akun" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

