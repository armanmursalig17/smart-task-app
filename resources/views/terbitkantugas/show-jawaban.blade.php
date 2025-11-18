<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Detail Jawaban Siswa') }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">

               
                <div class="mb-8 border-b pb-4">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $terbitan->tugas->nama_tugas }}</h3>
                    <p class="text-gray-600">Kelas: {{ $terbitan->kelas->nama_kelas }}</p>
                    <p class="text-gray-600">Nama Siswa: <span class="font-bold text-blue-600">{{ $jawabanSiswa->student_name }}</span></p>
                    <p class="text-gray-600">Waktu Selesai: {{ $jawabanSiswa->created_at->format('d M Y H:i:s') }}</p>
                    
                </div>


                <h4 class="text-xl font-semibold mb-6">Detail Jawaban (Total Soal: {{ $totalSoal }})</h4>

                @foreach ($dataJawaban as $data)
                    @php
                        // Inisialisasi warna default (Netral/Uraian)
                        $borderColor = '#3B82F6'; // Blue
                        $siswaBgColor = '#EFF6FF';
                        $siswaTextColor = '#1D4ED8';
                        $statusText = 'Belum Dinilai';

                        // Logic warna hanya berlaku untuk Pilihan Ganda
                        if ($data['tipe_soal'] === 'pilihan_ganda') {
                            if ($data['is_benar'] === true) {
                                $borderColor = '#10B981'; // Green (Benar)
                                $siswaBgColor = '#D1FAE5';
                                $siswaTextColor = '#065F46';
                                $statusText = 'Jawaban Benar';
                            } elseif ($data['is_benar'] === false) {
                                $borderColor = '#EF4444'; // Red (Salah)
                                $siswaBgColor = '#FEE2E2';
                                $siswaTextColor = '#991B1B';
                                $statusText = 'Jawaban Salah';
                            } elseif ($data['is_benar'] === null) {
                                // Belum menjawab (jika belum menjawab di pilihan ganda)
                                $borderColor = '#9CA3AF'; // Gray
                                $siswaBgColor = '#F3F4F6';
                                $siswaTextColor = '#4B5563';
                                $statusText = 'Belum Menjawab';
                            }
                        } else {
                            // Untuk Uraian/Isian, gunakan warna netral
                            $borderColor = '#3B82F6';
                            $siswaBgColor = '#EFF6FF';
                            $siswaTextColor = '#1D4ED8';
                            $statusText = $data['jawaban_siswa'] ? 'Menunggu Penilaian' : 'Belum Menjawab';
                        }

                    @endphp

                    <div class="bg-gray-50 border-l-4 p-4 mb-6 rounded-lg shadow-sm"
                        style="border-color: {{ $borderColor }};">

                        {{-- Tampilan Soal Header --}}
                        <p class="text-base font-bold text-gray-800 mb-3">
                            Soal No. {{ $data['nomor'] }}
                            <span class="text-sm font-medium text-gray-500">
                                (Tipe: {{ ucwords(str_replace('_', ' ', $data['tipe_soal'])) }})
                            </span>
                        </p>

                        {{-- Teks Soal --}}
                        <div class="prose max-w-none text-gray-900 mb-4">
                            {!! $data['soal_teks'] !!}
                        </div>
                        
                        {{-- GAMBAR SOAL: MENGGUNAKAN HELPER ASSET/STORAGE --}}
                        @if ($data['gambar_soal'])
                             <img src="{{ asset('storage/' . $data['gambar_soal']) }}" alt="Gambar Soal" class="max-w-xs h-auto mb-4 rounded shadow-md">
                        @endif

                        {{-- Opsi Jawaban (Hanya untuk Pilihan Ganda) --}}
                        @if ($data['tipe_soal'] === 'pilihan_ganda' && !empty($data['opsi_tampilan']))

                            <h5 class="text-sm font-semibold mt-4 mb-2">Pilihan Jawaban:</h5>
                            <div class="space-y-2">
                                @foreach ($data['opsi_tampilan'] as $id => $opsi)
                                    @php
                                        // LOGIKA LABEL HURUF UNTUK OPSI JAWABAN
                                        $labelHuruf = chr(65 + $loop->index);

                                        $isSiswaJawab = (string) $data['jawaban_siswa'] === (string) $id;
                                        $isKunciBenar = (string) $data['jawaban_benar'] === (string) $id;

                                        $opsiClass = 'bg-white border text-gray-700 border-gray-200';
                                        
                                        // Highlight Opsi yang Benar (Kunci Jawaban)
                                        if ($isKunciBenar) {
                                            $opsiClass = 'bg-green-100 border-green-500 border-2 font-bold text-green-800';
                                        } 
                                        // Highlight Jawaban Siswa Jika Salah (Sudah divalidasi tidak isKunciBenar)
                                        elseif ($isSiswaJawab) {
                                            $opsiClass = 'bg-red-100 border-red-500 border-2 font-bold text-red-800';
                                        }
                                    @endphp
                                    <div class="p-3 rounded-lg shadow-sm {{ $opsiClass }}">
                                        <span class="font-extrabold mr-3">{{ $labelHuruf }}.</span>
                                        {!! $opsi['teks'] !!}
                                        
                                      
                                        @if ($opsi['gambar'])
                                            <img src="{{ asset('storage/' . $opsi['gambar']) }}" alt="Gambar Opsi"  class="mt-2 h-20 w-20 rounded">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <hr class="my-4 border-gray-300">

                     
                        <div class="mt-4 p-3 rounded-md" style="background-color: {{ $siswaBgColor }};">
                            <p class="font-bold mb-1 text-sm" style="color: {{ $siswaTextColor }};">
                                Jawaban Siswa:
                                <span class="font-extrabold text-lg">
                                    @if ($data['tipe_soal'] === 'pilihan_ganda')
                                        {{ $data['jawaban_siswa_huruf'] ?? 'Belum Menjawab' }}
                                    @else
                                     
                                        <span class="text-base font-medium"></span>
                                    @endif
                                </span>
                            </p>

                            @if ($data['tipe_soal'] === 'pilihan_ganda')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mt-1 
                                    {{ $data['is_benar'] === true ? 'bg-green-600 text-white' : ($data['is_benar'] === false ? 'bg-red-600 text-white' : 'bg-gray-500 text-white') }}">
                                    {{ $statusText }}
                                </span>
                            @else
                                <span class="text-sm font-medium italic">
                                    {{ $data['jawaban_siswa'] ?  : 'Siswa belum menjawab.' }}
                                </span>
                            @endif
                        </div>

                        {{-- Jawaban Kunci (Hanya untuk Pilihan Ganda - Huruf) --}}
                        @if ($data['tipe_soal'] === 'pilihan_ganda' && $data['jawaban_benar_huruf'] !== null)
                            <div class="mt-2 p-3 bg-gray-100 rounded-md border border-green-300">
                                <p class="font-bold text-sm">
                                    Jawaban Kunci:
                                    <span class="font-extrabold text-lg text-green-700">
                                        {{ $data['jawaban_benar_huruf'] }}
                                    </span>
                                </p>
                            </div>
                        @endif
                       
                        @if ($data['tipe_soal'] !== 'pilihan_ganda')
                            {{-- @if ($data['jawaban_siswa'] !== null)
                                
                                <div class="mt-4 p-3 bg-yellow-50 rounded-md border border-yellow-300">
                                    <p class="font-bold text-sm text-yellow-800 mb-1">Jawaban Teks Siswa:</p>
                                    <div class="prose max-w-none text-gray-800 p-2 border border-gray-200 rounded bg-white">
                                        {!! nl2br(e($data['jawaban_siswa'])) !!}
                                    </div>
                                </div>
                            @endif --}}

                            {{-- Kunci Jawaban Teks --}}
                            @if ($data['jawaban_benar'] !== null)
                                <div class="mt-2 p-3 bg-gray-100 rounded-md border border-gray-300">
                                    <p class="font-bold text-sm text-gray-700 mb-1">Kunci Jawaban Teks:</p>
                                    <div class="prose max-w-none text-gray-800 p-2 border border-gray-200 rounded bg-white">
                                      
                                        {!! nl2br(e($data['jawaban_benar'])) !!}
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>