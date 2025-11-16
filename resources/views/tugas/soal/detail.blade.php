<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg leading-tight"> Detail Tugas: {{ $tugas->nama_tugas }}
        </h2>
    </x-slot>


    <div class="py-0">
        <div class="max-w-full mx-auto"> <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-4 sm:p-4 text-gray-900"> <div class="mb-3"> <a href="{{ route('kelas.detail', $tugas->kelas_id) }}"
                            class="inline-flex items-center px-3 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            &larr; Kembali
                        </a>
                    </div>

                    <h3 class="text-xl font-bold mb-1"> Nama Tugas:
                    </h3>
                    <p class="text-base text-gray-700 mb-1"> {{ $tugas->nama_tugas }}
                    </p>
                    <p class="text-sm text-gray-600 mb-4 capitalize"> Jenis Tugas: {{ str_replace('_', ' ', $tugas->jenis_tugas) }}
                    </p>

                    <hr class="my-4"> <div class="flex justify-between items-center mb-3"> <h3 class="text-xl font-bold">Daftar Soal & Kunci Jawaban</h3> <a href="{{ route('tugas.soal.create', $tugas->id) }}"
                            class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            + Soal
                        </a>
                    </div>



                    <div class="space-y-4"> @forelse ($tugas->soals as $soal)
                            <div class="bg-gray-50 p-3 rounded-lg shadow-sm border border-gray-200"> <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 pb-2 border-b border-gray-200">
                                    <h4 class="text-base font-semibold text-gray-800 mb-1 sm:mb-0"> Soal #{{ $loop->iteration }}
                                        <span class="text-xs font-normal capitalize ml-1">({{ str_replace('_', ' ', $soal->tipe_soal_di_tugas) }})</span>
                                    </h4>
                                    <div class="flex items-center space-x-3"> <a href="{{ route('soal.edit', $soal->id) }}"
                                            class="text-xs font-medium text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline"> Edit
                                        </a>
                                        <form action="{{ route('soal.destroy', $soal->id) }}" method="POST"
                                            class="m-0 p-0"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-medium text-red-600 hover:text-red-900 focus:outline-none focus:underline bg-transparent border-none p-0 cursor-pointer"> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>


                                @if ($soal->keterangan_soal)
                                    <p class="text-gray-600 text-xs italic mb-2"> {{ $soal->keterangan_soal }}
                                    </p>
                                @endif


                                @if ($soal->gambar_soal)
                                    <div class="mb-3"> <img src="{{ Storage::url($soal->gambar_soal) }}" alt="Gambar Soal"
                                            class="max-w-full sm:max-w-sm rounded-md shadow-md border object-contain h-auto max-h-40"> </div>
                                @endif


                                <p class="text-sm text-gray-800 mb-2 mt-1" > {{ $soal->pertanyaan }}</p>



                                
                                @if ($soal->opsiJawabans->isNotEmpty())
                                    <div class="ml-3 space-y-1 mb-2 text-sm"> @foreach ($soal->opsiJawabans as $opsi)
                                            <div class="flex items-start text-gray-700">
                                                <strong
                                                    class="w-4 flex-shrink-0 text-xs mt-0.5">{{ chr(65 + $loop->index) }}.</strong> @if ($opsi->tipe_opsi == 'teks')
                                                    <span>{{ $opsi->opsi_teks }}</span>
                                                @elseif ($opsi->tipe_opsi == 'gambar' && $opsi->opsi_gambar)
                                                    <img src="{{ Storage::url($opsi->opsi_gambar) }}" alt="Opsi Gambar"
                                                        class="max-w-xs h-16 rounded border shadow-sm object-contain"> @else
                                                    <span class="text-gray-400 italic text-xs">(Opsi tidak valid)</span> @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="bg-green-100 border-l-4 border-green-500 p-2 rounded-md mt-3 text-sm"> <strong class="text-green-800">Kunci Jawaban:</strong>

                                    @if ($soal->tipe_soal_di_tugas == 'pilihan_ganda')
                                        @php
                                            $kunci_index = (int) $soal->kunci_jawaban;
                                            $huruf_kunci = chr(65 + $kunci_index);
                                            $opsi_benar = $soal->opsiJawabans[$kunci_index] ?? null;
                                        @endphp

                                        <span class="text-green-700 font-semibold">{{ $huruf_kunci }}.</span>

                                        @if ($opsi_benar && $opsi_benar->tipe_opsi == 'teks')
                                            <span class="text-green-700">{{ $opsi_benar->opsi_teks }}</span>
                                        @elseif($opsi_benar && $opsi_benar->tipe_opsi == 'gambar')
                                            <span class="text-green-700 italic text-xs">(Jawaban Gambar)</span>
                                        @else
                                            <span class="text-green-700 italic text-xs">(Kunci tidak ditemukan)</span>
                                        @endif
                                    @else
                                        <span class="text-green-700"
                                            style="white-space: pre-wrap;">{{ $soal->kunci_jawaban ?: '(Tidak ada kunci jawaban)' }}</span>
                                    @endif

                                </div>
                            </div>

                        @empty
                            <div class="text-center py-6 bg-gray-50 rounded-lg"> <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true"> <path vector-effect="non-scaling-stroke" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2z" />
                                </svg>
                                <h3 class="mt-1 text-sm font-semibold text-gray-900">Belum Ada Soal</h3> <p class="mt-0.5 text-xs text-gray-500">Belum ada soal yang ditambahkan untuk tugas ini.
                                </p> </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>