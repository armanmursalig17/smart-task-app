<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl leading-tight">
{{ __('Nilai Partisipan Tugas') }}
</h2>
</x-slot>

<div class="py-0">
    <div class="max-w-full mx-auto ">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 mb-6">
            <a href="{{ route('terbitkantugas.show', $terbitan->id) }}"
                class="text-gray-500 hover:text-indigo-600 text-sm font-semibold flex items-center mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Detail Progres
            </a>
            

            <h3 class="text-2xl font-bold text-gray-800 mb-2">
                Nilai: {{ $terbitan->tugas->nama_tugas }}
            </h3>

            <a href="{{ route('terbitkantugas.exportNilai', $terbitan->id) }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition duration-150 flex items-center shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Data Nilai (Excel/CSV)
            </a>
           
            
            @if ($nilaiPartisipan->isEmpty())
                <div class="p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                    Belum ada siswa yang berhasil menyelesaikan dan mendapatkan nilai otomatis.
                </div>
            @else
                <div class="overflow-x-auto mt-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Siswa
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nilai 
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($nilaiPartisipan as $jawaban)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        {{ $jawaban->student_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xl font-bold text-indigo-700">
                                        {{ $jawaban->nilai_otomatis }}
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


</x-app-layout>

