<div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-900 bg-opacity-50 sm:hidden"
    x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
</div>

<div x-show="mobileSidebarOpen" class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-slate-800 sm:hidden text-white"
    x-transition:enter="transition-transform ease-out duration-300" x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform ease-in duration-200"
    x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" x-cloak>
    <div class="flex items-center justify-between p-4 border-b border-slate-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('img/tutwuri.png') }}" alt="Logo Tutwuri" class="h-9 w-auto">
            <span class="text-white font-semibold text-lg">SDN 09 Kota Barat</span>
        </a>
        <button @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="route().current('dashboard') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4h.01M12 12h.01M15 12h.01M12 9h.01M15 9h.01M9 9h.01">
                </path>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('kelas.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="route().current('kelas.index') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 7l9-4 9 4M4 10h16M4 10v6m16-6v6M4 16l8 4 8-4" />
            </svg>
            <span>Kelas & Tugas</span>
        </a>

        <a href="{{ route('terbitkantugas.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="route().current('terbitkantugas.index') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                </path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Terbitkan tugas</span>
        </a>

        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="route().current('users.index') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                <path d="M15 9a3 3 0 10-6 0 3 3 0 006 0zM12 11a7 7 0 00-7 7v1h14v-1a7 7 0 00-7-7z" />
            </svg>
            <span>Users</span>
        </a>
    </nav>
</div>

{{-- lll --}}


<div class="hidden sm:flex sm:flex-col bg-slate-800 transition-all duration-300 ease-in-out text-white"
    :class="desktopSidebarCollapsed ? 'w-20' : 'w-64'">
    <div class="flex items-center justify-center h-[65px] border-b border-slate-700">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center">

            <div :class="desktopSidebarCollapsed ? 'block' : 'hidden'">
                <img src="{{ asset('img/tutwuri.png') }}" alt="Logo Tutwuri" class="h-9 w-auto">


            </div>
            <div class="flex items-center gap-2 transition-all" :class="desktopSidebarCollapsed ? 'hidden' : 'flex'">
                <img src="{{ asset('img/tutwuri.png') }}" alt="Logo Tutwuri" class="h-9 w-auto">


                <span class="text-white font-semibold text-lg">SDN 9 Kota Barat</span>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-2">
        {{-- dahboard --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="[
                desktopSidebarCollapsed ? 'justify-center' : '',
                route().current('dashboard') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'
            ]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4h.01M12 12h.01M15 12h.01M12 9h.01M15 9h.01M9 9h.01">
                </path>
            </svg>
            <span :class="desktopSidebarCollapsed ? 'hidden' : 'block'">Dashboard</span>
        </a>

        {{-- Tugas --}}
        <a href="{{ route('kelas.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="[
                desktopSidebarCollapsed ? 'justify-center' : '',
                route().current('kelas.index') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'
            ]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 7l9-4 9 4M4 10h16M4 10v6m16-6v6M4 16l8 4 8-4" />
            </svg>
            <span :class="desktopSidebarCollapsed ? 'hidden' : 'block'">Kelas & Tugas</span>
        </a>

        {{-- terbitkan tugas --}}
        <a href="{{ route('terbitkantugas.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="[
                desktopSidebarCollapsed ? 'justify-center' : '',
                route().current('terbitkantugas.index') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'
            ]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                </path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span :class="desktopSidebarCollapsed ? 'hidden' : 'block'">Terbitkan tugas</span>
        </a>

        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md font-medium"
            :class="[
                desktopSidebarCollapsed ? 'justify-center' : '',
                route().current('users.index') ? 'text-slate-200 bg-slate-700' :
                'text-slate-400 hover:bg-slate-700 hover:text-slate-200'
            ]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                <path d="M15 9a3 3 0 10-6 0 3 3 0 006 0zM12 11a7 7 0 00-7 7v1h14v-1a7 7 0 00-7-7z" />
            </svg>
            <span :class="desktopSidebarCollapsed ? 'hidden' : 'block'">Users</span>
        </a>
    </nav>

    <div class="p-4 border-t border-slate-700">
        <button @click="desktopSidebarCollapsed = !desktopSidebarCollapsed"
            class="flex items-center justify-center w-full px-3 py-2 rounded-md text-slate-400 hover:bg-slate-700 hover:text-slate-200">
            <svg :class="desktopSidebarCollapsed ? 'hidden' : 'block'" class="w-5 h-5" fill="none"
                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
            <svg :class="desktopSidebarCollapsed ? 'block' : 'hidden'" class="w-5 h-5" fill="none"
                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7">
                </path>
            </svg>

            <span class="sr-only">Toggle Sidebar</span>
        </button>
    </div>
</div>
