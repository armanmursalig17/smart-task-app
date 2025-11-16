<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Tugas;
use App\Models\OpsiJawaban;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException; 

class TugasController extends Controller
{
    /**
     * Menampilkan form untuk membuat tugas baru.
     */
    public function create(Request $request)
    {
        $kelas_id = $request->query('kelas_id');
        if (!$kelas_id) {
            return redirect()->route('kelas.index')->with('error', 'Kelas tidak valid.');
        }
        $kelas = Kelas::find($kelas_id);
        if (!$kelas) {
            return redirect()->route('kelas.index')->with('error', 'Kelas tidak ditemukan.');
        }
        return view('tugas.create', compact('kelas'));
    }

    


    
    
    public function store(Request $request)
    {
       
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nama_tugas' => 'required|string|max:255',
            'jenis_tugas' => 'required|in:pilihan_ganda,uraian,gabungan',
            'soal' => 'required|array|min:1',
            'soal.*.pertanyaan' => 'required|string',
            'soal.*.keterangan_soal' => 'nullable|string',
            'soal.*.gambar_soal' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'soal.*.tipe_soal' => 'required|in:pilihan_ganda,uraian',

          
            'soal.*.opsi' => 'nullable|array',
            'soal.*.opsi.*.tipe' => 'required|in:teks,gambar',
            'soal.*.opsi.*.konten_teks' => 'nullable|required_if:soal.*.opsi.*.tipe,teks|string|max:255',
            'soal.*.opsi.*.konten_gambar' => 'nullable|required_if:soal.*.opsi.*.tipe,gambar|image|mimes:jpg,jpeg,png,gif|max:1024',

            'soal.*.kunci_jawaban_pg' => 'nullable|integer',
            'soal.*.kunci_jawaban_uraian' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $tugas = Tugas::create([
                'kelas_id' => $request->kelas_id,
                'nama_tugas' => $request->nama_tugas,
                'jenis_tugas' => $request->jenis_tugas,
            ]);

            foreach ($request->soal as $index => $dataSoal) {

                $tipe_soal = $dataSoal['tipe_soal'];
                $kunci_jawaban_final = null;
                $gambar_path = null;

               
                if ($request->hasFile("soal.{$index}.gambar_soal")) {
                    $file = $request->file("soal.{$index}.gambar_soal");
                    $gambar_path = $file->store('soal_images', 'public');
                }

               
                $soal = $tugas->soals()->create([
                    'pertanyaan' => $dataSoal['pertanyaan'],
                    'keterangan_soal' => $dataSoal['keterangan_soal'] ?? null,
                    'gambar_soal' => $gambar_path,
                    'tipe_soal_di_tugas' => $tipe_soal,
                ]);

              
                if ($tipe_soal == 'pilihan_ganda' && isset($dataSoal['opsi'])) {
                    foreach ($dataSoal['opsi'] as $opsiIndex => $dataOpsi) {

                        $tipe_opsi = $dataOpsi['tipe'];
                        $opsi_teks_data = null;
                        $opsi_gambar_path = null;

                        if ($tipe_opsi == 'teks') {
                            $opsi_teks_data = $dataOpsi['konten_teks'] ?? null;
                        } elseif ($tipe_opsi == 'gambar') {
                            // Cek file gambar untuk opsi
                            if ($request->hasFile("soal.{$index}.opsi.{$opsiIndex}.konten_gambar")) {
                                $fileOpsi = $request->file("soal.{$index}.opsi.{$opsiIndex}.konten_gambar");
                                $opsi_gambar_path = $fileOpsi->store('opsi_images', 'public');
                            }
                        }

                        $soal->opsiJawabans()->create([
                            'tipe_opsi' => $tipe_opsi,
                            'opsi_teks' => $opsi_teks_data,
                            'opsi_gambar' => $opsi_gambar_path,
                        ]);
                    }
                    $kunci_jawaban_final = $dataSoal['kunci_jawaban_pg'] ?? null;
                } elseif ($tipe_soal == 'uraian') {
                    $kunci_jawaban_final = $dataSoal['kunci_jawaban_uraian'] ?? null;
                }

               
                $soal->kunci_jawaban = $kunci_jawaban_final;
                $soal->save();
            }

            DB::commit();
            return redirect()->route('kelas.detail', $request->kelas_id)->with('success', 'Tugas berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat simpan tugas: '. $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan tugas. Pesan: ' . $e->getMessage());
        }
    }

    
    public function show(Tugas $tugas)
    {
        $tugas->load('soals.opsiJawabans');
        return view('tugas.soal.detail', compact('tugas'));
    }





    public function createSoal(Tugas $tugas)
    {
       
        return view('tugas.soal.create-soal', compact('tugas'));
    }

   
    public function storeSoal(Request $request, Tugas $tugas)
    {
     
        $request->validate([
           
            'soal' => 'required|array|min:1',
            'soal.0.pertanyaan' => 'required|string',
            'soal.0.keterangan_soal' => 'nullable|string',
            'soal.0.gambar_soal' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'soal.0.tipe_soal' => 'required|in:pilihan_ganda,uraian',

          
            'soal.0.opsi' => 'nullable|array',
            'soal.0.opsi.*.tipe' => 'required|in:teks,gambar',
            'soal.0.opsi.*.konten_teks' => 'nullable|required_if:soal.0.opsi.*.tipe,teks|string|max:255',
            'soal.0.opsi.*.konten_gambar' => 'nullable|required_if:soal.0.opsi.*.tipe,gambar|image|mimes:jpg,jpeg,png,gif|max:1024',

            'soal.0.kunci_jawaban_pg' => Rule::requiredIf($request->input('soal.0.tipe_soal') == 'pilihan_ganda') . '|nullable|integer',
            'soal.0.kunci_jawaban_uraian' => Rule::requiredIf($request->input('soal.0.tipe_soal') == 'uraian') . '|nullable|string',
        ]);

       

        $dataSoal = $request->soal[0]; 

        DB::beginTransaction();
        try {
            
            $tipe_soal = $dataSoal['tipe_soal'];
            $kunci_jawaban_final = null;
            $gambar_path = null;

          
            if ($request->hasFile("soal.0.gambar_soal")) { 
                $file = $request->file("soal.0.gambar_soal");
                $gambar_path = $file->store('soal_images', 'public');
            }

           
            $soal = $tugas->soals()->create([
                'pertanyaan' => $dataSoal['pertanyaan'],
                'keterangan_soal' => $dataSoal['keterangan_soal'] ?? null,
                'gambar_soal' => $gambar_path,
                'tipe_soal_di_tugas' => $tipe_soal,
            ]);

            
            if ($tipe_soal == 'pilihan_ganda' && isset($dataSoal['opsi'])) {
                foreach ($dataSoal['opsi'] as $opsiIndex => $dataOpsi) {

                    $tipe_opsi = $dataOpsi['tipe'];
                    $opsi_teks_data = null;
                    $opsi_gambar_path = null;

                    if ($tipe_opsi == 'teks') {
                        $opsi_teks_data = $dataOpsi['konten_teks'] ?? null;
                    } elseif ($tipe_opsi == 'gambar') {
                     
                        if ($request->hasFile("soal.0.opsi.{$opsiIndex}.konten_gambar")) { 
                            $fileOpsi = $request->file("soal.0.opsi.{$opsiIndex}.konten_gambar");
                            $opsi_gambar_path = $fileOpsi->store('opsi_images', 'public');
                        }
                    }

                    $soal->opsiJawabans()->create([
                        'tipe_opsi' => $tipe_opsi,
                        'opsi_teks' => $opsi_teks_data,
                        'opsi_gambar' => $opsi_gambar_path,
                    ]);
                }
                $kunci_jawaban_final = $dataSoal['kunci_jawaban_pg'] ?? null;
            } elseif ($tipe_soal == 'uraian') {
                $kunci_jawaban_final = $dataSoal['kunci_jawaban_uraian'] ?? null;
            }

         
            $soal->kunci_jawaban = $kunci_jawaban_final;
            $soal->save();
            
            DB::commit();

          
            return redirect()->route('tugas.detail', $tugas->id)
                ->with('success', 'Soal baru berhasil ditambahkan ke tugas!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat simpan soal baru: '. $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan soal. Pesan: ' . $e->getMessage());
        }
    }
  
    public function edit(Soal $soal)
    {
        $soal->load('opsiJawabans');
        return view('tugas.soal.edit', compact('soal'));
    }

    /**
     * Update soal yang ada di database.
     */
    public function update(Request $request, Soal $soal)
    {
        
        $rules = [
            'pertanyaan' => 'required|string',
            'keterangan_soal' => 'nullable|string',
            'gambar_soal_baru' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
            'hapus_gambar' => 'nullable|in:1', 
            
            'opsi' => 'nullable|array',
            'opsi.*.tipe' => 'required_with:opsi|in:teks,gambar',
            'opsi.*.konten_teks' => 'nullable|required_if:opsi.*.tipe,teks|string|max:255',
            'soal.*.opsi.*.konten_gambar' => 'nullable|required_if:soal.*.opsi.*.tipe,gambar|image|mimes:jpg,jpeg,png,gif|max:5120',
            'opsi.*.gambar_lama' => 'nullable|string',

            'kunci_jawaban_pg' => 'nullable|integer',
            'kunci_jawaban_uraian' => 'nullable|string',
        ];

        
        $validator = Validator::make($request->all(), $rules, [
            'opsi.*.konten_teks.required_if' => 'Konten teks wajib diisi untuk opsi teks.',
            'opsi.*.konten_gambar.required_without' => 'Opsi gambar wajib di-upload jika Anda mengubah tipe opsi menjadi gambar dan tidak ada gambar sebelumnya.',
        ]);

        
        if ($request->has('opsi')) {
            foreach ($request->input('opsi') as $index => $opsiItem) {
                if (isset($opsiItem['tipe']) && $opsiItem['tipe'] == 'gambar') {
                    // Jika tipe adalah gambar, konten_gambar (baru) harus ada ATAU gambar_lama harus ada
                    $validator->addRules([
                        "opsi.{$index}.konten_gambar" => "required_without:opsi.{$index}.gambar_lama"
                    ]);
                }
            }
        }

        
        $validator->validate(); 

        
        DB::beginTransaction();
        try {
            
            $soal->pertanyaan = $request->pertanyaan;
            $soal->keterangan_soal = $request->keterangan_soal;

            // Logika Update Gambar Soal
            if ($request->hasFile('gambar_soal_baru')) {
                if ($soal->gambar_soal) {
                    Storage::disk('public')->delete($soal->gambar_soal);
                }
                $soal->gambar_soal = $request->file('gambar_soal_baru')->store('soal_images', 'public');
            } elseif ($request->input('hapus_gambar') == '1') {
                if ($soal->gambar_soal) {
                    Storage::disk('public')->delete($soal->gambar_soal);
                }
                $soal->gambar_soal = null;
            }

          
            $tipe_soal = $soal->tipe_soal_di_tugas;
            if ($tipe_soal == 'pilihan_ganda') {

                // 1. Ambil path gambar opsi yang sudah ada di DB
                $existingOpsiPaths = $soal->opsiJawabans->pluck('opsi_gambar')->filter()->all();
                
                // 2. Kumpulkan path gambar lama yang masih dikirim (reused)
                $opsiToKeepPaths = [];
                if ($request->has('opsi')) {
                    foreach ($request->opsi as $dataOpsi) {
                        if (isset($dataOpsi['tipe']) && $dataOpsi['tipe'] == 'gambar' && !empty($dataOpsi['gambar_lama'])) {
                            $opsiToKeepPaths[] = $dataOpsi['gambar_lama'];
                        }
                    }
                }
                
                // 3. Hapus entri OpsiJawaban lama
                $soal->opsiJawabans()->delete();
                
                // 4. Proses opsi baru/update
                if ($request->has('opsi')) {
                    foreach ($request->opsi as $opsiIndex => $dataOpsi) {
                        $tipe_opsi = $dataOpsi['tipe'];
                        $opsi_teks_data = null;
                        $opsi_gambar_path = null;

                        if ($tipe_opsi == 'teks') {
                            $opsi_teks_data = $dataOpsi['konten_teks'] ?? null;
                        } elseif ($tipe_opsi == 'gambar') {
                            
                            if ($request->hasFile("opsi.{$opsiIndex}.konten_gambar")) {
                                $fileOpsi = $request->file("opsi.{$opsiIndex}.konten_gambar");
                                $opsi_gambar_path = $fileOpsi->store('opsi_images', 'public');
                                
                                // Jika ada upload baru, pastikan path lama tidak dimasukkan ke daftar keep
                                if (($key = array_search($dataOpsi['gambar_lama'] ?? null, $opsiToKeepPaths)) !== false) {
                                    unset($opsiToKeepPaths[$key]);
                                }
                            }
                          
                            elseif (!empty($dataOpsi['gambar_lama'])) {
                                $opsi_gambar_path = $dataOpsi['gambar_lama'];
                            }
                        }

                        $soal->opsiJawabans()->create([
                            'tipe_opsi' => $tipe_opsi,
                            'opsi_teks' => $opsi_teks_data,
                            'opsi_gambar' => $opsi_gambar_path,
                        ]);
                    }
                }
                
                // 5. Hapus gambar dari storage yang TIDAK digunakan lagi (yang ada di DB tapi tidak ada di input baru/reused)
                $opsiToDeletePaths = array_diff($existingOpsiPaths, $opsiToKeepPaths);
                foreach ($opsiToDeletePaths as $path) {
                     Storage::disk('public')->delete($path);
                }
                
                $soal->kunci_jawaban = $request->kunci_jawaban_pg ?? null;
            } elseif ($tipe_soal == 'uraian') {
                $soal->kunci_jawaban = $request->kunci_jawaban_uraian ?? null;
            }

            // Simpan semua perubahan
            $soal->save();
            DB::commit();

            return redirect()->route('tugas.detail', $soal->tugas_id)->with('success', 'Soal berhasil diperbarui.');
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat update soal: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus soal dari database.
     */
    public function destroy(Soal $soal)
    {
        try {
            $tugas_id = $soal->tugas_id;

            
            if ($soal->gambar_soal) {
                Storage::disk('public')->delete($soal->gambar_soal);
            }

          
            $soal->delete();

            return redirect()->route('tugas.detail', $tugas_id)->with('success', 'Soal berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error saat hapus soal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus soal.');
        }
    }
}