<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Detail Progres Tugas') }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto ">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg mb-6 p-6">
                <div class=" mb-8">
                    <a href="{{ route('terbitkantugas.index') }}"
                        class="text-indigo-600 hover:text-indigo-900 font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    {{ $terbitan->tugas->nama_tugas }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-gray-600">
                    <div>
                        <p class="font-medium">Kelas:</p>
                        <p class="text-gray-900">{{ $terbitan->kelas->nama_kelas }}</p>
                    </div>
                    <div>
                        <p class="font-medium">Token Akses:</p>
                        <p class="text-indigo-600 font-bold text-lg">{{ $terbitan->token }}</p>
                    </div>
                    <div>
                        <p class="font-medium">Total Soal:</p>
                        <p class="text-gray-900">{{ $totalSoal }} Soal</p>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('terbitkantugas.showNilai', $terbitan->id) }}"
                            class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-sm hover:bg-blue-700 transition duration-150">
                            Nilai Partisipan
                        </a>
                    </div>


                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-semibold mb-4">
                        Daftar Siswa yang Mengakses (Total: {{ $terbitan->jawabanSiswa->count() }})
                    </h4>

                    @if ($terbitan->jawabanSiswa->isEmpty())
                        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                            Belum ada siswa yang mengakses tugas ini.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Siswa
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Progres
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status Pengerjaan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Waktu Dikerjakan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($terbitan->jawabanSiswa as $jawaban)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                                {{ $jawaban->student_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $jawabanDiisi = collect($jawaban->jawaban_json ?? [])
                                                        ->filter(function ($value) {
                                                            return !is_null($value) && $value !== '';
                                                        })
                                                        ->count();

                                                    $progres =
                                                        $totalSoal > 0 ? round(($jawabanDiisi / $totalSoal) * 100) : 0;

                                                    // LOGIKA UNTUK MENCARI POSISI SOAL SAAT INI
                                                    $nextSoal = 1;
                                                    $soalIds = $terbitan->tugas->soals->pluck('id')->toArray();

                                                    foreach ($soalIds as $index => $soalId) {
                                                        if (
                                                            is_null($jawaban->jawaban_json[$soalId] ?? null) ||
                                                            $jawaban->jawaban_json[$soalId] === ''
                                                        ) {
                                                            $nextSoal = $index + 1;
                                                            break;
                                                        }
                                                        $nextSoal = $index + 2;
                                                    }

                                                    $posisi = min($nextSoal, $totalSoal + 1);

                                                    // Tentukan status berdasarkan progres jawaban
                                                    if ($progres == 100) {
                                                        $statusPengerjaan = 'selesai';
                                                    } elseif ($jawabanDiisi > 0) {
                                                        $statusPengerjaan = 'sedang_dikerjakan';
                                                    } else {
                                                        $statusPengerjaan = 'belum_mulai';
                                                    }

                                                    // Tentukan Teks Posisi Saat Ini
                                                    if ($statusPengerjaan === 'selesai') {
                                                        $posisiTeks = 'Selesai';
                                                    } else {
                                                        $posisiTeks = 'Soal ' . $posisi;
                                                    }
                                                @endphp

                                                {{-- Tampilan Progres --}}
                                                <p class="font-semibold text-sm">
                                                    {{ $jawabanDiisi }} / {{ $totalSoal }} Soal
                                                    ({{ $progres }}%)
                                                </p>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="h-1.5 rounded-full {{ $progres == 100 ? 'bg-green-600' : 'bg-blue-600' }}"
                                                        style="width: {{ $progres }}%"></div>
                                                </div>

                                                {{-- Tambahkan Baris Posisi/Progres Saat Ini --}}
                                                <p
                                                    class="text-xs mt-2 font-medium 
                                                     {{ $posisiTeks === 'Selesai' ? 'text-green-600' : 'text-indigo-600' }}">
                                                    <span class="font-bold">Posisi Saat Ini:</span> {{ $posisiTeks }}
                                                </p>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap">

                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $statusPengerjaan === 'selesai'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($statusPengerjaan === 'sedang_dikerjakan'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucwords(str_replace('_', ' ', $statusPengerjaan)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if ($jawaban->total_waktu_menit)
                                                    {{ $jawaban->total_waktu_menit }} Menit
                                                @else
                                                    ...
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
