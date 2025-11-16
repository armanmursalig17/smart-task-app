<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TerbitanTugas;
use App\Models\JawabanSiswa;
use App\Services\PenilaianService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SiswaTugasController extends Controller
{
    public function checkToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|min:10|max:10',
        ]);

        $token = $request->input('token');

        $terbitan = TerbitanTugas::with('tugas.soals', 'kelas')
            ->where('token', $token)
            ->where('status', 'aktif')
            ->first();

        if (!$terbitan) {
            return back()->withErrors(['token' => 'Token tidak valid atau tugas sudah ditutup.'])->withInput();
        }

        if ($terbitan->tugas->soals->isEmpty()) {
            return back()->withErrors(['token' => 'Tugas ini belum memiliki soal. Mohon hubungi guru Anda.'])->withInput();
        }

        return redirect()->route('siswa.start', $terbitan->id);
    }


    public function startTask(TerbitanTugas $terbitanTugas)
    {
        if ($terbitanTugas->status !== 'aktif') {
            return redirect('/')->withErrors(['token' => 'Tugas ini sudah ditutup.']);
        }
        return view('siswa.start', compact('terbitanTugas'));
    }

    public function processStart(Request $request, TerbitanTugas $terbitanTugas)
    {
        $request->validate(['student_name' => 'required|string|max:255']);

        if ($terbitanTugas->status !== 'aktif') {
            return redirect('/')->withErrors(['token' => 'Tugas ini sudah ditutup.']);
        }

        $soals = $terbitanTugas->tugas->soals->pluck('id')->toArray();

        // 1. BUAT ENTRI JAWABAN SISWA BARU DI DATABASE
        $initialJawaban = JawabanSiswa::create([
            'terbitan_tugas_id' => $terbitanTugas->id,
            'student_name' => $request->student_name,
            'total_waktu_menit' => 0, // Nilai awal
            'total_soal' => count($soals),
            'jawaban_json' => array_fill_keys($soals, null), // Array kosong untuk semua jawaban
            'status_penilaian' => 'belum_dinilai',
            'nilai_otomatis' => 0,
        ]);

        // 2. SIMPAN ID JAWABANSISWA YANG BARU DIBUAT KE DALAM SESSION
        $request->session()->put('task_session', [
            'terbitan_id' => $terbitanTugas->id,
            'student_name' => $request->student_name,
            'start_time' => Carbon::now()->timestamp,
            'duration' => $terbitanTugas->durasi_menit,
            'soal_ids' => $soals,
            'jawaban' => $initialJawaban->jawaban_json, // Ambil dari model yang baru dibuat
            'jawaban_siswa_id' => $initialJawaban->id, // <--- TAMBAHKAN INI
        ]);

        return redirect()->route('siswa.task', [
            'terbitanTugas' => $terbitanTugas->id,
            'soalIndex' => 1
        ]);
    }


    public function showTask(Request $request, TerbitanTugas $terbitanTugas, int $soalIndex)
    {
        $session = $request->session()->get('task_session');

        if (!$session || $session['terbitan_id'] != $terbitanTugas->id || $terbitanTugas->status !== 'aktif') {
            return redirect('/')->withErrors(['session' => 'Sesi tugas tidak valid atau sudah berakhir.']);
        }

        if (isset($session['jawaban_siswa_id'])) {
            $jawabanSiswaDB = JawabanSiswa::find($session['jawaban_siswa_id']);
            if ($jawabanSiswaDB) {
                // Perbarui session.jawaban dengan data terbaru dari database
                $session['jawaban'] = $jawabanSiswaDB->jawaban_json;
                $request->session()->put('task_session', $session);
            }
        }

        $elapsedTime = Carbon::now()->timestamp - $session['start_time'];
        $remainingTime = ($session['duration'] * 60) - $elapsedTime;


        if ($session['duration'] > 0 && $remainingTime <= 0) {
            return $this->submitTask($request, $terbitanTugas, app(PenilaianService::class));
        }

        $totalSoal = count($session['soal_ids']);
        $currentSoalID = $session['soal_ids'][$soalIndex - 1] ?? null;

        if ($soalIndex < 1 || $soalIndex > $totalSoal || !$currentSoalID) {
            return redirect()->route('siswa.task', [$terbitanTugas->id, 1]);
        }


        $terbitanTugas->load('tugas.soals.opsiJawabans');
        $soal = $terbitanTugas->tugas->soals->where('id', $currentSoalID)->first();

        if (!$soal) {
            return redirect('/')->withErrors(['error' => 'Soal tidak ditemukan.']);
        }

        $jawabanSiswa = $session['jawaban'][$currentSoalID];

        return view('siswa.task', compact('terbitanTugas', 'soal', 'soalIndex', 'totalSoal', 'jawabanSiswa', 'remainingTime', 'session'));
    }


    public function submitAnswer(Request $request, TerbitanTugas $terbitanTugas)
    {
        $session = $request->session()->get('task_session');

        if (!$session || $session['terbitan_id'] != $terbitanTugas->id || $terbitanTugas->status !== 'aktif') {
            return redirect('/')->withErrors(['session' => 'Sesi tugas tidak valid atau sudah berakhir.']);
        }

        // Periksa apakah 'jawaban_siswa_id' ada di session
        if (!isset($session['jawaban_siswa_id'])) {
            // Jika tidak ada, mungkin ada sesi lama yang terputus, arahkan ke awal.
            return redirect('/')->withErrors(['error' => 'Sesi database tugas tidak ditemukan. Mohon ulangi dari awal.']);
        }

        $soalId = $request->input('soal_id');
        $jawaban = $request->input('jawaban');
        $action = $request->input('action');
        $currentSoalIndex = array_search($soalId, $session['soal_ids']) + 1;
        $totalSoal = count($session['soal_ids']);

        // Simpan jawaban ke dalam session
        $session['jawaban'][$soalId] = $jawaban;
        $request->session()->put('task_session', $session);

        // **********************************************
        // * TAMBAHAN: UPDATE JAWABAN KE DATABASE (REAL-TIME)
        // **********************************************
        $jawabanSiswa = JawabanSiswa::find($session['jawaban_siswa_id']);
        if ($jawabanSiswa) {
            $jawabanSiswa->jawaban_json = $session['jawaban'];
            $jawabanSiswa->save(); // Update jawaban_json di DB
        }
        // **********************************************

        // Logika navigasi tetap sama
        if ($action === 'next' && $currentSoalIndex < $totalSoal) {
            return redirect()->route('siswa.task', [
                'terbitanTugas' => $terbitanTugas->id,
                'soalIndex' => $currentSoalIndex + 1
            ]);
        } elseif ($action === 'finish') {
            return redirect()->route('siswa.confirm', $terbitanTugas->id);
        } else {
            return redirect()->route('siswa.task', [
                'terbitanTugas' => $terbitanTugas->id,
                'soalIndex' => $currentSoalIndex
            ]);
        }
    }

    public function confirmTask(Request $request, TerbitanTugas $terbitanTugas)
    {
        $session = $request->session()->get('task_session');

        if (!$session || $session['terbitan_id'] != $terbitanTugas->id || $terbitanTugas->status !== 'aktif') {
            return redirect('/')->withErrors(['session' => 'Sesi tugas tidak valid atau tugas sudah ditutup.']);
        }

        $answeredCount = collect($session['jawaban'])->filter(function ($jawaban) {
            return !is_null($jawaban) && $jawaban !== '';
        })->count();

        $totalSoal = count($session['soal_ids']);
        $unansweredCount = $totalSoal - $answeredCount;
        $timeTaken = (Carbon::now()->timestamp - $session['start_time']) / 60;

        return view('siswa.confirm', compact('terbitanTugas', 'answeredCount', 'unansweredCount', 'totalSoal', 'timeTaken', 'session'));
    }


    public function submitTask(Request $request, TerbitanTugas $terbitanTugas, PenilaianService $penilaianService)
    {
        $session = $request->session()->get('task_session');

        if (!$session || $session['terbitan_id'] != $terbitanTugas->id) {
            return redirect('/')->withErrors(['session' => 'Sesi tugas tidak valid atau sudah berakhir.']);
        }


        if ($terbitanTugas->status !== 'aktif') {
            $request->session()->forget('task_session');
            return redirect('/')->withErrors(['token' => 'Tugas sudah ditutup saat Anda mencoba mengirim jawaban.']);
        }

        $timeTaken = ceil((Carbon::now()->timestamp - $session['start_time']) / 60);


        $terbitanTugas->load('tugas.soals');
        $results = $penilaianService->hitungNilaiOtomatis($terbitanTugas, $session['jawaban']);

        JawabanSiswa::create([
            'terbitan_tugas_id' => $terbitanTugas->id,
            'student_name' => $session['student_name'],
            'total_waktu_menit' => $timeTaken,
            'total_soal' => count($session['soal_ids']),
            'jawaban_json' => $session['jawaban'],


            'nilai_otomatis' => $results['nilai_otomatis'],
            'status_penilaian' => $results['status_penilaian'],


        ]);


        $request->session()->forget('task_session');


        return redirect()->route('siswa.finish')->with('success', 'Jawaban Anda berhasil dikumpulkan! Nilai Anda akan segera tersedia.');
    }
}
