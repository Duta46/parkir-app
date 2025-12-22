@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pengaturan Akun</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                            <img
                                src="{{ auth()->user()->profile_photo_path ? asset(auth()->user()->profile_photo_path) : (auth()->user()->profile_photo_url ?? asset('assets/img/avatars/1.png')) }}"
                                alt="user-avatar"
                                class="d-block rounded"
                                id="uploadedAvatar"
                                style="width: 100px; height: 100px; object-fit: cover;"
                            />
                            <div class="button-wrapper">
                                <label for="profile_photo" class="btn btn-primary me-3 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload Foto</span>
                                    <i class="icon-base ti tabler-upload d-block d-sm-none"></i>
                                    <input type="file" id="profile_photo" name="profile_photo" class="account-file-input" hidden
                                        accept="image/png, image/jpeg, image/jpg" />
                                </label>
                                @if(auth()->user()->profile_photo_path)
                                <button type="button" class="btn btn-label-secondary account-image-reset mb-4"
                                    onclick="confirmAndSubmitForm('deletePhotoForm')">
                                    <i class="icon-base ti tabler-trash d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Hapus Foto</span>
                                </button>
                                @endif

                                <div class="text-muted small">JPG, JPEG, atau PNG. Maksimal 2MB</div>
                            </div>
                        </div>

                        <div class="row gy-4 gx-6 mb-6">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required />
                                @if ($errors->get('name'))
                                    <div class="text-danger mt-1">
                                        @foreach($errors->get('name') as $error)
                                            <small>{{ $error }}</small>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="identity_number" class="form-label">Nomor Identitas</label>
                                <input class="form-control" type="text" id="identity_number" name="identity_number" value="{{ old('identity_number', auth()->user()->identity_number) }}" readonly disabled />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="user_type" class="form-label">Tipe Pengguna</label>
                                <input class="form-control" type="text" id="user_type" name="user_type" value="{{ old('user_type', auth()->user()->user_type) }}" readonly disabled />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="vehicle_type" class="form-label">Jenis Kendaraan</label>
                                <select class="form-select" id="vehicle_type" name="vehicle_type">
                                    <option value="Motor" selected>Motor</option>
                                </select>
                                @if ($errors->get('vehicle_type'))
                                    <div class="text-danger mt-1">
                                        @foreach($errors->get('vehicle_type') as $error)
                                            <small>{{ $error }}</small>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="vehicle_plate_number" class="form-label">Nomor Plat Kendaraan</label>
                                <input class="form-control" type="text" id="vehicle_plate_number" name="vehicle_plate_number" value="{{ old('vehicle_plate_number', auth()->user()->vehicle_plate_number) }}" placeholder="Contoh: B 1234 CD" />
                                @if ($errors->get('vehicle_plate_number'))
                                    <div class="text-danger mt-1">
                                        @foreach($errors->get('vehicle_plate_number') as $error)
                                            <small>{{ $error }}</small>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>

                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success mt-3 mb-0">
                                <strong>Berhasil!</strong> Profil Anda telah diperbarui.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle file input change to preview image
            const profilePhotoInput = document.getElementById('profile_photo');
            const uploadedAvatar = document.getElementById('uploadedAvatar');

            if (profilePhotoInput && uploadedAvatar) {
                profilePhotoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            uploadedAvatar.src = event.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // If there's a success alert, update the top navbar avatar after page reload
            if (window.performance) {
                const perfData = window.performance.getEntriesByType("navigation")[0];
                // Check if the page was loaded after a form submission (type 2 is reload including form submission)
                if (perfData.type === 'reload' || perfData.type === 'navigate') {
                    // After a short delay to ensure page is fully loaded, update avatars
                    setTimeout(function() {
                        // Update the top navbar avatar
                        const topNavAvatars = document.querySelectorAll('.navbar-dropdown.dropdown-user img');
                        if (topNavAvatars.length > 0) {
                            topNavAvatars.forEach(function(avatar) {
                                const currentSrc = avatar.src;
                                // Add timestamp to force browser to reload the image and bypass cache
                                const timestamp = new Date().getTime();
                                // Remove any existing timestamp parameter first
                                const cleanSrc = currentSrc.split('?')[0];
                                avatar.src = cleanSrc + '?t=' + timestamp;
                            });
                        }
                    }, 1000);
                }
            }
        });

        function confirmAndSubmitForm(formId) {
            if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>

    <!-- Hidden form for deleting photo -->
    <form id="deletePhotoForm" method="POST" action="{{ route('settings.photo.destroy') }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection