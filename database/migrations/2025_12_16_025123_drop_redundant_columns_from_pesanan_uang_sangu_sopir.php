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
        // ✅ Raw SQL - Skip if not exists
        
        // PESANAN
        if (Schema::hasColumn('pesanan', 'nomor_pesanan')) {
            DB::statement('ALTER TABLE pesanan DROP INDEX IF EXISTS pesanan_nomor_pesanan_unique');
            DB::statement('ALTER TABLE pesanan DROP COLUMN nomor_pesanan');
        }
        
        // UANG_SANGU
        if (Schema::hasColumn('uang_sangu', 'nomor_sangu')) {
            DB::statement('ALTER TABLE uang_sangu DROP INDEX IF EXISTS uang_sangu_nomor_sangu_unique');
            DB::statement('ALTER TABLE uang_sangu DROP COLUMN nomor_sangu');
        }
        
        // SOPIR
        if (Schema::hasColumn('sopir', 'kode')) {
            DB::statement('ALTER TABLE sopir DROP INDEX IF EXISTS sopir_kode_unique');
            DB::statement('ALTER TABLE sopir DROP COLUMN kode');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('nomor_pesanan')->unique()->after('id');
        });
        
        Schema::table('uang_sangu', function (Blueprint $table) {
            $table->string('nomor_sangu')->unique()->after('id');
        });
        
        Schema::table('sopir', function (Blueprint $table) {
            $table->string('kode')->unique()->after('id');
        });
    }
};