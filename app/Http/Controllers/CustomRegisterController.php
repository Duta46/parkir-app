<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CustomRegisterController extends Controller
{
    /**
     * Tampilkan form registrasi
     */
    public function showRegistrationForm()
    {
        return view('auth.register-custom');
    }

    /**
     * Proses registrasi pengguna
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'user_type' => ['required', 'in:mahasiswa,dosen'],
            'identity_number' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($request) {
                if ($request->user_type === 'mahasiswa' && strlen($value) < 6) {
                    $fail('NIM must be at least 6 characters.');
                } elseif ($request->user_type === 'dosen' && strlen($value) < 6) {
                    $fail('NIP/NUP must be at least 6 characters.');
                }
            }, 'unique:users,identity_number'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'identity_number' => $request->identity_number,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'role' => 'User', // Default role for regular users
        ]);

        // Login user setelah registrasi
        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }
}