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
    Schema::create('sopir', function (Blueprint $table) {
        $table->id();
        $table->string('kode')->unique();
        $table->string('nama');
        $table->string('telepon')->nullable();
        $table->string('no_sim')->nullable();
        $table->text('alamat')->nullable();
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
        Schema::dropIfExists('sopir');
    }
};
