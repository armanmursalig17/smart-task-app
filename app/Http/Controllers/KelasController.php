<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // FILTERING: Hanya ambil kelas yang dimiliki oleh user yang sedang login
        $kelas = Kelas::where('user_id', Auth::id())
                        ->orderBy('id', 'asc')
                        ->get(); 
        
        return view('kelasm.kelas', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
        ], [
            'nama_kelas.required' => 'Nama kelas tidak boleh kosong.',
        ]);

        // Pengecekan unik berdasarkan user_id (Unik per user)
        $existingKelas = Kelas::where('user_id', Auth::id())
                              ->where('nama_kelas', $request->nama_kelas)
                              ->exists();
        
        if ($existingKelas) {
            return redirect()->back()->withErrors(['nama_kelas' => 'Anda sudah memiliki kelas dengan nama ini.']);
        }

        // Simpan data dengan menyertakan user_id
        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'user_id' => Auth::id(), // SIMPAN user_id dari user yang login
        ]);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Cari kelas dan pastikan kelas tersebut dimiliki oleh user yang login (Authorization check)
        $kelas = Kelas::where('user_id', Auth::id())->findOrFail($id);

        // Validasi input
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:255',
                // Pengecekan unik per user, kecuali kelas yang sedang diedit
                Rule::unique('kelas')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($kelas->id),
            ],
        ], [
            'nama_kelas.required' => 'Nama kelas tidak boleh kosong.',
            'nama_kelas.unique' => 'Anda sudah memiliki kelas dengan nama ini.',
        ]);
        
        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
        ]);

        // Redirect ke index kelas, bukan tugas
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Cari kelas dan pastikan kelas tersebut dimiliki oleh user yang login (Authorization check)
        $kelas = Kelas::where('user_id', Auth::id())->findOrFail($id);
        $kelas->delete();

        // Redirect ke index kelas, bukan tugas
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
    
   

    public function create() {}
    public function show(string $id) {}
    public function edit(string $id) {}
}