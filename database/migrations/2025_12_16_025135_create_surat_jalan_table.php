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
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trip')->onDelete('cascade');
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade');
            
            // Detail Pengiriman
            $table->date('tanggal_muat');
            $table->date('tanggal_kirim')->nullable();
            $table->date('tanggal_terima')->nullable();
            
            // Quantity (untuk split delivery)
            $table->decimal('tonase_dikirim', 10, 2);
            
            // Penerima
            $table->string('nama_penerima')->nullable();
            $table->text('ttd_penerima')->nullable(); // Base64 signature image
            
            // Status & Notes
            $table->enum('status', ['draft', 'dimuat', 'dikirim', 'diterima'])->default('draft');
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('tanggal_muat');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};