<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Daftar Tugas Terbit') }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto ">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    <span class="font-medium">Berhasil!</span> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('terbitkantugas.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Terbitkan Tugas Baru
                        </a>


                        @if ($terbitanDitutup->isNotEmpty())
                            <button id="toggleHistoryBtn" type="button"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Lihat Riwayat Tugas (Expired)
                            </button>
                        @endif
                    </div>

                    <h3 class="text-lg font-semibold mb-3">Tugas yang Sedang Aktif</h3>


                    @if ($terbitanAktif->isEmpty())
                        <p class="text-gray-500">Belum ada tugas yang diterbitkan dan berstatus **Aktif**.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                @include('terbitkantugas._table_header')
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($terbitanAktif as $terbitan)
                                        @include('terbitkantugas._table_row', ['terbitan' => $terbitan])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $terbitanAktif->links() }}
                        </div>
                    @endif


                    @if ($terbitanDitutup->isNotEmpty())
                        <div id="historyTableContainer" class="mt-8" style="display:none;">
                            <h3 class="text-lg font-semibold mb-3 border-t pt-4">Riwayat Tugas (Tidak Aktif)</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    @include('terbitkantugas._table_header')
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($terbitanDitutup as $terbitan)
                                            @include('terbitkantugas._table_row', [
                                                'terbitan' => $terbitan,
                                            ])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    
    <div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 mx-4">
            <h3 class="text-xl font-bold mb-4" id="modalTitle">Aksi Tugas: <span id="modalTaskName"></span></h3>
            <p class="mb-4 text-sm text-gray-500">Status saat ini: <span id="modalTaskStatus"
                    class="font-semibold"></span></p>

            <div class="flex flex-col space-y-3">

              
                <a id="lihatDetailBtn" href="#"
                    class="px-4 py-2 text-center bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition duration-150">
                    Lihat Detail Tugas
                </a>

               
                <form id="reUseForm" method="POST" class="hidden">
                    @csrf
                    <button type="submit" id="reUseBtn"
                        class="w-full px-4 py-2 text-center bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition duration-150">
                        Buat Token Baru
                    </button>
                </form>

              
                <form id="deactivateForm" method="POST" class="hidden">
                    @csrf
                    <button type="submit" id="deactivateBtn"
                        class="w-full px-4 py-2 text-center bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 transition duration-150">
                        Nonaktifkan Tugas
                    </button>
                </form>


                
                <button type="button" id="closeModalBtn"
                    class="px-4 py-2 text-center bg-gray-300 text-gray-800 font-semibold rounded-md hover:bg-gray-400 transition duration-150">
                    Tutup
                </button>
            </div>
        </div>
    </div>


   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const toggleBtn = document.getElementById('toggleHistoryBtn');
            const historyContainer = document.getElementById('historyTableContainer');

            if (toggleBtn && historyContainer) {
                toggleBtn.addEventListener('click', function() {
                    if (historyContainer.style.display === 'none') {
                        historyContainer.style.display = 'block';
                        toggleBtn.textContent = 'Sembunyikan Riwayat Tugas (Expired)';
                    } else {
                        historyContainer.style.display = 'none';
                        toggleBtn.textContent =
                            'Lihat Riwayat Tugas (Expired)'; 
                    }
                });
            }

            
            const modal = document.getElementById('actionModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const modalTaskName = document.getElementById('modalTaskName');
            const modalTaskStatus = document.getElementById('modalTaskStatus');
            const lihatDetailBtn = document.getElementById('lihatDetailBtn');

           
            const reUseForm = document.getElementById('reUseForm');
            const deactivateForm = document.getElementById('deactivateForm'); 

            const reUseRouteTemplate = "{{ route('terbitkantugas.reUse', 'TASK_ID') }}";
            const deactivateRouteTemplate = "{{ route('terbitkantugas.deactivate', 'TASK_ID') }}"; // BARU
            const showRouteTemplate = "{{ route('terbitkantugas.show', 'TASK_ID') }}";


            document.querySelectorAll('.open-detail-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.dataset.id;
                    const taskStatus = this.dataset.status;
                    const taskName = this.dataset.tugasNama;

                    
                    modalTaskName.textContent = taskName;
                    modalTaskStatus.textContent = taskStatus === 'aktif' ? 'Active' : 'Expired';

                  
                    lihatDetailBtn.href = showRouteTemplate.replace('TASK_ID', taskId);

                    
                    reUseForm.classList.add('hidden');
                    deactivateForm.classList.add('hidden'); 

                    if (taskStatus === 'aktif') {
                        

                       
                        reUseForm.classList.remove('hidden');
                        reUseForm.action = reUseRouteTemplate.replace('TASK_ID', taskId);
                        reUseForm.onsubmit = () => confirm(
                            'Apakah Anda yakin ingin menonaktifkan tugas ini dan menerbitkannya kembali dengan Token Akses baru?'
                        );

                       
                        deactivateForm.classList.remove('hidden');
                        deactivateForm.action = deactivateRouteTemplate.replace('TASK_ID', taskId);
                        deactivateForm.onsubmit = () => confirm(
                            'Apakah Anda yakin ingin menonaktifkan tugas ini? Status akan menjadi Expired.'
                        );

                    } else if (taskStatus === 'ditutup') {
                        
                    }

                    
                    modal.style.display = 'flex';
                });
            });

           
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>