<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg leading-tight"> Buat Tugas Baru untuk {{ $kelas->nama_kelas }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto "> <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-4 sm:p-4 text-gray-900"> <div class="mb-3"> <a href="{{ route('kelas.detail', $kelas->id) }}"
                            class="inline-flex items-center px-3 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            &larr; Batal dan Kembali
                        </a>
                    </div>

                    {{-- Tampilkan error jika ada --}}
                    @if (session('error'))
                        <div class="mb-3 p-3 bg-red-100 text-red-700 rounded-md text-sm"> {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-3 p-3 bg-red-100 text-red-700 rounded-md text-sm"> <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <form action="{{ route('tugas.store') }}" method="POST" x-data="tugasForm()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

                        <div class="mb-3"> <label for="nama_tugas" class="block font-medium text-sm text-gray-700">Nama Tugas</label>
                            <input type="text" name="nama_tugas" id="nama_tugas" value="{{ old('nama_tugas') }}"
                                required
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"> </div>

                        <div class="mb-4"> <label for="jenis_tugas" class="block font-medium text-sm text-gray-700">Jenis Tugas</label>
                            <select name="jenis_tugas" id="jenis_tugas" x-model="jenisTugas" required
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"> <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="uraian">Uraian</option>
                                <option value="gabungan">Gabungan (Pilihan Ganda & Uraian)</option>
                            </select>
                        </div>

                        <hr class="my-4"> <h3 class="text-lg font-bold mb-3">Daftar Soal</h3> <template x-for="(soal, index) in soals" :key="index">
                            <div class="border border-gray-300 rounded-lg p-3 mb-3"> <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2">
                                    <h4 class="text-base font-semibold" x-text="'Soal ' + (index + 1)"></h4> <button type="button" @click="hapusSoal(index)"
                                        class="mt-1 sm:mt-0 px-3 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                        Hapus Soal
                                    </button>
                                </div>

                                <template x-if="jenisTugas === 'gabungan'">
                                    <div class="mb-3">
                                        <label class="block font-medium text-sm text-gray-700">Tipe Soal Ini</label>
                                        <select :name="'soal[' + index + '][tipe_soal]'" x-model="soal.tipe_soal"
                                            class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm">
                                            <option value="pilihan_ganda">Pilihan Ganda</option>
                                            <option value="uraian">Uraian</option>
                                        </select>
                                    </div>
                                </template>

                                <template x-if="jenisTugas !== 'gabungan'">
                                    <input type="hidden" :name="'soal[' + index + '][tipe_soal]'"
                                        x-model="soal.tipe_soal" />
                                </template>

                                
                                <div class="border-t border-gray-200 mt-3 pt-3"> <button type="button" @click="soal.showOptionalFields = !soal.showOptionalFields"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium mb-2"> <span x-show="!soal.showOptionalFields">+ Tambah Keterangan / Gambar
                                            (Opsional)</span>
                                        <span x-show="soal.showOptionalFields" style="display: none;">- Sembunyikan
                                            Keterangan / Gambar</span>
                                    </button>

                                    <div x-show="soal.showOptionalFields" style="display: none;"
                                        class="space-y-3 p-3 bg-gray-50 rounded-md border border-gray-200"> <div>
                                            <label class="block font-medium text-sm text-gray-700">Keterangan
                                                Soal</label>
                                            <textarea :name="'soal[' + index + '][keterangan_soal]'" x-model="soal.keterangan_soal" rows="2"
                                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm"
                                                placeholder="Misal: Perhatikan gambar berikut untuk menjawab..."></textarea>
                                        </div>
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700">Gambar Soal</label>
                                            <input type="file" :name="'soal[' + index + '][gambar_soal]'"
                                                class="block mt-1 w-full text-sm text-gray-500
                                                file:mr-4 file:py-1.5 file:px-3
                                                file:rounded-md file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100"> </div>
                                    </div>
                                </div>
                                

                                <div class="mb-3 mt-3"> <label class="block font-medium text-sm text-gray-700">Pertanyaan</label>
                                    <textarea :name="'soal[' + index + '][pertanyaan]'" x-model="soal.pertanyaan" rows="2" required
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm"></textarea>
                                </div>


                                
                                <template x-if="soal.tipe_soal === 'pilihan_ganda'">
                                    <div class="pl-3 border-l-4 border-gray-200 mb-3"> <h5 class="font-semibold text-sm mb-2">Opsi Jawaban</h5> <template x-for="(opsi, opsiIndex) in soal.opsi" :key="opsiIndex">
                                            <div class="flex items-start mb-2 p-2 border rounded-md bg-gray-50"> <input :name="'soal[' + index + '][kunci_jawaban_pg]'"
                                                    :value="opsiIndex" type="radio"
                                                    x-model="soal.kunci_jawaban_pg" class="mr-2 mt-2 focus:ring-blue-500 text-sm">

                                                
                                                <span class="mr-2 font-medium w-4 mt-1.5 text-sm"
                                                    x-text="String.fromCharCode(65 + opsiIndex) + '.'"></span> <div class="flex-1">
                                                    
                                                    
                                                    <div class="mb-1">
                                                        <select :name="'soal[' + index + '][opsi][' + opsiIndex + '][tipe]'" 
                                                                x-model="opsi.tipe"
                                                                class="text-xs rounded-md border-gray-300 shadow-sm"
                                                                style="padding-top: 2px; padding-bottom: 2px;"> <option value="teks">Teks</option>
                                                            <option value="gambar">Gambar</option>
                                                        </select>
                                                    </div>
                                                    
                                                    
                                                    <div x-show="opsi.tipe === 'teks'">
                                                        <input type="text"
                                                            :name="'soal[' + index + '][opsi][' + opsiIndex + '][konten_teks]'"
                                                            x-model="opsi.konten_teks" placeholder="Teks Opsi Jawaban"
                                                            class="block w-full rounded-md shadow-sm border-gray-300 text-sm py-1"> </div>

                                                    
                                                    
                                                    <div x-show="opsi.tipe === 'gambar'" style="display: none;">
                                                        <input type="file" 
                                                            :name="'soal[' + index + '][opsi][' + opsiIndex + '][konten_gambar]'"
                                                            class="block w-full text-xs text-gray-500
                                                                file:mr-2 file:py-1 file:px-2
                                                                file:rounded-md file:border-0 file:text-xs file:font-semibold
                                                                file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"> <span class="text-xs text-gray-500 italic">Upload gambar untuk opsi ini.</span>
                                                    </div>
                                                </div>
                                                
                                                
                                                <button type="button" @click="hapusOpsi(index, opsiIndex)"
                                                    class="ml-2 text-red-500 hover:text-red-700 text-xs mt-1.5">
                                                    Hapus
                                                </button>
                                            </div>
                                        </template>
                                        
                                        <button type="button" @click="tambahOpsi(index)"
                                            class="mt-1 px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">
                                            + Tambah Opsi
                                        </button>
                                    </div>
                                </template>
                                


                                <template x-if="soal.tipe_soal === 'uraian'">
                                    <div class="pl-3 border-l-4 border-blue-200 mb-3"> <label class="block font-medium text-sm text-gray-700">Kunci Jawaban
                                            (Uraian)</label>
                                        <textarea :name="'soal[' + index + '][kunci_jawaban_uraian]'" x-model="soal.kunci_jawaban_uraian" rows="2"
                                            class="block mt-1 w-full rounded-md shadow-sm border-gray-300 text-sm"></textarea>
                                    </div>
                                </template>

                            </div>
                        </template>

                        <button type="button" @click="tambahSoal()"
                            class="mb-4 px-3 py-1.5 bg-gray-800 text-white rounded-md hover:bg-gray-700 text-sm"> + Tambah Soal
                        </button>

                        <hr class="my-4"> <div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700">
                                Simpan Tugas
                            </button>
                        </div>
                    </form>

                    @php
                        // ▼▼▼ PERSIAPKAN DATA (MODIFIKASI) ▼▼▼
                        $default_opsi = [
                            ['tipe' => 'teks', 'konten_teks' => ''],
                            ['tipe' => 'teks', 'konten_teks' => ''],
                        ];
                        
                        $default_soal = [
                            [
                                'pertanyaan' => '',
                                'keterangan_soal' => '',
                                'showOptionalFields' => false,
                                'tipe_soal' => 'pilihan_ganda',
                                'opsi' => $default_opsi, // <-- Gunakan struktur baru
                                'kunci_jawaban_pg' => null,
                                'kunci_jawaban_uraian' => '',
                            ],
                        ];

                        // Ambil data lama jika ada (setelah error validasi), atau gunakan default
                        $soals_data = old('soal') ? array_values(old('soal')) : $default_soal;
                        
                        // Pastikan data 'opsi' ada jika data 'old' digunakan
                        foreach ($soals_data as $key => $soal) {
                            if (empty($soal['opsi'])) {
                                $soals_data[$key]['opsi'] = $default_opsi;
                            }
                            // Pastikan properti showOptionalFields ada (untuk data dari 'old()')
                            if (!isset($soals_data[$key]['showOptionalFields'])) {
                                $soals_data[$key]['showOptionalFields'] = false;
                            }
                        }
                        
                        $soals_json = json_encode($soals_data);
                        // ▲▲▲ AKHIR DATA ▲▲▲
                    @endphp

                    {{-- ▼▼▼ SCRIPT ALPINE.JS (MODIFIKASI) ▼▼▼ --}}
                    <script>
                        function tugasForm() {
                            return {
                                jenisTugas: '{{ old('jenis_tugas', 'pilihan_ganda') }}',
                                soals: {!! $soals_json !!},

                                init() {
                                    this.$watch('jenisTugas', (newJenis) => {
                                        this.soals.forEach(soal => {
                                            if (newJenis !== 'gabungan') {
                                                soal.tipe_soal = newJenis;
                                            }
                                        });
                                    });
                                    let initialJenis = this.jenisTugas;
                                    this.soals.forEach(soal => {
                                        if (initialJenis !== 'gabungan') {
                                            soal.tipe_soal = initialJenis;
                                        }
                                        // Pastikan properti default ada (untuk data dari 'old()')
                                        if (!soal.opsi) soal.opsi = [{ tipe: 'teks', konten_teks: '' }, { tipe: 'teks', konten_teks: '' }];
                                        if (soal.kunci_jawaban_pg === undefined) soal.kunci_jawaban_pg = null;
                                        if (soal.kunci_jawaban_uraian === undefined) soal.kunci_jawaban_uraian = '';
                                        if (soal.showOptionalFields === undefined) soal.showOptionalFields = false;
                                        
                                        // Pastikan 'konten_teks' ada di data 'opsi'
                                        soal.opsi.forEach(op => {
                                            if (op.tipe === 'teks' && op.konten_teks === undefined) {
                                                op.konten_teks = '';
                                            }
                                        });
                                    });
                                },

                                tambahSoal() {
                                    let tipeSoalBaru = this.jenisTugas === 'gabungan' ? 'pilihan_ganda' : this.jenisTugas;
                                    this.soals.push({
                                        pertanyaan: '',
                                        keterangan_soal: '',
                                        showOptionalFields: false,
                                        tipe_soal: tipeSoalBaru,
                                        opsi: [{ tipe: 'teks', konten_teks: '' }, { tipe: 'teks', konten_teks: '' }], // <-- Struktur baru
                                        kunci_jawaban_pg: null,
                                        kunci_jawaban_uraian: ''
                                    });
                                },
                                hapusSoal(index) {
                                    this.soals.splice(index, 1);
                                },
                                tambahOpsi(soalIndex) {
                                    this.soals[soalIndex].opsi.push({ tipe: 'teks', konten_teks: '' }); // <-- Struktur baru
                                },
                                hapusOpsi(soalIndex, opsiIndex) {
                                    this.soals[soalIndex].opsi.splice(opsiIndex, 1);
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