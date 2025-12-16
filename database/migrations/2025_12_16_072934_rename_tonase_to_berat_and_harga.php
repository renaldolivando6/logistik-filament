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
        // RUTE: harga_per_ton → harga_per_kg
        Schema::table('rute', function (Blueprint $table) {
            $table->renameColumn('harga_per_ton', 'harga_per_kg');
        });
        
        // PESANAN: tonase → berat, harga_per_ton → harga_per_kg
        Schema::table('pesanan', function (Blueprint $table) {
            $table->renameColumn('tonase', 'berat');
            $table->renameColumn('harga_per_ton', 'harga_per_kg');
        });
        
        // SURAT_JALAN: tonase_dikirim → berat_dikirim
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->renameColumn('tonase_dikirim', 'berat_dikirim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->renameColumn('berat_dikirim', 'tonase_dikirim');
        });
        
        Schema::table('pesanan', function (Blueprint $table) {
            $table->renameColumn('berat', 'tonase');
            $table->renameColumn('harga_per_kg', 'harga_per_ton');
        });
        
        Schema::table('rute', function (Blueprint $table) {
            $table->renameColumn('harga_per_kg', 'harga_per_ton');
        });
    }
};