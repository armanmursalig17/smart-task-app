<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pengumpulan Tugas</title>
    <link rel="icon" href="{{ asset('img/tutwuri.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">

    <div class="w-full max-w-lg mx-auto p-6 md:p-10 bg-white rounded-xl shadow-2xl text-center">
        <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Konfirmasi Pengumpulan Tugas</h1>
        
        <p class="text-lg text-gray-700 mb-2">
            Halo, <strong class="text-indigo-600">{{ $session['student_name'] ?? 'Siswa' }}</strong>.
        </p>
        <p class="text-gray-600 mb-8">
            Anda akan mengumpulkan tugas {{ $terbitanTugas->tugas->nama_tugas }} untuk  {{ $terbitanTugas->kelas->nama_kelas }}. Sebelum Menyimpan Jawaban, Silakan Periksa Kembali dan pastikan jawaban anda sudah benar
        </p>

        <div class="grid grid-cols-2 gap-4 text-left mb-10 p-5 bg-gray-50 rounded-lg border">
            <div class="col-span-2 text-sm font-semibold text-gray-600 border-b pb-2 mb-2">Ringkasan Pengerjaan</div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Soal</p>
                <p class="text-xl font-bold text-gray-900">{{ $totalSoal }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Waktu Digunakan</p>
                <p class="text-xl font-bold text-gray-900">{{ round($timeTaken, 1) }} Menit</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Jawaban Terisi</p>
                <p class="text-xl font-bold text-green-600">{{ $answeredCount }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Dijawab</p>
                <p class="text-xl font-bold text-red-600">{{ $unansweredCount }}</p>
            </div>
        </div>

        <form action="{{ route('siswa.submit', $terbitanTugas->id) }}" method="POST">
            @csrf
            
            <button type="submit" 
                    class="w-full px-4 py-3 bg-green-600 text-white rounded-lg font-semibold text-lg hover:bg-green-700 transition-colors mb-3"
                    onclick="return confirm('APAKAH ANDA YAKIN INGIN MENGUMPULKAN? Anda tidak bisa mengedit lagi setelah ini.');">
                YA, Kumpulkan Jawaban Sekarang
            </button>
        </form>
        
        <a href="{{ route('siswa.task', [$terbitanTugas->id, 1]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
            &larr; Kembali ke Soal (Cek Ulang)
        </a>
    </div>
    
</body>
</html>