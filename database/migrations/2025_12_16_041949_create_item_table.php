<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create item table
        Schema::create('item', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('satuan')->default('Ton');
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nama');
        });
        
        // 2. Populate item dari rute.jenis_muatan yang sudah ada
        DB::statement("
            INSERT INTO item (nama, satuan, aktif, created_at, updated_at)
            SELECT DISTINCT jenis_muatan, 'Ton', 1, NOW(), NOW()
            FROM rute
            WHERE jenis_muatan IS NOT NULL
            ORDER BY jenis_muatan
        ");
        
        // 3. Add item_id to rute
        Schema::table('rute', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->after('tujuan')
                ->constrained('item')->onDelete('cascade');
        });
        
        // 4. Populate rute.item_id dari jenis_muatan
        DB::statement("
            UPDATE rute r
            INNER JOIN item i ON r.jenis_muatan = i.nama
            SET r.item_id = i.id
        ");
        
        // 5. Make item_id required
        Schema::table('rute', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable(false)->change();
        });
        
        // 6. Drop jenis_muatan dari rute
        Schema::table('rute', function (Blueprint $table) {
            $table->dropColumn('jenis_muatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore jenis_muatan
        Schema::table('rute', function (Blueprint $table) {
            $table->string('jenis_muatan')->after('tujuan');
        });
        
        // Populate jenis_muatan dari item
        DB::statement("
            UPDATE rute r
            INNER JOIN item i ON r.item_id = i.id
            SET r.jenis_muatan = i.nama
        ");
        
        // Drop item_id
        Schema::table('rute', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
        });
        
        // Drop item table
        Schema::dropIfExists('item');
    }
};