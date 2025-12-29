<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'jasa' option
        DB::statement("ALTER TABLE perusahaan MODIFY COLUMN jenis_usaha ENUM('dagang', 'simpan_pinjam', 'serba_usaha', 'jasa') DEFAULT 'dagang'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'jasa' from enum (revert to original)
        DB::statement("ALTER TABLE perusahaan MODIFY COLUMN jenis_usaha ENUM('dagang', 'simpan_pinjam', 'serba_usaha') DEFAULT 'dagang'");
    }
};
