<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terbitan_tugas_id')->constrained('terbitan_tugas')->onDelete('cascade');
            $table->string('student_name');
            
            $table->integer('total_waktu_menit')->nullable();
            $table->integer('total_soal');
            $table->json('jawaban_json');
            
            $table->enum('status_penilaian', ['belum_dinilai', 'sebagian_dinilai', 'sudah_dinilai'])->default('belum_dinilai');
            $table->float('nilai_otomatis')->nullable();
            $table->float('nilai_uraian')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswa');
    }
};