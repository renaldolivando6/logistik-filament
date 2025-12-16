
<?php

// =============================================================================
// MIGRATION 1: Remove jenis_muatan from pesanan
// =============================================================================
// php artisan make:migration remove_jenis_muatan_from_pesanan_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['uang_sangu', 'sisa_tagihan']);
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('uang_sangu', 15, 2)->default(0)->after('total_tagihan');
            $table->decimal('sisa_tagihan', 15, 2)->default(0)->after('uang_sangu');
        });
    }
};