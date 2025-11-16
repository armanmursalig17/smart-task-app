<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg leading-tight">
            Tugas: {{ $kelas->nama_kelas }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto "> <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-4 sm:p-4 text-gray-900"> 
                    <div class="mb-3"> <a href="{{ route('kelas.index') }}"
                            class="inline-flex items-center px-3 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            &larr; Kembali
                        </a>
                    </div>

                    <h3 class="text-xl font-bold mb-1"> Nama Kelas:
                    </h3>
                    <p class="text-base text-gray-700 mb-4"> {{ $kelas->nama_kelas }}
                    </p>

                    <hr class="my-4"> <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3"> <h3 class="text-xl font-bold mb-2 sm:mb-0">Daftar Tugas</h3> <a href="{{ route('tugas.create', ['kelas_id' => $kelas->id]) }}"
                            class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            + Tugas Baru
                        </a>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg"> @forelse ($kelas->tugas as $tugas)
                            <div class="border-b border-gray-200 py-2 flex flex-col sm:flex-row justify-between items-start sm:items-center"> <div class="mb-2 sm:mb-0">
                                    <h4 class="text-base font-semibold">{{ $tugas->nama_tugas }}</h4> <span class="text-xs text-gray-600 capitalize"> Jenis: {{ str_replace('_', ' ', $tugas->jenis_tugas) }}
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row items-start sm:items-center"> 
                                    <a href="{{ route('tugas.detail', $tugas) }}"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm sm:text-xs"> Detail Tugas
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-3 text-sm"> Belum ada tugas untuk kelas ini.
                            </p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>