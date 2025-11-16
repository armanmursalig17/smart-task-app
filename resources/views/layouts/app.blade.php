<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trim(strip_tags($header ?? config('app.name'))) }}</title>

    <link rel="icon" href="{{ asset('img/tutwuri.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-900">
    <div class="min-h-screen" x-data="{ mobileSidebarOpen: false, desktopSidebarCollapsed: false }">
        <div class="flex h-screen overflow-hidden">

            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col overflow-hidden">

                <div
                    class="flex justify-between items-center p-4 bg-white border-b border-slate-200 shadow-sm sm:hidden">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center space-x-2 text-xl font-bold text-blue-600 hover:text-blue-800">
                            <!-- Logo -->
                            <img src="{{ asset('img/tutwuri.png') }}" alt="Logo Tutwuri" class="h-9 w-auto">
                            <!-- Teks -->
                            <span>Gudang Soal SDN 09 Kota Barat</span>
                        </a>

                    </div>
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                @isset($header)
                    <header class="bg-blue-800 shadow-sm border-b border-slate-200 hidden sm:block text-white">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">

                            <div class="flex-1 min-w-0">
                                {{ $header }}
                            </div>

                            <div class="relative">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button
                                            class="flex items-center text-sm font-medium rounded-md text-white  hover:text-green-300 transition duration-150 ease-in-out">
                                            <div>{{ Auth::user()->name }}</div>
                                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ __('Profile') }}
                                        </x-dropdown-link>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="flex items-center">
                                                <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                                </svg>
                                                {{ __('Log Out') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>
                    </header>
                @endisset

                <main class="flex-1 overflow-y-auto p-4 sm:p-2 lg:p-8 bg-slate-50">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
</body>

</html>
