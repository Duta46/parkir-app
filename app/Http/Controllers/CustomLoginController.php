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

        // Validasi bahwa user login dengan cara yang sesuai dengan tipe mereka
        if (!$this->isValidLoginMethod($user, $loginField)) {
            throw ValidationException::withMessages([
                'login' => ['Silakan login menggunakan ' . $this->getRequiredLoginField($user) . '.'],
            ]);
        }

        // Kita tidak menggunakan verifikasi email lagi, jadi abaikan pengecekan ini

        // Login user
        Auth::login($user, $request->filled('remember'));

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Validasi apakah user login dengan cara yang sesuai
     */
    private function isValidLoginMethod($user, $loginField)
    {
        // Untuk tipe mahasiswa, dosen, dan pegawai, hanya boleh login dengan identity_number atau nim_nip_nup
        if (in_array($user->user_type, ['mahasiswa', 'dosen', 'pegawai'])) {
            // Harus login dengan identity_number atau nim_nip_nup
            return ($user->identity_number === $loginField || $user->nim_nip_nup === $loginField);
        }

        // Untuk tipe admin, bisa login dengan username
        if ($user->user_type === 'admin') {
            return $user->username === $loginField;
        }

        // Default: izinkan jika ditemukan
        return true;
    }

    /**
     * Dapatkan label field login yang seharusnya digunakan
     */
    private function getRequiredLoginField($user)
    {
        switch ($user->user_type) {
            case 'mahasiswa':
                return 'NIM (Nomor Induk Mahasiswa)';
            case 'dosen':
                return 'NIP/NUP (Nomor Induk Pegawai/Pegawai Universitas)';
            case 'pegawai':
                return 'NIP/NUP (Nomor Induk Pegawai/Pegawai Universitas)';
            case 'admin':
                return 'username';
            default:
                return 'identifier yang benar';
        }
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

        // Cari berdasarkan username (akan divalidasi nanti apakah diperbolehkan untuk tipe user ini)
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

        return redirect()->route('login');
    }
}
