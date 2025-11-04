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
    Schema::create('uang_sangu', function (Blueprint $table) {
        $table->id();
        $table->string('nomor_sangu')->unique();
        $table->date('tanggal_sangu');
        $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->cascadeOnDelete();
        $table->foreignId('sopir_id')->constrained('sopir')->cascadeOnDelete(); // tanpa s
        $table->foreignId('kendaraan_id')->constrained('kendaraan')->cascadeOnDelete(); // tanpa s
        $table->decimal('jumlah', 15, 2);
        $table->text('catatan')->nullable();
        $table->enum('status', ['menunggu', 'disetujui', 'selesai'])->default('menunggu');
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_sangu');
    }
};
