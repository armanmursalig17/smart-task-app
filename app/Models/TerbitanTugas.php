<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TerbitanTugas extends Model
{
    use HasFactory;

   
    protected $table = 'terbitan_tugas';

  
    protected $fillable = [
        'user_id',
        'kelas_id',
        'tugas_id',
        'token',
        'durasi_menit',
        'tanggal_terbit',
       
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class, 'terbitan_tugas_id');
    }

   
    protected static function booted()
    {
        static::creating(function ($terbitan) {
           
            if (empty($terbitan->token)) {
                $terbitan->token = Str::random(10); 
            }
        });
    }
}