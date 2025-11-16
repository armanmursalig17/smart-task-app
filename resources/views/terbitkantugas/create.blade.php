<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Form Terbitkan Tugas') }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto ">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('terbitkantugas.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="kelas_id" class="block font-medium text-sm text-gray-700">Pilih Kelas</label>
                            <select name="kelas_id" id="kelas_id" required
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tugas_id" class="block font-medium text-sm text-gray-700">Pilih Tugas</label>
                            <select name="tugas_id" id="tugas_id" required disabled
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full bg-gray-100">
                                <option value="">-- Pilih Tugas (Pilih Kelas Dulu) --</option>
                              
                                @foreach ($tugas as $t)
                                    <option class="tugas-option kelas-{{ $t->kelas_id }}" value="{{ $t->id }}"
                                        data-kelas-id="{{ $t->kelas_id }}" style="display:none;">
                                        [{{ $t->jenis_tugas }}] - {{ $t->nama_tugas }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Pilih kelas terlebih dahulu untuk melihat daftar tugas yang sesuai.
                            </p>
                            @error('tugas_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="durasi_menit" class="block font-medium text-sm text-gray-700">Durasi Pengerjaan
                                (Menit, Opsional)</label>
                            <input type="number" name="durasi_menit" id="durasi_menit"
                                value="{{ old('durasi_menit') }}" min="1" placeholder="Cth: 60"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                            <p class="text-xs text-gray-500 mt-1">
                                Masukkan durasi dalam menit. Kosongkan jika waktu pengerjaan tidak dibatasi.
                            </p>
                            @error('durasi_menit')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Terbitkan Tugas Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk memfilter Tugas berdasarkan Kelas yang dipilih --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelasSelect = document.getElementById('kelas_id');
            const tugasSelect = document.getElementById('tugas_id');
            const tugasOptions = document.querySelectorAll('.tugas-option');

            function filterTugas() {
                const selectedKelasId = kelasSelect.value;

                // Reset drop-down Tugas
                tugasSelect.innerHTML = '<option value="">-- Pilih Tugas --</option>';
                tugasSelect.disabled = true;
                tugasSelect.classList.add('bg-gray-100');
                tugasSelect.classList.remove('bg-white');


                if (selectedKelasId) {
                    let tasksFound = false;
                    tugasOptions.forEach(option => {
                        if (option.dataset.kelasId === selectedKelasId) {
                            // Clone the option and add it to the visible select
                            const clonedOption = option.cloneNode(true);
                            clonedOption.style.display = 'block'; // Ensure it's visible in the new select
                            clonedOption.removeAttribute('class');
                            clonedOption.removeAttribute('data-kelas-id');

                            // Hapus Class ID dari teks yang ditampilkan (yang dulunya ada di controller)
                            let text = clonedOption.textContent.trim();
                            // Hapus (Kelas ID: X) jika ada
                            text = text.replace(/\s*\(Kelas ID: \d+\)$/, '');
                            clonedOption.textContent = text;

                            tugasSelect.appendChild(clonedOption);
                            tasksFound = true;
                        }
                    });

                    if (tasksFound) {
                        tugasSelect.disabled = false;
                        tugasSelect.classList.remove('bg-gray-100');
                        tugasSelect.classList.add('bg-white');
                    } else {
                        tugasSelect.innerHTML = '<option value="">-- Tidak ada tugas Baru untuk kelas ini --</option>';
                    }
                }
            }

           
            filterTugas();

          
            kelasSelect.addEventListener('change', filterTugas);

            @if (old('tugas_id'))
                const selectedTugasId = "{{ old('tugas_id') }}";
                const existingOption = document.querySelector(`option[value="${selectedTugasId}"]`);
                if (existingOption) {
                    existingOption.selected = true;
                }
            @endif
        });
    </script>


</x-app-layout>
