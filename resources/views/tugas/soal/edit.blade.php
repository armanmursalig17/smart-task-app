<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Edit Soal untuk Tugas: {{ $soal->tugas->nama_tugas }}
        </h2>
    </x-slot>

    
    @php
        // LOGIKA PHP TIDAK DIUBAH, HANYA DIBIARKAN APA ADANYA
        $showOptionalFields = (
            old('keterangan_soal') ||
            $errors->has('keterangan_soal') ||
            $errors->has('gambar_soal_baru') ||
            $soal->gambar_soal ||
            $soal->keterangan_soal
        ) ? 'true' : 'false';
        
        $opsi_items_data = [];
        
        if (old('opsi')) {
            $oldOpsiArray = array_values(old('opsi')); 
            foreach ($oldOpsiArray as $index => $oldOpsi) {
                $gambar_path = $oldOpsi['gambar_lama'] ?? null; 
                $opsi_items_data[] = [
                    'local_id' => 'old_' . $index,
                    'tipe' => $oldOpsi['tipe'] ?? 'teks',
                    'konten_teks' => $oldOpsi['konten_teks'] ?? '',
                    'konten_gambar_path' => $gambar_path, 
                    'konten_gambar_url' => $gambar_path ? Storage::url($gambar_path) : null, 
                ];
            }
        }
        elseif ($soal->tipe_soal_di_tugas == 'pilihan_ganda' && $soal->opsiJawabans->count() > 0) {
            foreach ($soal->opsiJawabans as $opsi) {
                $opsi_items_data[] = [
                    'local_id' => 'item_' . $opsi->id,
                    'tipe' => $opsi->tipe_opsi,
                    'konten_teks' => $opsi->opsi_teks ?? '',
                    'konten_gambar_path' => $opsi->opsi_gambar,
                    'konten_gambar_url' => $opsi->opsi_gambar ? Storage::url($opsi->opsi_gambar) : null,
                ];
            }
        }
        else if ($soal->tipe_soal_di_tugas == 'pilihan_ganda') {
            $opsi_items_data = [
                ['local_id' => 'new_1', 'tipe' => 'teks', 'konten_teks' => '', 'konten_gambar_path' => null, 'konten_gambar_url' => null],
                ['local_id' => 'new_2', 'tipe' => 'teks', 'konten_teks' => '', 'konten_gambar_path' => null, 'konten_gambar_url' => null],
            ];
        }
        
        $opsi_items_json = json_encode($opsi_items_data);
        
        $kunci_jawaban_pg = old('kunci_jawaban_pg', $soal->kunci_jawaban);
        $kunci_jawaban_pg = ($kunci_jawaban_pg === '' || $kunci_jawaban_pg === null) ? null : (int)$kunci_jawaban_pg;
        $kunci_jawaban_json = json_encode($kunci_jawaban_pg);
    @endphp

    
    <div class="max-w-full mx-auto py-4">
        <div class="bg-white overflow-hidden shadow-md rounded-lg">
            
            <div x-data="{
                    showOptionalFields: {{ $showOptionalFields }},
                    opsi_items: {{ $opsi_items_json }},
                    kunci_jawaban_index: {{ $kunci_jawaban_json }},
                    nextId: {{ count($opsi_items_data) + 1 }},
                    
                    addOpsi() {
                        this.opsi_items.push({
                            local_id: 'new_' + this.nextId++,
                            tipe: 'teks',
                            konten_teks: '',
                            konten_gambar_path: null,
                            konten_gambar_url: null
                        });
                    },
                    
                    removeOpsi(index) {
                        if (this.opsi_items.length > 2) {
                            this.opsi_items.splice(index, 1);
                            if (this.kunci_jawaban_index === index) {
                                this.kunci_jawaban_index = null;
                            } else if (this.kunci_jawaban_index > index) {
                                this.kunci_jawaban_index--;
                            }
                        } else {
                            alert('Minimal harus ada 2 opsi jawaban!');
                        }
                    },
                    
                    handleTipeChange(index) {
                        if (this.opsi_items[index].tipe === 'teks') {
                            this.opsi_items[index].konten_gambar_path = null;
                            this.opsi_items[index].konten_gambar_url = null;
                        } else {
                            this.opsi_items[index].konten_teks = '';
                        }
                        
                        let fileInput = document.querySelector(`input[name='opsi[${index}][konten_gambar]']`);
                        if(fileInput) {
                            fileInput.value = '';
                        }
                    }
                }" 
                x-cloak 
                class="p-6 md:p-8 text-slate-900"
            >
                <form action="{{ route('soal.update', $soal->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    {{-- Tipe Soal (Read-only) --}}
                    <div>
                        <label class="block font-medium text-sm text-slate-700">Tipe Soal</label>
                        <p class="mt-1 text-lg font-semibold text-slate-600">{{ str_replace('_', ' ', ucwords($soal->tipe_soal_di_tugas)) }}</p>
                    </div>
                    
                    {{-- 1. Pertanyaan --}}
                    <div>
                        <label for="pertanyaan" class="block font-medium text-sm text-slate-700">
                            Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="pertanyaan" id="pertanyaan" rows="4" required 
                                  class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                        @error('pertanyaan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Toggle Button --}}
                    <div>
                        <button type="button" 
                                @click="showOptionalFields = !showOptionalFields" 
                                x-text="showOptionalFields ? '- Sembunyikan Keterangan & Gambar' : '+ Tambah/Edit Keterangan & Gambar (Opsional)'"
                                class="py-2 px-4 rounded-md font-semibold text-sm transition duration-150 ease-in-out bg-white border border-slate-300 text-slate-700 hover:bg-slate-50">
                        </button>
                    </div>
                    
                    {{-- Field Opsional (Responsive: Mobile-friendly styling retained) --}}
                    <div x-show="showOptionalFields" x-transition class="space-y-6 border-l-4 border-slate-200 pl-4">
                        {{-- 2. Keterangan Soal --}}
                        <div>
                            <label for="keterangan_soal" class="block font-medium text-sm text-slate-700">Keterangan Soal (Opsional)</label>
                            <textarea name="keterangan_soal" id="keterangan_soal" rows="3"
                                      class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan_soal', $soal->keterangan_soal) }}</textarea>
                            @error('keterangan_soal')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- 3. Gambar Soal --}}
                        <div>
                            <label class="block font-medium text-sm text-slate-700">Gambar Soal</label>
                            
                            @if ($soal->gambar_soal)
                                <div class="mt-2 p-4 border border-slate-200 rounded-md">
                                    <img src="{{ Storage::url($soal->gambar_soal) }}" alt="Gambar Soal" class="max-w-xs rounded-md">
                                    <div class="mt-2">
                                        <label class="inline-flex items-center text-sm">
                                            <input type="checkbox" name="hapus_gambar" value="1" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="ml-2 text-slate-700">Centang untuk menghapus gambar ini</span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p class="mt-1 text-sm text-slate-500">Tidak ada gambar untuk soal ini.</p>
                            @endif
                            
                            <div class="mt-2">
                                <label for="gambar_soal_baru" class="block text-sm font-medium text-slate-700 mb-1">
                                    @if($soal->gambar_soal) Ganti Gambar @else Upload Gambar @endif
                                </label>
                                <input type="file" name="gambar_soal_baru" id="gambar_soal_baru" accept="image/*"
                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <small class="mt-1 text-xs text-slate-500">
                                    @if($soal->gambar_soal) Mengupload file baru akan menggantikan gambar di atas. @endif
                                    (Tipe: jpg, png, gif. Max: 2MB)
                                </small>
                            </div>
                            @error('gambar_soal_baru')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- PEMBATAS --}}
                    <hr class="border-slate-200">
                    
                    {{-- OPSI JAWABAN (Pilihan Ganda) --}}
                    @if ($soal->tipe_soal_di_tugas == 'pilihan_ganda')
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-slate-900">Opsi & Kunci Jawaban</h3>
                                <p class="mt-1 text-sm text-slate-600">Pilih radio button untuk Kunci Jawaban yang benar.</p>
                            </div>
                            
                            <div class="space-y-4">
                                <template x-for="(item, index) in opsi_items" :key="item.local_id">
                                    <div class="border border-slate-200 bg-slate-50 p-4 rounded-lg shadow-sm space-y-4">
                                        
                                        {{-- Header Opsi --}}
                                        <div class="flex justify-between items-center">
                                            <label class="inline-flex items-center font-semibold">
                                                <input type="radio" name="kunci_jawaban_pg" :value="index" x-model="kunci_jawaban_index"
                                                        class="mr-2 border-slate-400 text-indigo-600 focus:ring-indigo-500">
                                                Opsi <span x-text="String.fromCharCode(65 + index)" class="ml-1 font-bold text-indigo-700 w-5 text-center"></span> (Set sebagai Kunci Jawaban)
                                            </label>

                                            <button type="button" @click="removeOpsi(index)" x-show="opsi_items.length > 2"
                                                    class="py-1 px-3 rounded-md text-xs bg-red-600 text-white hover:bg-red-700 transition duration-150 ease-in-out">
                                                Hapus
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            {{-- Tipe Opsi --}}
                                            <div>
                                                <label class="block font-medium text-sm text-slate-700">Tipe Opsi</label>
                                                <select :name="'opsi[' + index + '][tipe]'" x-model="item.tipe" @change="handleTipeChange(index)"
                                                        class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="teks">Teks</option>
                                                    <option value="gambar">Gambar</option>
                                                </select>
                                            </div>

                                            {{-- Konten Teks (md:col-span-2 ensures full width on mobile) --}}
                                            <div class="md:col-span-2" x-show="item.tipe === 'teks'">
                                                <label class="block font-medium text-sm text-slate-700">Teks Opsi</M>
                                                <input type="text" :name="'opsi[' + index + '][konten_teks]'"
                                                     x-model="item.konten_teks" placeholder="Masukkan teks opsi"
                                                     class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                 @error("opsi.*.konten_teks") 
                                                     <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                 @enderror
                                            </div>
                                            
                                            {{-- Konten Gambar (md:col-span-2 ensures full width on mobile) --}}
                                            <div class="md:col-span-2 space-y-2" x-show="item.tipe === 'gambar'">
                                                <label class="block font-medium text-sm text-slate-700">Gambar Opsi</label>
                                                
                                                {{-- Preview Gambar Lama --}}
                                                <div x-show="item.konten_gambar_url" class="p-2 border border-slate-200 rounded-md w-fit">
                                                    <img :src="item.konten_gambar_url" alt="Preview" class="max-w-[150px] sm:max-w-[200px] rounded">
                                                    <p class="mt-1 text-xs text-slate-500">Gambar saat ini</p>
                                                </div>
                                                
                                                {{-- Hidden Input untuk Path Gambar Lama --}}
                                                <input type="hidden" :name="'opsi[' + index + '][gambar_lama]'" x-model="item.konten_gambar_path">
                                                
                                                {{-- Upload Gambar Baru --}}
                                                <div>
                                                    <input type="file" :name="'opsi[' + index + '][konten_gambar]'" accept="image/*"
                                                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                     <small class="mt-1 text-xs text-slate-500">Tipe: jpg, png, gif. Max: 1MB</small>
                                                     @error("opsi.*.konten_gambar") 
                                                         <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                     @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            {{-- Tombol Tambah Opsi --}}
                            <div>
                                <button type="button" @click="addOpsi()"
                                        class="py-2 px-4 rounded-md font-semibold text-sm transition duration-150 ease-in-out bg-gray-400 text-white hover:bg-indigo-700">
                                    + Tambah Opsi Jawaban
                                </button>
                            </div>
                            
                            {{-- Error Global Opsi --}}
                            @error('kunci_jawaban_pg')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('opsi')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                    {{-- KUNCI JAWABAN (Uraian) --}}
                    @elseif ($soal->tipe_soal_di_tugas == 'uraian')
                        <div>
                            <h3 class="text-lg font-medium text-slate-900">Kunci Jawaban Uraian</h3>
                            <p class="mt-1 text-sm text-slate-600">(Opsional) Masukkan referensi kunci jawaban untuk mempermudah koreksi.</p>
                            <textarea name="kunci_jawaban_uraian" rows="5"
                                      class="mt-2 block w-full border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('kunci_jawaban_uraian', $soal->kunci_jawaban) }}</textarea>
                            @error('kunci_jawaban_uraian')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    
                    {{-- Tombol Aksi --}}
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <a href="{{ route('tugas.detail', $soal->tugas_id) }}"
                           class="py-2 px-4 rounded-md font-semibold text-sm transition duration-150 ease-in-out bg-white border border-slate-300 text-slate-700 hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit"
                                class="py-2 px-4 rounded-md font-semibold text-sm transition duration-150 ease-in-out bg-indigo-600 text-white hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        console.log('Alpine.js sudah dimuat oleh layout.');
        console.log('Data opsi awal:', @json($opsi_items_data));
    </script>
    @endpush
</x-app-layout>