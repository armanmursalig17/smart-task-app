<?php

namespace App\Models;

use App\Models\JawabanSiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'kelas_id',
        'nama_tugas',
        'jenis_tugas',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function soals()
    {
        return $this->hasMany(Soal::class);
    }

     public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class, 'terbitan_tugas_id');
    }

    
}