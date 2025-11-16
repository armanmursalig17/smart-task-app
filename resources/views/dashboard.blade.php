<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl  leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-full mx-auto ">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-2xl font-bold mb-4">
                        Selamat datang, {{ $userName }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        
                        <div class="bg-blue-100 p-5 rounded-lg shadow-md text-center">
                            <p class="text-sm font-medium text-blue-600">Total Kelas Anda</p>
                            <p class="text-4xl font-extrabold text-blue-800 mt-2">{{ $jumlahKelas }}</p>
                        </div>

                        <div class="bg-green-100 p-5 rounded-lg shadow-md text-center">
                            <p class="text-sm font-medium text-green-600">Tugas Active</p>
                            <p class="text-4xl font-extrabold text-green-800 mt-2">{{ $terbitanAktif }}</p>
                        </div>

                        <div class="bg-red-100 p-5 rounded-lg shadow-md text-center">
                            <p class="text-sm font-medium text-red-600">Tugas Expired</p>
                            <p class="text-4xl font-extrabold text-red-800 mt-2">{{ $terbitanDitutup }}</p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>