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
    Schema::create('kendaraan', function (Blueprint $table) {
        $table->id();
        $table->string('nopol')->unique();
        $table->string('jenis')->nullable();
        $table->integer('kapasitas')->nullable(); // dalam ton
        $table->string('merk')->nullable();
        $table->year('tahun')->nullable();
        $table->boolean('aktif')->default(true);
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
