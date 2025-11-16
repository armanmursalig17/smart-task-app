<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl  leading-tight">
            Tambah Soal Baru untuk Tugas: {{ $tugas->nama_tugas }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto ">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('tugas.detail', $tugas->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            &larr; Batal dan Kembali ke Detail Tugas
                        </a>
                    </div>

                
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                
                    <form action="{{ route('tugas.soal.store', $tugas->id) }}" method="POST" x-data="soalForm()"
                        enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="tugas_id" value="{{ $tugas->id }}">

                        <div class="mb-6">
                            <p class="block font-medium text-sm text-gray-700 mb-2">
                                Jenis Soal <span class="font-bold capitalize">{{ str_replace('_', ' ', $tugas->jenis_tugas) }}</span>
                            </p>
                        </div>

                        <hr class="my-6">

                        <h3 class="text-xl font-bold mb-4">Detail Soal Baru</h3>

                     
                        <div class="border border-gray-300 rounded-lg p-4 mb-4">
                            <h4 class="text-lg font-semibold mb-2">Soal Baru</h4>
                            
                           
                            @if ($tugas->jenis_tugas === 'gabungan')
                                <div class="mb-4">
                                    <label class="block font-medium text-sm text-gray-700">Tipe Soal Ini</label>
                                    <select name="soal[0][tipe_soal]" x-model="soal.tipe_soal"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                        <option value="pilihan_ganda">Pilihan Ganda</option>
                                        <option value="uraian">Uraian</option>
                                    </select>
                                </div>
                            @else
                               
                                <input type="hidden" name="soal[0][tipe_soal]" x-model="soal.tipe_soal" />
                            @endif

                           
                            <div class="border-t border-gray-200 mt-4 pt-4">
                                <button type="button" @click="soal.showOptionalFields = !soal.showOptionalFields"
                                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium mb-3">
                                    <span x-show="!soal.showOptionalFields">+ Tambah Keterangan / Gambar (Opsional)</span>
                                    <span x-show="soal.showOptionalFields" style="display: none;">- Sembunyikan Keterangan / Gambar</span>
                                </button>

                                <div x-show="soal.showOptionalFields" style="display: none;"
                                    class="space-y-4 p-4 bg-gray-50 rounded-md border border-gray-200">
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700">Keterangan Soal</label>
                                        <textarea name="soal[0][keterangan_soal]" x-model="soal.keterangan_soal" rows="2"
                                            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                                            placeholder="Misal: Perhatikan gambar berikut untuk menjawab..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700">Gambar Soal</label>
                                        <input type="file" name="soal[0][gambar_soal]"
                                            class="block mt-1 w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100">
                                    </div>
                                </div>
                            </div>
                           
                            <div class="mb-4 mt-4">
                                <label class="block font-medium text-sm text-gray-700">Pertanyaan</label>
                                <textarea name="soal[0][pertanyaan]" x-model="soal.pertanyaan" rows="3" required
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300"></textarea>
                            </div>


                        
                            <template x-if="soal.tipe_soal === 'pilihan_ganda'">
                                <div class="pl-4 border-l-4 border-gray-200 mb-4">
                                    <h5 class="font-semibold mb-2">Opsi Jawaban</h5>

                                    <template x-for="(opsi, opsiIndex) in soal.opsi" :key="opsiIndex">
                                        <div class="flex items-start mb-3 p-2 border rounded-md bg-gray-50">
                                            
                                          
                                            <input :name="'soal[0][kunci_jawaban_pg]'"
                                                :value="opsiIndex" type="radio"
                                                x-model="soal.kunci_jawaban_pg" class="mr-3 mt-2 focus:ring-blue-500">

                                            
                                            <span class="mr-2 font-medium w-6 mt-1.5"
                                                x-text="String.fromCharCode(65 + opsiIndex) + '.'"></span>

                                          
                                            <div class="flex-1">
                                               
                                                <div class="mb-2">
                                                    <select :name="'soal[0][opsi][' + opsiIndex + '][tipe]'" 
                                                            x-model="opsi.tipe"
                                                            class="text-xs rounded-md border-gray-300 shadow-sm"
                                                            style="padding-top: 4px; padding-bottom: 4px;">
                                                        <option value="teks">Teks</option>
                                                        <option value="gambar">Gambar</option>
                                                    </select>
                                                </div>
                                                
                                                
                                                <div x-show="opsi.tipe === 'teks'">
                                                    <input type="text"
                                                        :name="'soal[0][opsi][' + opsiIndex + '][konten_teks]'"
                                                        x-model="opsi.konten_teks" placeholder="Teks Opsi Jawaban"
                                                        class="block w-full rounded-md shadow-sm border-gray-300 text-sm">
                                                </div>

                                                
                                                <div x-show="opsi.tipe === 'gambar'" style="display: none;">
                                                    <input type="file" 
                                                        :name="'soal[0][opsi][' + opsiIndex + '][konten_gambar]'"
                                                        class="block w-full text-sm text-gray-500
                                                            file:mr-3 file:py-1 file:px-3
                                                            file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                            file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                                    <span class="text-xs text-gray-500 italic">Upload gambar untuk opsi ini.</span>
                                                </div>
                                            </div>
                                            
                                          
                                            <button type="button" @click="hapusOpsi(opsiIndex)"
                                                class="ml-3 text-red-500 hover:text-red-700 text-sm mt-1.5">
                                                Hapus
                                            </button>
                                        </div>
                                    </template>
                                    
                                    <button type="button" @click="tambahOpsi()"
                                        class="mt-2 px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">
                                        + Tambah Opsi
                                    </button>
                                </div>
                            </template>
                            


                            <template x-if="soal.tipe_soal === 'uraian'">
                                <div class="pl-4 border-l-4 border-blue-200 mb-4">
                                    <label class="block font-medium text-sm text-gray-700">Kunci Jawaban
                                        (Uraian)</label>
                                    <textarea name="soal[0][kunci_jawaban_uraian]" x-model="soal.kunci_jawaban_uraian" rows="2"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300"></textarea>
                                </div>
                            </template>

                        </div>

                        
                        <hr class="my-6">

                        <div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700">
                                Simpan Soal
                            </button>
                        </div>
                    </form>

                    @php
                        // ▼▼▼ PERSIAPKAN DATA ALPINE.JS ▼▼▼
                        $default_opsi = [
                            ['tipe' => 'teks', 'konten_teks' => ''],
                            ['tipe' => 'teks', 'konten_teks' => ''],
                        ];
                        
                        // Tentukan tipe soal default berdasarkan jenis tugas, 
                        // kecuali jika gabungan, defaultnya pilihan_ganda
                        $initial_tipe_soal = $tugas->jenis_tugas === 'gabungan' ? 'pilihan_ganda' : $tugas->jenis_tugas;
                        
                        $default_soal = [
                            'pertanyaan' => old('soal.0.pertanyaan') ?? '',
                            'keterangan_soal' => old('soal.0.keterangan_soal') ?? '',
                            'showOptionalFields' => (bool) old('soal.0.keterangan_soal') || old('soal.0.gambar_soal'),
                            'tipe_soal' => old('soal.0.tipe_soal', $initial_tipe_soal),
                            'opsi' => old('soal.0.opsi') ? array_values(old('soal.0.opsi')) : $default_opsi,
                            'kunci_jawaban_pg' => old('soal.0.kunci_jawaban_pg'),
                            'kunci_jawaban_uraian' => old('soal.0.kunci_jawaban_uraian') ?? '',
                        ];

                        // Pastikan properti default ada untuk opsi (setelah validasi error)
                        foreach ($default_soal['opsi'] as $key => $opsi) {
                            if (!isset($opsi['konten_teks'])) {
                                $default_soal['opsi'][$key]['konten_teks'] = '';
                            }
                            if (!isset($opsi['tipe'])) {
                                $default_soal['opsi'][$key]['tipe'] = 'teks';
                            }
                        }
                        
                        $soal_json = json_encode($default_soal);
                        // ▲▲▲ AKHIR DATA ▲▲▲
                    @endphp

                   
                    <script>
                        function soalForm() {
                            return {
                                // Hanya perlu 1 objek soal, tidak perlu array soals
                                soal: {!! $soal_json !!},
                                
                                init() {
                                    // Watcher hanya perlu jika jenis tugas adalah 'gabungan'
                                    // if ('{{ $tugas->jenis_tugas }}' !== 'gabungan') {
                                    //     // Memaksa tipe_soal sesuai jenis_tugas
                                    //     this.soal.tipe_soal = '{{ $tugas->jenis_tugas }}';
                                    // }
                                },

                               
                                tambahOpsi() {
                                    this.soal.opsi.push({ tipe: 'teks', konten_teks: '' });
                                },
                                hapusOpsi(opsiIndex) {
                                    this.soal.opsi.splice(opsiIndex, 1);
                                }
                            }
                        }
                    </script>
                    {{-- ▲▲▲ AKHIR SCRIPT ▲▲▲ --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>