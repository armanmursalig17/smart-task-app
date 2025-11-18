<?php

use App\Models\Kelas;
// Hapus 'use App\Http\Controllers\Tugas;' yang salah
use App\Models\TerbitanTugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NilaiTugasController;
use App\Http\Controllers\SiswaTugasController;
use App\Http\Controllers\TerbitkanTugasController;

Route::get('/', function () {
    return view('welcome');
});

// GROUP ROUTE SISWA
Route::controller(SiswaTugasController::class)->group(function () {
    // 1. Pengecekan Token & Mulai Sesi
    Route::post('/check-token', 'checkToken')->name('siswa.checkToken');
    Route::get('/start-task/{terbitanTugas}', 'startTask')->name('siswa.start');
    Route::post('/start-task/{terbitanTugas}/process', 'processStart')->name('siswa.processStart');

    // 2. Route Konfirmasi (HARUS DI ATAS ROUTE UMUM 'task')
    Route::get('/task/{terbitanTugas}/confirm', 'confirmTask')->name('siswa.confirm'); // <-- PINDAHKAN KE SINI

    // 3. Pengerjaan Soal (Route Umum)
    Route::get('/task/{terbitanTugas}/{soalIndex}', 'showTask')->name('siswa.task');
    Route::post('/task/{terbitanTugas}/submit-answer', 'submitAnswer')->name('siswa.submitAnswer');

    // 4. Submit Final
    Route::post('/task/{terbitanTugas}/submit-final', 'submitTask')->name('siswa.submit');

    // 5. Halaman Selesai
    Route::get('/task/finish', function () {
        return view('siswa.finish');
    })->name('siswa.finish');
});





Route::get('/dashboard', function () {

    $user = Auth::user();


    $jumlahKelas = Kelas::where('user_id', $user->id)->count();


    $terbitanAktif = TerbitanTugas::where('user_id', $user->id)
        ->where('status', 'aktif')
        ->count();


    $terbitanDitutup = TerbitanTugas::where('user_id', $user->id)
        ->where('status', 'ditutup')
        ->count();

    return view('dashboard', [
        'userName' => $user->name,
        'jumlahKelas' => $jumlahKelas,
        'terbitanAktif' => $terbitanAktif,
        'terbitanDitutup' => $terbitanDitutup,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');

    Route::get('/kelas/{id}/detail', [DetailController::class, 'show'])->name('kelas.detail');

    // Route untuk menampilkan form tambah tugas
    Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
    Route::get('/tugas/{tugas}/detail', [TugasController::class, 'show'])->name('tugas.detail');

    Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');

    Route::get('/tugas/{tugas}/soal/create', [TugasController::class, 'createSoal'])->name('tugas.soal.create'); // <-- BARU
    Route::post('/tugas/{tugas}/soal', [TugasController::class, 'storeSoal'])->name('tugas.soal.store'); // <-- SUDAH ADA


    Route::get('/tugas/{tugas}/export-nilai', [NilaiTugasController::class, 'exportNilaiToCsv'])
        ->name('tugas.export_nilai');

    Route::resource('soal', TugasController::class)->only([
        'edit',
        'update',
        'destroy'
    ]);


    // Route::get('/terbitkantugas', [TerbitkanTugasController::class, 'index'])->name('terbitkantugas.index');
    Route::resource('terbitkantugas', TerbitkanTugasController::class);
    Route::post('terbitkantugas/{terbitanTuga}/reUse', [TerbitkanTugasController::class, 'reUse'])
        ->name('terbitkantugas.reUse');
    Route::get('terbitkantugas/{terbitan_tugas}/nilai', [TerbitkanTugasController::class, 'showNilai'])->name('terbitkantugas.showNilai');
    Route::get('terbitkantugas/{terbitan_tugas}/export/nilai', [TerbitkanTugasController::class, 'exportNilai'])->name('terbitkantugas.exportNilai');
    Route::post('/terbitkantugas/{id}/deactivate', [TerbitkanTugasController::class, 'deactivate'])->name('terbitkantugas.deactivate');
    
    Route::get('/terbitkantugas/{terbitanId}/jawaban/{jawabanId}', [TerbitkanTugasController::class, 'showJawabanSiswa'])
        ->name('terbitkantugas.showJawabanSiswa');

    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/reset-password', [UsersController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
