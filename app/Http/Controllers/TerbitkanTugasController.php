<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Tugas;
use Illuminate\Support\Str;
use App\Models\JawabanSiswa;

use Illuminate\Http\Request;
use App\Models\TerbitanTugas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class TerbitkanTugasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

       
        $terbitanAktif = TerbitanTugas::where('user_id', $userId)
            ->where('status', 'aktif')
            ->with(['kelas', 'tugas'])
            ->latest()
            ->paginate(10, ['*'], 'aktifPage'); 
      
        $terbitanDitutup = TerbitanTugas::where('user_id', $userId)
            ->where('status', 'ditutup')
            ->with(['kelas', 'tugas'])
            ->latest()
            ->get(); 

        return view('terbitkantugas.index', compact('terbitanAktif', 'terbitanDitutup'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $kelas = Kelas::where('user_id', Auth::id())->get();

       
        $tugasSudahTerbitIds = TerbitanTugas::where('user_id', Auth::id())
            ->pluck('tugas_id')
            ->toArray();

        
        $tugas = Tugas::whereHas('kelas', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->whereNotIn('id', $tugasSudahTerbitIds) 
            ->get();

        return view('terbitkantugas.create', compact('kelas', 'tugas'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tugas_id' => 'required|exists:tugas,id',
            'durasi_menit' => 'nullable|integer|min:1',
        ]);

        $kelasMilikUser = Kelas::where('id', $request->kelas_id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$kelasMilikUser) {
            return redirect()->back()->withErrors(['kelas_id' => 'Kelas yang dipilih tidak valid atau bukan milik Anda.']);
        }


        $terbitan = TerbitanTugas::create([
            'user_id' => Auth::id(),
            'kelas_id' => $request->kelas_id,
            'tugas_id' => $request->tugas_id,
            'durasi_menit' => $request->durasi_menit,
            'status' => 'aktif',
        ]);


        return redirect()->route('terbitkantugas.index')
            ->with('success', 'Tugas berhasil diterbitkan! Token akses: ' . $terbitan->token);
    }

    public function reUse(string $id)
    {

        $terbitanLama = TerbitanTugas::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();


        $terbitanLama->update(['status' => 'ditutup']);

        $terbitanBaru = TerbitanTugas::create([
            'user_id' => Auth::id(),
            'kelas_id' => $terbitanLama->kelas_id,
            'tugas_id' => $terbitanLama->tugas_id,
            'durasi_menit' => $terbitanLama->durasi_menit,
            'status' => 'aktif',
        ]);

        return redirect()->route('terbitkantugas.index')
            ->with('success', 'Tugas berhasil diaktifkan kembali! Token akses **BARU** Anda adalah: ' . $terbitanBaru->token);
    }

    public function deactivate(string $id)
    {
        $terbitan = TerbitanTugas::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->firstOrFail();

        $terbitan->update(['status' => 'ditutup']);

        return redirect()->route('terbitkantugas.index')
            ->with('success', 'Tugas berhasil dinonaktifkan dan statusnya diubah menjadi Expired.');
    }



    public function show(string $id)
    {
        $terbitan = TerbitanTugas::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['kelas', 'tugas.soals', 'jawabanSiswa'])
            ->firstOrFail();

        $totalSoal = $terbitan->tugas->soals->count();


        $filteredJawabanSiswa = $terbitan->jawabanSiswa
            ->sortByDesc('created_at')
            ->groupBy('student_name')
            ->map(function (Collection $items) {
                return $items->first();
            })
            ->values();


        $terbitan->setRelation('jawabanSiswa', $filteredJawabanSiswa);

        return view('terbitkantugas.show', compact('terbitan', 'totalSoal'));
    }


    public function showNilai(string $id)
    {
        $terbitan = TerbitanTugas::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['kelas', 'tugas', 'jawabanSiswa'])
            ->firstOrFail();


        $filteredJawabanSiswa = $terbitan->jawabanSiswa
            ->sortByDesc('created_at')
            ->groupBy('student_name')
            ->map(function (Collection $items) {
                return $items->first();
            })
            ->values();


        $nilaiPartisipan = $filteredJawabanSiswa->filter(function ($jawaban) {
            return $jawaban->nilai_otomatis !== null;
        });


        $totalPartisipan = $filteredJawabanSiswa->count();


        $selesaiNilai = $nilaiPartisipan->count();

        return view('terbitkantugas.nilai', compact(
            'terbitan',
            'nilaiPartisipan',
            'totalPartisipan',
            'selesaiNilai'
        ));
    }

    public function exportNilai(string $id)
    {
        $terbitan = TerbitanTugas::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['tugas', 'jawabanSiswa'])
            ->firstOrFail();


        $filteredJawabanSiswa = $terbitan->jawabanSiswa
            ->sortByDesc('created_at')
            ->groupBy('student_name')
            ->map(function (Collection $items) {
                return $items->first();
            })
            ->values();

        $nilaiData = $filteredJawabanSiswa->filter(function ($jawaban) {
            return $jawaban->nilai_otomatis !== null;
        })->map(function ($jawaban) {

            return [

                'Nama Siswa' => $jawaban->student_name,
                'Nilai Otomatis' => $jawaban->nilai_otomatis,
            ];
        });

        $filename = 'Nilai_Tugas_' . Str::slug($terbitan->tugas->nama_tugas) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama Siswa', 'Nilai '];

        $callback = function () use ($nilaiData, $columns) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $columns, ',');

            foreach ($nilaiData as $data) {

                fputcsv($file, $data, ',');
            }

            fclose($file);
        };

        return Response::streamDownload($callback, $filename, $headers);
    }

 public function showJawabanSiswa(string $terbitanId, string $jawabanId)
    {

        $terbitan = TerbitanTugas::where('id', $terbitanId)
            ->where('user_id', Auth::id())
            ->with(['tugas.soals.opsiJawabans'])
            ->firstOrFail();


        $jawabanSiswa = JawabanSiswa::where('id', $jawabanId)
            ->where('terbitan_tugas_id', $terbitanId)
            ->firstOrFail();

        $soals = $terbitan->tugas->soals;
        $jawabanSiswaJson = $jawabanSiswa->jawaban_json ?? [];
        $totalSoal = $soals->count();

       
        $dataJawaban = $soals->map(function ($soal, $index) use ($jawabanSiswaJson) {
            
           
            $siswaJawabRAW = $jawabanSiswaJson[(string)$soal->id] ?? null;

         
            $jawabanBenarRAW = $soal->kunci_jawaban ?? null;

            $isBenar = null;

           
            $opsiTampilan = $soal->opsiJawabans->map(function ($opsi) {
                return [
                    'id' => (string)$opsi->id, 
                    'teks' => $opsi->opsi_teks,
                    'gambar' => $opsi->opsi_gambar,
                ];
            })->values();

          
            $opsiHurufMap = []; 
            $opsiIdByIndex = []; 
            foreach ($opsiTampilan as $i => $opsi) {
                $opsiHurufMap[$opsi['id']] = chr(65 + $i);
                $opsiIdByIndex[$i] = $opsi['id'];
            }

          
            $normalizedSiswaID = null;
            $normalizedBenarID = null;
            
           
            if ($siswaJawabRAW !== null) {
                $rawSiswaString = (string)$siswaJawabRAW;
                
               
                if (isset($opsiHurufMap[$rawSiswaString])) {
                    $normalizedSiswaID = $rawSiswaString; 
                } 
               
                elseif (is_numeric($rawSiswaString) && isset($opsiIdByIndex[(int)$rawSiswaString])) {
                    $normalizedSiswaID = $opsiIdByIndex[(int)$rawSiswaString];
                }
            }

          
            if ($jawabanBenarRAW !== null) {
                $rawBenarString = (string)$jawabanBenarRAW;
                
                
                if (isset($opsiHurufMap[$rawBenarString])) {
                    $normalizedBenarID = $rawBenarString; 
                }
               
                elseif (is_numeric($rawBenarString) && isset($opsiIdByIndex[(int)$rawBenarString])) {
                    $normalizedBenarID = $opsiIdByIndex[(int)$rawBenarString];
                }
            }
       
            $jawabanSiswaHuruf = $normalizedSiswaID ? $opsiHurufMap[$normalizedSiswaID] : null;
            $jawabanBenarHuruf = $normalizedBenarID ? $opsiHurufMap[$normalizedBenarID] : null;

            if ($normalizedSiswaID === null) {
                $isBenar = null; 
            } else {
               
                $isBenar = $normalizedSiswaID === $normalizedBenarID;
            }


            return [
                'nomor' => $index + 1,
                'id' => $soal->id,
                'soal_teks' => $soal->pertanyaan,
                'gambar_soal' => $soal->gambar_soal,
                'tipe_soal' => $soal->tipe_soal_di_tugas,
                'opsi_tampilan' => collect($opsiTampilan)->keyBy(fn($o) => $o['id'])->toArray(),


               
                'jawaban_siswa_huruf' => $jawabanSiswaHuruf,
                'jawaban_benar_huruf' => $jawabanBenarHuruf,

                'jawaban_siswa' => $siswaJawabRAW, 
                'jawaban_benar' => $jawabanBenarRAW, 

                'is_benar' => $isBenar,
            ];
        });


        return view('terbitkantugas.show-jawaban', compact('terbitan', 'jawabanSiswa', 'dataJawaban', 'totalSoal'));
    }
}