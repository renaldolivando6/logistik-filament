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
        Schema::table('uang_sangu', function (Blueprint $table) {
            // Make pesanan_id nullable (karena sekarang bisa link ke trip)
            $table->foreignId('pesanan_id')->nullable()->change();
            
            // Add trip_id
            $table->foreignId('trip_id')->nullable()->after('pesanan_id')->constrained('trip')->onDelete('cascade');
            
            // Add index
            $table->index('trip_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_sangu', function (Blueprint $table) {
            // Drop foreign key & column
            $table->dropForeign(['trip_id']);
            $table->dropColumn('trip_id');
            
            // Make pesanan_id NOT NULL again
            $table->foreignId('pesanan_id')->nullable(false)->change();
        });
    }
};