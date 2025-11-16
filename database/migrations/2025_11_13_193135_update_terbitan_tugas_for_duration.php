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
        Schema::table('terbitan_tugas', function (Blueprint $table) {
           
            $table->dropColumn('tanggal_deadline');           
            $table->integer('durasi_menit')->nullable()->after('tanggal_terbit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terbitan_tugas', function (Blueprint $table) {
           
            $table->timestamp('tanggal_deadline')->nullable();
            
           
            $table->dropColumn('durasi_menit');
        });
    }
};