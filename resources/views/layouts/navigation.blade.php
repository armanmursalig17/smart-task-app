{{-- 
  File ini berisi dua sidebar:
  1. Sidebar Mobile (off-canvas, dikontrol oleh mobileMenuOpen)
  2. Sidebar Desktop (collapsible, dikontrol oleh desktopSidebarCollapsed)
--}}

<div 
    x-data="{ mobileMenuOpen: false, desktopSidebarCollapsed: false }"
    class="relative">

    <!-- ===== BACKDROP UNTUK MOBILE SIDEBAR ===== -->
    <div 
        x-show="mobileMenuOpen" 
        @click="mobileMenuOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300" 
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" 
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-20 sm:hidden" 
        aria-hidden="true">
    </div>

    <!-- ===== SIDEBAR MOBILE ===== -->
    <aside 
        :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-100
               transform transition-transform duration-300 ease-in-out 
               z-30 sm:hidden flex flex-col h-full">

        <!-- LOGO -->
        <div class="flex items-center justify-center h-16 border-b">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            </a>
        </div>

        <!-- NAVIGASI MOBILE -->
        <nav class="flex-1 overflow-y-auto py-4 space-y-1">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>
            <!-- Tambahkan link navigasi tambahan di sini -->
        </nav>

        <!-- DROPDOWN USER (BAGIAN BAWAH) -->
        <div class="p-4 border-t border-gray-200">
            <x-dropdown align="top" width="48">
                <x-slot name="trigger">
                    <button
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 transition ease-in-out duration-150">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </aside>

    <!-- ===== SIDEBAR DESKTOP ===== -->
    <aside 
        :class="desktopSidebarCollapsed ? 'w-20' : 'w-64'"
        class="hidden sm:flex sm:flex-col h-full fixed inset-y-0 left-0 z-10 
               bg-white border-r border-gray-100 shadow-lg
               transition-all duration-300 ease-in-out">

      

        <!-- NAVIGASI DESKTOP -->
        <nav class="flex-1 overflow-y-auto py-4 space-y-1">
            <a href="{{ route('dashboard') }}" title="Dashboard"
                class="flex items-center px-4 py-2 mx-2 rounded-md text-sm font-medium transition-colors duration-200
                      {{ request()->routeIs('dashboard') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-800' }}"
                :class="desktopSidebarCollapsed ? 'justify-center' : ''">
                <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span class="ml-3" :class="desktopSidebarCollapsed ? 'hidden' : 'block'">{{ __('Dashboard') }}</span>
            </a>
        </nav>

        <!-- TOGGLE SIDEBAR -->
        <div class="p-2 border-t border-gray-100">
            <button 
                @click="desktopSidebarCollapsed = !desktopSidebarCollapsed" 
                title="Toggle Sidebar"
                class="w-full flex items-center justify-center px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-md transition duration-200">
                <svg :class="desktopSidebarCollapsed ? 'hidden' : 'block'" class="h-6 w-6"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                </svg>
                <svg :class="desktopSidebarCollapsed ? 'block' : 'hidden'" class="h-6 w-6"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <!-- DROPDOWN USER DESKTOP -->
        <div class="p-2 border-t border-gray-200">
            <x-dropdown align="top" width="48">
                <x-slot name="trigger">
                    <button
                        class="w-full flex items-center justify-center text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out"
                        :class="desktopSidebarCollapsed ? 'px-2 py-2' : 'px-3 py-2 border border-gray-200'">
                        <svg :class="desktopSidebarCollapsed ? 'block' : 'hidden'" class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div :class="desktopSidebarCollapsed ? 'hidden' : 'flex'" class="w-full items-center justify-between">
                            <span class="truncate">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"
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
    </aside>
</div>
