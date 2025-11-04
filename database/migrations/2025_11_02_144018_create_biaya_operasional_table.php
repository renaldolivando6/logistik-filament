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
    Schema::create('biaya_operasional', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal_biaya');
        $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
        $table->foreignId('kendaraan_id')->constrained('kendaraan')->cascadeOnDelete(); // tanpa s
        $table->foreignId('kategori_biaya_id')->constrained('kategori_biaya')->cascadeOnDelete();
        $table->decimal('jumlah', 15, 2);
        $table->text('keterangan')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_operasional');
    }
};
