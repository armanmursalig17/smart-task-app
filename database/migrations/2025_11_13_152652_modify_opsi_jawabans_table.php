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
            // 1. Tambah kolom 'tipe_opsi' untuk (teks/gambar)
            $table->enum('tipe_opsi', ['teks', 'gambar'])->default('teks')->after('soal_id');
            
            // 2. Tambah kolom 'opsi_gambar' untuk path file
            $table->string('opsi_gambar')->nullable()->after('opsi_teks');
            
            // 3. Ubah 'opsi_teks' agar boleh null (jika tipenya gambar)
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