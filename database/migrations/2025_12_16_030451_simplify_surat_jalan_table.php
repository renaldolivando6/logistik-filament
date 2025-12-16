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
        // Drop kolom yang tidak perlu
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_muat',
                'nama_penerima',
                'ttd_penerima'
            ]);
        });
        
        // Update enum status (hapus 'dimuat')
        DB::statement("ALTER TABLE surat_jalan MODIFY COLUMN status ENUM('draft', 'dikirim', 'diterima') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->date('tanggal_muat')->after('pesanan_id');
            $table->string('nama_penerima')->nullable()->after('tonase_dikirim');
            $table->text('ttd_penerima')->nullable()->after('nama_penerima');
        });
        
        DB::statement("ALTER TABLE surat_jalan MODIFY COLUMN status ENUM('draft', 'dimuat', 'dikirim', 'diterima') DEFAULT 'draft'");
    }
};