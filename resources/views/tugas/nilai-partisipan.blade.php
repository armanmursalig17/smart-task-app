<x-app-layout>
    <x-slot name="header">
        {{-- Header dinamis sesuai permintaan --}}
        <h2 class="font-semibold text-xl  leading-tight">
            Daftar Nilai {{ $tugas->kelas->nama_kelas }} untuk Tugas: {{ $tugas->nama_tugas }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto ">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Tombol Kembali ke Detail Kelas --}}
                    <div class="mb-6">
                        <a href="{{ route('kelas.detail', $tugas->kelas) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            &larr; Kembali
                        </a>
                        <a href="{{ route('tugas.export_nilai', $tugas) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Unduh By Excel
                        </a>
                    </div>

                    {{-- Judul Utama (Sederhana) --}}
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">
                        Rekap Nilai Partisipan
                    </h3>

                    @if ($jawabanSiswa->isEmpty())
                        {{-- Pesan jika belum ada siswa yang mengerjakan --}}
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md"
                            role="alert">
                            <p class="font-bold">Informasi</p>
                            <p>Belum ada siswa yang mengerjakan tugas ini.</p>
                        </div>
                    @else
                        {{-- Tampilan Tabel Nilai --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg">
                                <thead class="bg-indigo-600">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider rounded-tl-lg">
                                            No.
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Nama Siswa
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider rounded-tr-lg">
                                            Nilai
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($jawabanSiswa as $index => $jawaban)
                                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $index + 1 }}
                                            </td>
                                            {{-- Kolom Nama Siswa --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                {{ $jawaban->student_name }}
                                            </td>
                                            {{-- Kolom Nilai Otomatis --}}
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-xl font-extrabold text-green-700">
                                                {{ $jawaban->nilai_otomatis ?? 'N/A' }}
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
