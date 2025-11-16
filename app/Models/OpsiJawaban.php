<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OpsiJawaban extends Model
{
    use HasFactory;

    protected $table = 'opsi_jawabans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'soal_id',
        'tipe_opsi',
        'opsi_teks',
        'opsi_gambar',
    ];

   
    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

   
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($opsi) {
           
            if ($opsi->opsi_gambar) {
                Storage::disk('public')->delete($opsi->opsi_gambar);
            }
        });
    }
}