<?php

namespace App\Http\Controllers;

use App\Models\User; // Pastikan ini di-import
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Tambahkan import ini
use Illuminate\Support\Facades\Session; // Tambahkan import ini untuk flash message

class UsersController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        // Mengambil semua user
        $users = User::all();

        // Mengambil ID user yang sedang login
        $currentUserId = Auth::id();

        return view('Users.index', compact('users', 'currentUserId'));
    }

    /**
     * Mereset password pengguna ke password default.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(User $user)
    {
        // Reset password ke 'password123' dan hash
        $user->password = Hash::make('password123');
        $user->save();

        // Kirim flash message sukses
        return redirect()->route('users.index')->with('success', "Password untuk pengguna **{$user->name}** berhasil di-reset menjadi **password123**.");
    }

    /**
     * Menghapus pengguna dari database.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Pencegahan: User tidak boleh menghapus akun mereka sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        // Kirim flash message sukses
        return redirect()->route('users.index')->with('success', "Pengguna **{$name}** berhasil dihapus.");
    }
}