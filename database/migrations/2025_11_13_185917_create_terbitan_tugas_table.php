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
        Schema::create('terbitan_tugas', function (Blueprint $table) {
            $table->id();
           
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            
            $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
            
            $table->string('token')->unique(); 
            $table->timestamp('tanggal_terbit')->useCurrent(); 
            $table->timestamp('tanggal_deadline')->nullable(); 
            $table->enum('status', ['aktif', 'ditutup'])->default('aktif');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terbitan_tugas');
    }
};