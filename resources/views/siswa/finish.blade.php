<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Selesai</title>
    <link rel="icon" href="{{ asset('img/tutwuri.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">

    <div class="w-full max-w-lg mx-auto p-6 md:p-10 bg-white rounded-xl shadow-2xl text-center">
        <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Pengumpulan Berhasil!</h1>
        
        @if (session('success'))
            <p class="text-lg text-gray-700 mb-8">{{ session('success') }}</p>
        @else
             <p class="text-lg text-gray-700 mb-8">Terima kasih telah menyelesaikan tugas ini. Jawaban Anda telah tersimpan dan akan segera dinilai oleh guru Anda.</p>
        @endif
        
        <a href="{{ url('/') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
            Kembali ke Halaman Utama
        </a>
    </div>
    
</body>
</html>