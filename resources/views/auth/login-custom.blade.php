@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic">
        <div class="authentication-inner py-4">
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <a href="/" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <span class="text-primary">
                                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                                            fill="currentColor" />
                                        <path
                                            opacity="0.06"
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                                            fill="#161616" />
                                        <path
                                            opacity="0.06"
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                                            fill="#161616" />
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                            </span>
                            <span class="app-brand-text demo text-body fw-bold">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                    </div>
                    
                    <!-- /Logo -->
                    <h4 class="mb-1 pt-2">Selamat Datang! 👋</h4>
                    <p class="mb-4">Silakan masuk ke akun Anda</p>

                    <form id="formAuthentication" class="mb-3" action="{{ route('login.custom') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="login" class="form-label">Username, NIP, NUP, atau NIM</label>
                            <input type="text" class="form-control" id="login" name="login" 
                                   value="{{ old('login') }}" placeholder="Masukkan username, NIP, NUP, atau NIM" 
                                   autofocus required>
                            @error('login')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" 
                                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                       required>
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                <label class="form-check-label" for="remember-me"> Ingat saya </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>Informasi Login:</span>
                        <br>
                        <small class="text-muted">
                            • Admin/Petugas: Gunakan <strong>username</strong> untuk login<br>
                            • Dosen: Gunakan <strong>NIP</strong> atau <strong>NUP</strong> untuk login<br>
                            • Mahasiswa: Gunakan <strong>NIM</strong> untuk login
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection