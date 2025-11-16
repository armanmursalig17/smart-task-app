<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    use HasFactory;

    protected $table = 'jawaban_siswa';

    protected $fillable = [
        'terbitan_tugas_id',
        'student_name',
        'total_waktu_menit',
        'total_soal',
        'jawaban_json', 
        'status_penilaian',
        'nilai_otomatis',
        'nilai_uraian',
    ];

    protected $casts = [
        'jawaban_json' => 'array',
    ];

    public function terbitanTugas()
    {
        return $this->belongsTo(TerbitanTugas::class);
    }
}