<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mulai Tugas: {{ $terbitanTugas->tugas->nama_tugas }}</title>
    <link rel="icon" href="{{ asset('img/tutwuri.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-xl mx-auto p-6 md:p-10 bg-white rounded-xl shadow-2xl">
        <h1 class="text-3xl font-bold text-blue-600 mb-2">Mulai Tugas</h1>
        <p class="text-xl text-gray-700 mb-6 font-semibold">{{ $terbitanTugas->tugas->nama_tugas }}</p>

        <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-800">
            <p><strong>Kelas:</strong> {{ $terbitanTugas->kelas->nama_kelas }}</p>
            <p><strong>Durasi Pengerjaan:</strong>
                {{ $terbitanTugas->durasi_menit ? $terbitanTugas->durasi_menit . ' Menit' : 'Tidak Dibatasi' }}
            </p>
            <p><strong>Jumlah Soal:</strong> {{ $terbitanTugas->tugas->soals->count() }} Soal</p>
        </div>

        <form action="{{ route('siswa.processStart', $terbitanTugas->id) }}" method="POST">
            @csrf
            
            <div class="mb-6">                 <label for="student_name"
                    class="block text-sm font-medium text-gray-700 mb-2">Masukkan Nama Lengkap Anda</label>
                <input type="text" name="student_name" id="student_name" required
                    placeholder="Cth: Budi Santoso (Wajib diisi)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500 shadow-sm">
                @error('student_name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                
            </div>
            
            <button type="submit"
                class="w-full px-4 py-3 bg-green-600 text-white rounded-lg font-semibold text-lg hover:bg-green-700 transition-colors">
                Selanjutnya: Mulai Soal
                </button>
            </form>
    </div>

</body>

</html>
