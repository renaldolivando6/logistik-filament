<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop kolom alamat dari pelanggan
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn('alamat');
        });

        // 2. Create tabel alamat_pelanggan
        Schema::create('alamat_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->string('label')->nullable(); // 'Kantor Pusat', 'Gudang A', etc
            $table->text('alamat_lengkap');
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('kontak_person')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('is_default')->default(false); // alamat utama
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pelanggan_id', 'aktif']);
            $table->index('is_default');
        });

        // 3. Add alamat_pelanggan_id ke surat_jalan
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->foreignId('alamat_pelanggan_id')
                ->nullable()
                ->after('pesanan_id')
                ->constrained('alamat_pelanggan')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropForeign(['alamat_pelanggan_id']);
            $table->dropColumn('alamat_pelanggan_id');
        });

        Schema::dropIfExists('alamat_pelanggan');

        Schema::table('pelanggan', function (Blueprint $table) {
            $table->text('alamat')->nullable();
        });
    }
};