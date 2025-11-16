<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl leading-tight">
{{ __('Users') }}
</h2>
</x-slot>

<div class="py-0">
    <div class="max-w-full mx-auto ">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">

            <div class="p-6  bg-white border-b border-gray-200">
                <div class="text-2xl font-bold mb-4">
                    Daftar Users 
                </div>

              
                @if (session('success'))
                    <div id="success-message" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                        <p class="font-bold">Berhasil!</p>
                        <p>{!! session('success') !!}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div id="error-message" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
                        <p class="font-bold">Gagal!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                           
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                           
                                            @if ($user->id === $currentUserId)
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    (saya)
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                       
                                        <button type="button" onclick="openResetModal({{ $user->id }}, '{{ $user->name }}')"
                                            class="text-indigo-600 hover:text-indigo-900 mr-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                            Reset Password
                                        </button>

                                       
                                        @if ($user->id !== $currentUserId)
                                            <button type="button" onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                                class="text-red-600 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                                Delete
                                            </button>
                                        @else
                                            <span class="text-red-400 cursor-not-allowed" title="Anda tidak bisa menghapus diri sendiri">
                                                Delete (Anda)
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI (Reset Password) --}}
<div id="reset-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
        <form id="reset-form" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Heroicon: Exclamation -->
                        <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-reset-title">
                            Konfirmasi Reset Password
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Anda yakin ingin mereset password untuk pengguna **<span id="reset-user-name" class="font-semibold text-gray-800"></span>**?
                            </p>
                            <p class="text-sm text-red-600 font-semibold mt-2">
                                Password akan diubah menjadi: <code class="bg-red-50 p-1 rounded">password123</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Reset
                </button>
                <button type="button" onclick="closeResetModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI (Delete User) --}}
<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
        <form id="delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Heroicon: Trash -->
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-delete-title">
                            Konfirmasi Hapus Pengguna
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Anda yakin ingin menghapus pengguna **<span id="delete-user-name" class="font-semibold text-gray-800"></span>**? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Hapus
                </button>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    // --- Functions for Reset Modal ---
    function openResetModal(userId, userName) {
        const modal = document.getElementById('reset-modal');
        const form = document.getElementById('reset-form');
        const userNameSpan = document.getElementById('reset-user-name');

        // Set Form Action (Assuming route is 'users.reset-password')
        // Replace 'users.reset-password' with the actual route name if different
        // If using standard Laravel URL structure, you might use: `/users/${userId}/reset-password`
        form.action = `/users/${userId}/reset-password`; // <-- PASTIKAN ROUTE INI SAMA DENGAN DI web.php

        userNameSpan.textContent = userName;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeResetModal() {
        const modal = document.getElementById('reset-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- Functions for Delete Modal ---
    function openDeleteModal(userId, userName) {
        const modal = document.getElementById('delete-modal');
        const form = document.getElementById('delete-form');
        const userNameSpan = document.getElementById('delete-user-name');

        // Set Form Action (Assuming route is 'users.destroy')
        // Replace 'users.destroy' with the actual route name if different
        // If using standard Laravel URL structure, you might use: `/users/${userId}`
        form.action = `/users/${userId}`; // <-- PASTIKAN ROUTE INI SAMA DENGAN DI web.php

        userNameSpan.textContent = userName;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- Auto-hide Success/Error Message after 5 seconds ---
    document.addEventListener('DOMContentLoaded', () => {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        if (successMessage) {
            setTimeout(() => {
                successMessage.style.transition = 'opacity 1s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 1000);
            }, 5000);
        }

        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.transition = 'opacity 1s';
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 1000);
            }, 5000);
        }
    });
</script>


</x-app-layout>