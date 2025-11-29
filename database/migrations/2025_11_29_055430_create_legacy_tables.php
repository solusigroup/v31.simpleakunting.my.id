<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Akun
        Schema::create('akun', function (Blueprint $table) {
            $table->string('kode_akun', 20)->primary();
            $table->string('nama_akun');
            $table->string('tipe_akun');
            $table->string('saldo_normal', 10); // Debit/Kredit
            $table->timestamps();
        });

        // 2. Tabel Perusahaan
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('akun_piutang_default')->nullable();
            $table->string('akun_utang_default')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Pelanggan
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->string('nama_pelanggan');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->decimal('saldo_awal_piutang', 15, 2)->default(0);
            $table->decimal('saldo_terkini_piutang', 15, 2)->default(0);
            $table->timestamps();
        });

        // 4. Tabel Pemasok
        Schema::create('pemasok', function (Blueprint $table) {
            $table->id('id_pemasok');
            $table->string('nama_pemasok');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->decimal('saldo_awal_hutang', 15, 2)->default(0);
            $table->decimal('saldo_terkini_hutang', 15, 2)->default(0);
            $table->timestamps();
        });

        // 5. Tabel Master Persediaan
        Schema::create('master_persediaan', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('satuan');
            $table->decimal('stok_awal', 10, 2)->default(0);
            $table->decimal('stok_saat_ini', 10, 2)->default(0);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->string('akun_persediaan')->nullable();
            $table->string('akun_hpp')->nullable();
            $table->string('akun_penjualan')->nullable();
            $table->timestamps();
        });

        // 6. Tabel Jurnal Umum
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id('id_jurnal');
            $table->string('no_transaksi');
            $table->date('tanggal');
            $table->text('deskripsi')->nullable();
            $table->string('sumber_jurnal');
            $table->boolean('is_locked')->default(0);
            $table->timestamps();
        });

        // 7. Tabel Jurnal Detail
        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_jurnal');
            $table->string('kode_akun');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->timestamps();

            // Foreign Keys (Optional, but good for integrity)
            // $table->foreign('id_jurnal')->references('id_jurnal')->on('jurnal_umum')->onDelete('cascade');
            // $table->foreign('kode_akun')->references('kode_akun')->on('akun');
        });

        // 8. Tabel Penjualan
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_jurnal')->nullable();
            $table->string('no_faktur');
            $table->date('tanggal_faktur');
            $table->decimal('total', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('metode_pembayaran'); // Tunai/Kredit
            $table->string('akun_kas_bank')->nullable();
            $table->decimal('sisa_tagihan', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('Belum Lunas');
            $table->timestamps();
        });

        // 9. Tabel Penjualan Detail
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_penjualan');
            $table->unsignedBigInteger('id_barang');
            $table->decimal('kuantitas', 10, 2);
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->string('akun_pendapatan')->nullable();
            $table->timestamps();
        });

        // 10. Tabel Pembelian
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->unsignedBigInteger('id_pemasok');
            $table->unsignedBigInteger('id_jurnal')->nullable();
            $table->string('no_faktur_pembelian');
            $table->date('tanggal_faktur');
            $table->decimal('total', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('metode_pembayaran'); // Tunai/Kredit
            $table->string('akun_kas_bank')->nullable();
            $table->decimal('sisa_tagihan', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('Belum Lunas');
            $table->timestamps();
        });

        // 11. Tabel Pembelian Detail
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_pembelian');
            $table->unsignedBigInteger('id_barang');
            $table->decimal('kuantitas', 10, 2);
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->string('akun_beban_persediaan')->nullable();
            $table->timestamps();
        });

        // 12. Tabel Kartu Stok
        Schema::create('kartu_stok', function (Blueprint $table) {
            $table->id('id_kartu');
            $table->unsignedBigInteger('id_barang');
            $table->string('tipe_transaksi'); // IN/OUT
            $table->decimal('kuantitas', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_tables');
    }
};
