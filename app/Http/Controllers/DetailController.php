<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    /**
     * Menampilkan halaman detail untuk kelas tertentu.
     */
    public function show($id)
    {
        $kelas = Kelas::with(['tugas' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);



        return view('kelasm.detail', compact('kelas'));
    }
}
