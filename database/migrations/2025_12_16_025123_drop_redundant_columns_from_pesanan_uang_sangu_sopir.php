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
        // ✅ Safe drop with try-catch
        
        // PESANAN
        if (Schema::hasColumn('pesanan', 'nomor_pesanan')) {
            try {
                DB::statement('ALTER TABLE pesanan DROP INDEX pesanan_nomor_pesanan_unique');
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            Schema::table('pesanan', function (Blueprint $table) {
                $table->dropColumn('nomor_pesanan');
            });
        }
        
        // UANG_SANGU
        if (Schema::hasColumn('uang_sangu', 'nomor_sangu')) {
            try {
                DB::statement('ALTER TABLE uang_sangu DROP INDEX uang_sangu_nomor_sangu_unique');
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            Schema::table('uang_sangu', function (Blueprint $table) {
                $table->dropColumn('nomor_sangu');
            });
        }
        
        // SOPIR
        if (Schema::hasColumn('sopir', 'kode')) {
            try {
                DB::statement('ALTER TABLE sopir DROP INDEX sopir_kode_unique');
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            Schema::table('sopir', function (Blueprint $table) {
                $table->dropColumn('kode');
            });
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