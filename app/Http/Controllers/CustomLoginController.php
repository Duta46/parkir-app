<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomLoginController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLoginForm()
    {
        return view('auth.login-custom');
    }

    /**
     * Proses login dengan logika berbeda untuk setiap tipe pengguna
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'login' => 'required|string', // Bisa username, NIP, NUP, atau NIM
            'password' => 'required|string',
        ]);

        $loginField = $request->login;
        $password = $request->password;

        // Temukan pengguna berdasarkan berbagai kemungkinan
        $user = $this->findUserByLoginField($loginField);

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Kredensial yang diberikan tidak valid.'],
            ]);
        }

        // Kita tidak menggunakan verifikasi email lagi, jadi abaikan pengecekan ini

        // Login user
        Auth::login($user, $request->filled('remember'));

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Temukan pengguna berdasarkan berbagai kemungkinan login field
     */
    private function findUserByLoginField($loginField)
    {
        // Cari berdasarkan identity_number (NIP/NUP/NIM)
        $user = User::where('identity_number', $loginField)->first();
        if ($user) {
            return $user;
        }

        // Cari berdasarkan NIM/NIP/NUP lama
        $user = User::where('nim_nip_nup', $loginField)->first();
        if ($user) {
            return $user;
        }

        // Cari berdasarkan username (untuk admin/petugas)
        $user = User::where('username', $loginField)->first();
        if ($user) {
            return $user;
        }

        return null;
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
