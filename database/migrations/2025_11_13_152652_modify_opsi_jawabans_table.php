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
        Schema::table('opsi_jawabans', function (Blueprint $table) {
           
            $table->enum('tipe_opsi', ['teks', 'gambar'])->default('teks')->after('soal_id');
            
         
            $table->string('opsi_gambar')->nullable()->after('opsi_teks');
            
         
            $table->text('opsi_teks')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opsi_jawabans', function (Blueprint $table) {
           
            $table->dropColumn(['tipe_opsi', 'opsi_gambar']);
            
           
            $table->text('opsi_teks')->nullable(false)->change();
        });
    }
};