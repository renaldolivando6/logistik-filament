<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_operasional', function (Blueprint $table) {
            // Add trip_id as nullable
            $table->foreignId('trip_id')
                ->nullable()
                ->after('tanggal_biaya')
                ->constrained('trip')
                ->onDelete('cascade');
            
            // Make pesanan_id and kendaraan_id nullable
            $table->unsignedBigInteger('pesanan_id')->nullable()->change();
            $table->unsignedBigInteger('kendaraan_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_operasional', function (Blueprint $table) {
            $table->dropForeign(['trip_id']);
            $table->dropColumn('trip_id');
            
            // Restore to NOT NULL
            $table->unsignedBigInteger('pesanan_id')->nullable(false)->change();
            $table->unsignedBigInteger('kendaraan_id')->nullable(false)->change();
        });
    }
};