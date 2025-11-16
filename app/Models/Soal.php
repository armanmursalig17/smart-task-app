<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Soal extends Model
{
    use HasFactory;

    protected $table = 'soals';

    protected $fillable = [
        'tugas_id',
        'pertanyaan',
        'gambar_soal',
        'keterangan_soal',
        'tipe_soal_di_tugas',
        'kunci_jawaban',
    ];

    /**
     * Relasi ke Tugas
     */
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    /**
     * Relasi ke OpsiJawaban
     */
    public function opsiJawabans()
    {
        return $this->hasMany(OpsiJawaban::class);
    }

    /**
     * Accessor untuk menampilkan kunci jawaban yang mudah dibaca.
     */
    protected function kunciJawabanDisplay(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $tipe_soal = $attributes['tipe_soal_di_tugas'];
                $kunci_jawaban = $attributes['kunci_jawaban'];

              
                if ($tipe_soal == 'uraian') {
                    return $kunci_jawaban;
                }

              
                if ($tipe_soal == 'pilihan_ganda') {
                    
                  
                    if (is_null($kunci_jawaban) || $kunci_jawaban === '') {
                        return 'N/A'; // Tidak ada kunci jawaban
                    }

                   
                    $correctIndex = (int) $kunci_jawaban;

                 
                    return chr(65 + $correctIndex);
                }

                return $kunci_jawaban; 
            },
        );
    }
}