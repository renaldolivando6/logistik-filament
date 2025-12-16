
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
        // Add uang_sangu columns to trip table
        Schema::table('trip', function (Blueprint $table) {
            $table->decimal('uang_sangu', 15, 2)->default(0)->after('kendaraan_id');
            $table->text('catatan_sangu')->nullable()->after('uang_sangu');
        });
        
        // Migrate existing data from uang_sangu to trip
        DB::statement("
            UPDATE trip t
            LEFT JOIN uang_sangu us ON us.trip_id = t.id
            SET t.uang_sangu = COALESCE(us.jumlah, 0),
                t.catatan_sangu = us.catatan
            WHERE us.id IS NOT NULL
        ");
        
        // Drop uang_sangu table
        Schema::dropIfExists('uang_sangu');
    }

    public function down(): void
    {
        // Recreate uang_sangu table
        Schema::create('uang_sangu', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_sangu');
            $table->foreignId('trip_id')->nullable()->constrained('trip')->onDelete('cascade');
            $table->foreignId('sopir_id')->constrained('sopir')->onDelete('cascade');
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->text('catatan')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'selesai'])->default('menunggu');
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Migrate data back from trip to uang_sangu
        DB::statement("
            INSERT INTO uang_sangu (tanggal_sangu, trip_id, sopir_id, kendaraan_id, jumlah, catatan, status, created_at, updated_at)
            SELECT 
                tanggal_trip,
                id as trip_id,
                sopir_id,
                kendaraan_id,
                uang_sangu,
                catatan_sangu,
                'selesai',
                created_at,
                updated_at
            FROM trip
            WHERE uang_sangu > 0
        ");
        
        // Remove columns from trip
        Schema::table('trip', function (Blueprint $table) {
            $table->dropColumn(['uang_sangu', 'catatan_sangu']);
        });
    }
};