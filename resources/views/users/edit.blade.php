@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Pengguna</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali ke Pengguna</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label" id="username_label">Nama Pengguna</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Peran</label>
                                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">Pilih Peran</option>
                                        <option value="Pengguna" {{ $user->hasRole('Pengguna') ? 'selected' : '' }}>Pengguna</option>
                                        <option value="Petugas" {{ $user->hasRole('Petugas') ? 'selected' : '' }}>Petugas</option>
                                        <option value="Admin" {{ $user->hasRole('Admin') ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="identity_number_field">
                                <div class="mb-3">
                                    <label for="identity_number" class="form-label">Nomor Identitas (NIM/NIP/NUP)</label>
                                    <input type="text" class="form-control @error('identity_number') is-invalid @enderror" id="identity_number" name="identity_number" value="{{ old('identity_number', $user->identity_number) }}">
                                    @error('identity_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vehicle_type" class="form-label">Jenis Kendaraan</label>
                                    <select class="form-control @error('vehicle_type') is-invalid @enderror" id="vehicle_type" name="vehicle_type">
                                        <option value="motor" {{ old('vehicle_type', $user->vehicle_type) == 'motor' ? 'selected' : '' }}>Motor</option>
                                    </select>
                                    @error('vehicle_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vehicle_plate_number" class="form-label">Nomor Plat Kendaraan</label>
                                    <input type="text" class="form-control @error('vehicle_plate_number') is-invalid @enderror" id="vehicle_plate_number" name="vehicle_plate_number" value="{{ old('vehicle_plate_number', $user->vehicle_plate_number) }}">
                                    @error('vehicle_plate_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Kata Sandi (kosongkan untuk tetap menggunakan yang lama)</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                            <div class="col-md-6" id="user_type_field">
                                <div class="mb-3">
                                    <label for="user_type" class="form-label">Tipe Pengguna</label>
                                    <select class="form-control @error('user_type') is-invalid @enderror" id="user_type" name="user_type" required>
                                        <option value="">Pilih Tipe Pengguna</option>
                                        <option value="mahasiswa" {{ old('user_type', $user->user_type) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                        <option value="dosen" {{ old('user_type', $user->user_type) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                        <option value="pegawai" {{ old('user_type', $user->user_type) == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                        <option value="petugas" {{ old('user_type', $user->user_type) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                                        <option value="admin" {{ old('user_type', $user->user_type) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('user_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Perbarui Pengguna</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const userTypeField = document.getElementById('user_type_field');
    const userTypeSelect = document.getElementById('user_type');
    const identityNumberField = document.getElementById('identity_number_field');
    const usernameLabel = document.getElementById('username_label');

    function toggleFields() {
        if (roleSelect && userTypeSelect) {
            const selectedRole = roleSelect.value;

            // Jika role adalah Admin atau Petugas, sembunyikan user_type dan identity_number, serta atur nilai default
            if (selectedRole === 'Admin' || selectedRole === 'Petugas') {
                userTypeField.style.display = 'none';
                identityNumberField.style.display = 'none';

                // Atur nilai default berdasarkan role
                if (selectedRole === 'Admin') {
                    userTypeSelect.value = 'admin';
                } else if (selectedRole === 'Petugas') {
                    userTypeSelect.value = 'petugas'; // Tipe pengguna untuk petugas parkir
                }

                // Ubah label nama pengguna menjadi Username
                usernameLabel.textContent = 'Username';
            } else {
                userTypeField.style.display = 'block';
                identityNumberField.style.display = 'block';

                // Kembalikan label ke Nama Pengguna
                usernameLabel.textContent = 'Nama Pengguna';
            }
        }
    }

    // Tambahkan event listener untuk perubahan role
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleFields);

        // Panggil fungsi saat halaman dimuat untuk menangani nilai default
        toggleFields();
    }
});
</script>
@endpush

@endsection