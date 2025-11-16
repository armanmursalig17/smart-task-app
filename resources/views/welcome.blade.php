<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Portal Tugas - SDN 9 Kota Barat</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="icon" href="{{ asset('img/tutwuri.png') }}">


    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="font-sans antialiased text-gray-800">


    <div class="fixed inset-0 -z-10 bg-cover bg-center"
        style="background-image: url('{{ asset('img/siswa.png') }}'); filter: brightness(50%);">
    </div>




    <header class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md transition-all" x-data="{ atTop: true }"
        @scroll.window="atTop = (window.scrollY > 10) ? false : true" :class="{ 'py-4': atTop, 'py-2': !atTop }">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="#" class="flex items-center space-x-2 text-xl font-bold text-blue-600 hover:text-blue-800">
                <!-- Logo -->
                <img src="{{ asset('img/tutwuri.png') }}" alt="Logo" class="h-9 w-9">
                <!-- Teks -->
                <span>SMART TASKS </span>
            </a>


            <nav class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-600 hover:text-blue-600 text-sm font-medium transition-colors">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main>

        <section id="hero" class="pt-40 pb-24">
            <div class="container mx-auto px-6 text-center">

                <div x-data="{ shown: false }" x-init="setTimeout(() => { shown = true }, 300)"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all ease-out duration-1000">
                    <h1 class="text-2xl md:text-4xl font-bold text-yellow-300 mb-6 leading-tight animate-gold-pulse">
                        Selamat Datang di Smart Tasks SDN 09 Kota Barat
                    </h1>
                    <p class="text-lg md:text-xl text-yellow-100 mb-10 max-w-2xl mx-auto animate-gold-pulse">
                        Sistem digital yang digunakan untuk mengelola, memantau, dan mengumpulkan tugas siswa antara
                        guru dan siswa di SDN 9 Kota Barat.
                    </p>


                    <div class="max-w-lg mx-auto bg-white p-6 md:p-8 rounded-xl shadow-xl border border-gray-200">
                        <h3 class="text-xl font-semibold mb-4 text-blue-600">Mulai Pengerjaan Tugas</h3>

                        @error('token')
                            <p class="text-sm text-red-600 p-3 mb-4 bg-red-50 border border-red-200 rounded-md">
                                {{ $message }}</p>
                        @enderror

                        <form action="{{ route('siswa.checkToken') }}" method="POST"
                            class="flex flex-col md:flex-row gap-3">
                            @csrf
                            <input type="text" name="token" id="token" placeholder="Masukkan Token Akses Tugas"
                                required value="{{ old('token') }}"
                                class="flex-grow px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 shadow-sm text-base  tracking-widest">

                            <button type="submit"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                                Cek Token
                            </button>
                        </form>
                    </div>
                </div>


            </div>
        </section>



    </main>

    <footer class="py-10 bg-gray-800 text-gray-400">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} SDN 9 Kota Barat. Hak Cipta Dilindungi.</p>
            <p class="text-sm">By @PTI FT-UNG kelompok WA2N </p>
        </div>
    </footer>

</body>

</html>
