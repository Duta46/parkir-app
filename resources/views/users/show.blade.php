@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Pengguna</h5>
                    <div>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali ke Pengguna</a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit Pengguna</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID</strong></td>
                                    <td>: {{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama</strong></td>
                                    <td>: {{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Pengguna</strong></td>
                                    <td>: {{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: {{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe Pengguna</strong></td>
                                    <td>:
                                        <span class="badge bg-label-{{ $user->user_type == 'admin' ? 'primary' : ($user->user_type == 'pegawai' ? 'info' : ($user->user_type == 'dosen' ? 'warning' : 'secondary')) }} me-1">
                                            {{ ucfirst($user->user_type) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Peran</strong></td>
                                    <td>:
                                        <span class="badge bg-label-{{ $user->role == 'Admin' ? 'danger' : 'success' }} me-1">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Identitas</strong></td>
                                    <td>: {{ $user->identity_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Tanggal</strong></td>
                                    <td>: {{ $user->created_at->format('d F Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diperbarui Tanggal</strong></td>
                                    <td>: {{ $user->updated_at->format('d F Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Informasi Tambahan</h6>
                        <div class="row">
                            <div class="col-md-12">
                                @if($user->email_verified_at)
                                    <p><strong>Email Terverifikasi:</strong> <span class="badge bg-success">Ya</span> pada {{ $user->email_verified_at->format('d F Y H:i') }}</p>
                                @else
                                    <p><strong>Email Terverifikasi:</strong> <span class="badge bg-warning">Tidak</span></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection