<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            
            $table->string('gambar_soal')->nullable()->after('pertanyaan');
            $table->text('keterangan_soal')->nullable()->after('gambar_soal');
        });
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn(['gambar_soal', 'keterangan_soal']);
        });
    }
};