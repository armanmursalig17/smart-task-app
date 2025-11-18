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

        // 1. Ambil tugas dengan status 'aktif' (untuk tabel utama)
        $terbitanAktif = TerbitanTugas::where('user_id', $userId)
            ->where('status', 'aktif')
            ->with(['kelas', 'tugas'])
            ->latest()
            ->paginate(10, ['*'], 'aktifPage'); // Gunakan nama page yang berbeda

        // 2. Ambil tugas dengan status 'ditutup' (untuk riwayat tersembunyi)
        $terbitanDitutup = TerbitanTugas::where('user_id', $userId)
            ->where('status', 'ditutup')
            ->with(['kelas', 'tugas'])
            ->latest()
            ->get(); // Tidak perlu pagination di sini jika data ditutup tidak terlalu banyak

        // Kirim kedua koleksi ke view
        return view('terbitkantugas.index', compact('terbitanAktif', 'terbitanDitutup'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $kelas = Kelas::where('user_id', Auth::id())->get();

        // 1. Ambil ID tugas yang sudah diterbitkan oleh user yang sedang login.
        $tugasSudahTerbitIds = TerbitanTugas::where('user_id', Auth::id())
            ->pluck('tugas_id')
            ->toArray();

        // 2. Ambil semua tugas milik kelas user ini, KECUALI yang sudah diterbitkan.
        $tugas = Tugas::whereHas('kelas', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->whereNotIn('id', $tugasSudahTerbitIds) // <-- Tambahan: Filter tugas yang sudah diterbitkan
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

        // Persiapkan data untuk ditampilkan
        $dataJawaban = $soals->map(function ($soal, $index) use ($jawabanSiswaJson) {
            
            // 1. MENGAMBIL JAWABAN SISWA (pastikan akses key Soal ID adalah string)
            $siswaJawabRAW = $jawabanSiswaJson[(string)$soal->id] ?? null;

            // 2. MENGAMBIL KUNCI JAWABAN (ID Opsi yang benar/Index Opsi)
            $jawabanBenarRAW = $soal->kunci_jawaban ?? null;

            $isBenar = null;

            // 3. MEMBUAT MAP OPSI (ID Opsi ke Teks, dan Opsi ID dijamin string)
            $opsiTampilan = $soal->opsiJawabans->map(function ($opsi) {
                return [
                    'id' => (string)$opsi->id, // <-- ID Opsi dijamin string
                    'teks' => $opsi->opsi_teks,
                    'gambar' => $opsi->opsi_gambar,
                ];
            })->values();

            // 4. MEMBUAT MAP ID OPSI ke HURUF A/B/C dan INDEX OPSI ke ID OPSI
            $opsiHurufMap = []; // [ID Opsi (string) => Huruf]
            $opsiIdByIndex = []; // [Index (integer) => ID Opsi (string)]

            foreach ($opsiTampilan as $i => $opsi) {
                $opsiHurufMap[$opsi['id']] = chr(65 + $i);
                $opsiIdByIndex[$i] = $opsi['id'];
            }

            
            // =========================================================================
            // 5. NORMALISASI JAWABAN: Menentukan ID Opsi yang BENAR dari RAW Value (ID/Index)
            // =========================================================================

            $normalizedSiswaID = null;
            $normalizedBenarID = null;
            
            // --- Normalisasi Jawaban Siswa ---
            if ($siswaJawabRAW !== null) {
                $rawSiswaString = (string)$siswaJawabRAW;
                
                // Coba lookup sebagai ID Opsi
                if (isset($opsiHurufMap[$rawSiswaString])) {
                    $normalizedSiswaID = $rawSiswaString; 
                } 
                // Jika gagal, coba lookup sebagai Index Opsi (karena rawSiswa adalah '0', '1', '2'...)
                elseif (is_numeric($rawSiswaString) && isset($opsiIdByIndex[(int)$rawSiswaString])) {
                    $normalizedSiswaID = $opsiIdByIndex[(int)$rawSiswaString];
                }
            }

            // --- Normalisasi Kunci Jawaban ---
            if ($jawabanBenarRAW !== null) {
                $rawBenarString = (string)$jawabanBenarRAW;
                
                // Coba lookup sebagai ID Opsi
                if (isset($opsiHurufMap[$rawBenarString])) {
                    $normalizedBenarID = $rawBenarString; 
                }
                // Jika gagal, coba lookup sebagai Index Opsi
                elseif (is_numeric($rawBenarString) && isset($opsiIdByIndex[(int)$rawBenarString])) {
                    $normalizedBenarID = $opsiIdByIndex[(int)$rawBenarString];
                }
            }
            
            // =========================================================================
            // 6. HITUNG HASIL
            // =========================================================================

            $jawabanSiswaHuruf = $normalizedSiswaID ? $opsiHurufMap[$normalizedSiswaID] : null;
            $jawabanBenarHuruf = $normalizedBenarID ? $opsiHurufMap[$normalizedBenarID] : null;

            if ($normalizedSiswaID === null) {
                $isBenar = null; // Belum menjawab
            } else {
                // PENENTUAN BENAR/SALAH: Perbandingan dilakukan antara ID Opsi yang sudah dinormalisasi
                $isBenar = $normalizedSiswaID === $normalizedBenarID;
            }


            return [
                'nomor' => $index + 1,
                'id' => $soal->id,
                'soal_teks' => $soal->pertanyaan,
                'gambar_soal' => $soal->gambar_soal,
                'tipe_soal' => $soal->tipe_soal_di_tugas,
                'opsi_tampilan' => collect($opsiTampilan)->keyBy(fn($o) => $o['id'])->toArray(),


                // === Output ke View ===
                'jawaban_siswa_huruf' => $jawabanSiswaHuruf,
                'jawaban_benar_huruf' => $jawabanBenarHuruf,

                'jawaban_siswa' => $siswaJawabRAW, // Mengirim RAW Value (hanya untuk debugging)
                'jawaban_benar' => $jawabanBenarRAW, // Mengirim RAW Value (hanya untuk debugging)

                'is_benar' => $isBenar,
            ];
        });


        return view('terbitkantugas.show-jawaban', compact('terbitan', 'jawabanSiswa', 'dataJawaban', 'totalSoal'));
    }
}