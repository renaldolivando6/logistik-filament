<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip', function (Blueprint $table) {
            $table->decimal('uang_kembali', 15, 2)->default(0)->after('uang_sangu');
            $table->enum('status_sangu', ['belum_selesai', 'selesai'])->default('belum_selesai')->after('uang_kembali');
            $table->date('tanggal_pengembalian')->nullable()->after('status_sangu');
        });
    }

    public function down(): void
    {
        Schema::table('trip', function (Blueprint $table) {
            $table->dropColumn(['uang_kembali', 'status_sangu', 'tanggal_pengembalian']);
        });
    }
};