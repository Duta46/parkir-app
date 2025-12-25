<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('name', 'asc')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:mahasiswa,dosen,pegawai,petugas,admin',
            'role' => 'required|in:Pengguna,Petugas,Admin',
            'identity_number' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|in:motor,car',
            'vehicle_plate_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Jika role adalah Admin atau Petugas, atur user_type secara otomatis
        $userType = $request->user_type;
        if ($request->role === 'Admin') {
            $userType = 'admin';
        } elseif ($request->role === 'Petugas') {
            $userType = 'petugas';
        }

        // Jika role adalah Admin atau Petugas, identity_number tidak perlu disimpan atau diisi null
        $identityNumber = $request->identity_number;
        if ($request->role === 'Admin' || $request->role === 'Petugas') {
            $identityNumber = null;
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_type' => $userType,
            'identity_number' => $identityNumber,
            'vehicle_type' => $request->vehicle_type ?? null,
            'vehicle_plate_number' => $request->vehicle_plate_number ?? null,
        ]);

        // Assign role based on the request
        if ($request->role === 'Admin') {
            $user->assignRole('Admin');
        } elseif ($request->role === 'Petugas') {
            $user->assignRole('Petugas');
        } else {
            $user->assignRole('Pengguna');
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'user_type' => 'required|in:mahasiswa,dosen,pegawai,petugas,admin',
            'role' => 'required|in:Pengguna,Petugas,Admin',
            'identity_number' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'vehicle_type' => 'nullable|string|in:motor,car',
            'vehicle_plate_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Jika role adalah Admin atau Petugas, atur user_type secara otomatis
        $userType = $request->user_type;
        if ($request->role === 'Admin') {
            $userType = 'admin';
        } elseif ($request->role === 'Petugas') {
            $userType = 'petugas';
        }

        // Jika role adalah Admin atau Petugas, identity_number tidak perlu disimpan atau diisi null
        $identityNumber = $request->identity_number;
        if ($request->role === 'Admin' || $request->role === 'Petugas') {
            $identityNumber = null;
        }

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'user_type' => $userType,
            'identity_number' => $identityNumber,
            'vehicle_type' => $request->vehicle_type ?? null,
            'vehicle_plate_number' => $request->vehicle_plate_number ?? null,
        ]);

        // Sync roles based on the request
        $user->syncRoles([]);
        if ($request->role === 'Admin') {
            $user->assignRole('Admin');
        } elseif ($request->role === 'Petugas') {
            $user->assignRole('Petugas');
        } else {
            $user->assignRole('Pengguna');
        }

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}