<!doctype html>

<html
  lang="en"
  class="layout-wide customizer-hide"
  dir="ltr"
  data-skin="default"
  data-template="vertical-menu-template"
  data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Register - Parkir System</title>

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/iconify-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/form-validation.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/page-auth.css') }}" />

    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <style>
      .btn-orange {
        color: #fff;
        background-color: #ff9800;
        border-color: #ff9800;
      }

      .btn-orange:hover,
      .btn-orange:focus,
      .btn-orange:active {
        background-color: #fb8c00;
        border-color: #fb8c00;
        color: #fff;
      }

      .authentication-inner a,
      .authentication-inner .text-primary {
          color: #ff9800 !important;
      }
      .authentication-inner a:hover {
          color: #fb8c00 !important;
      }
      
      .authentication-wrapper.authentication-basic .authentication-inner::before,
      .authentication-wrapper.authentication-basic .authentication-inner::after {
          content: none !important;
          background: none !important;
          mask-image: none !important; 
          display: none !important;
      }

      .form-control:focus,
      select:focus {
        border-color: #ff9800 !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 152, 0, 0.25) !important;
      }

      .form-control:focus,
      select:focus {
        border-color: #ff9800 !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 152, 0, 0.25) !important;
      }

      .input-group:focus-within .form-control { 
      border-color: #ff9800 !important; 
      z-index: 3; 
      }

      .input-group.input-group-merge:focus-within {
          box-shadow: 0 0 0 0.25rem rgba(255, 152, 0, 0.25) !important;
      }

      .input-group.input-group-merge:focus-within .input-group-text {
          border-color: #ff9800 !important; 
          box-shadow: none !important;
      }

      .input-group:focus-within .form-control:focus {
          box-shadow: none !important;
      }
      
      body {
        background-color: #ff9800;
        background-image: url('{{ asset('assets/images/orange-bg-pattern.png') }}') !important;
        background-size: cover !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed;
      }
    </style>
  </head>

  <body>
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
          <div class="card">
            <div class="card-body">
              <div class="app-brand justify-content-center mb-6">
                <a href="{{ url('/') }}" class="app-brand-link">
                  <span class="app-brand-logo demo">
                    <img 
                      src="{{ asset('assets/images/logo.png') }}" 
                      alt="Logo Universitas" 
                      style="height: 70px; width: auto;" 
                    />
                  </span>
                  <span class="app-brand-text demo text-heading fw-bold">Sistem Parkir Universitas PGRI Kanjuruhan Malang</span>
                </a>
              </div>
              <h4 class="mb-1">Buat akun anda disini!</h4>
              <p class="mb-6">Website manajemen parkir agar parkirmu terasa lebih mudah.</p>

              <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-6 form-control-validation">
                  <label for="name" class="form-label">Nama</label>
                  <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    placeholder="Masukkan Nama Lengkap"
                    value="{{ old('name') }}"
                    autofocus />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6 form-control-validation">
                  <label for="username" class="form-label">Username</label>
                  <input
                    type="text"
                    class="form-control @error('username') is-invalid @enderror"
                    id="username"
                    name="username"
                    placeholder="Masukkan Username"
                    value="{{ old('username') }}" />
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6 form-control-validation">
                  <label for="user_type" class="form-label">Jenis Pengguna</label>
                  <select class="form-control @error('user_type') is-invalid @enderror" id="user_type" name="user_type" onchange="toggleIdentityField()">
                    <option value="">Pilih Jenis Pengguna</option>
                    <option value="mahasiswa" {{ old('user_type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('user_type') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                  </select>
                  @error('user_type')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="mb-6 form-control-validation" id="identity_field" style="display: none;">
                  <label for="identity_number" class="form-label" id="identity_label">NIM (for Mahasiswa)</label>
                  <input
                    type="text"
                    class="form-control @error('identity_number') is-invalid @enderror"
                    id="identity_number"
                    name="identity_number"
                    placeholder="Masukkan NIM"
                    value="{{ old('identity_number') }}" />
                    @error('identity_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6 form-password-toggle form-control-validation">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control @error('password') is-invalid @enderror"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password" />
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="mb-6 form-password-toggle form-control-validation">
                  <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password_confirmation"
                      class="form-control"
                      name="password_confirmation"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password_confirmation" />
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                </div>
                <div class="my-8 form-control-validation">
                  <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                    <label class="form-check-label" for="terms-conditions">
                      Saya setuju pada
                      <a href="javascript:void(0);">privacy policy & terms</a>
                    </label>
                  </div>
                </div>
                <button class="btn btn-primary d-grid w-100 btn-orange">Sign up</button>
              </form>

              <p class="text-center">
                <span>Sudah memiliki akun?</span>
                <a href="{{ route('login') }}">
                  <span>Sign in disini!</span>
                </a>
              </p>

              <div class="divider my-6">
                <div class="divider-text">or</div>
              </div>

              <div class="d-flex justify-content-center">
                <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-facebook me-1_5">
                  <i class="icon-base ti tabler-brand-facebook-filled icon-20px"></i>
                </a>

                <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-twitter me-1_5">
                  <i class="icon-base ti tabler-brand-twitter-filled icon-20px"></i>
                </a>

                <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-github me-1_5">
                  <i class="icon-base ti tabler-brand-github-filled icon-20px"></i>
                </a>

                <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-google-plus">
                  <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
                </a>
              </div>
            </div>
          </div>
          </div>
      </div>
    </div>

    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/popper.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/node-waves.js') }}"></script>
    <script src="{{ asset('assets/js/autocomplete-js.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.js') }}"></script>
    <script src="{{ asset('assets/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/js/hammer.js') }}"></script>
    <script src="{{ asset('assets/js/i18n.js') }}"></script>
    <script src="{{ asset('assets/js/menu.js') }}"></script>

    <script src="{{ asset('assets/js/popular.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/auto-focus.js') }}"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>

    <script>
      function toggleIdentityField() {
        const userType = document.getElementById('user_type').value;
        const identityField = document.getElementById('identity_field');
        const identityLabel = document.getElementById('identity_label');
        const identityInput = document.getElementById('identity_number');

        if (userType === 'mahasiswa') {
          identityField.style.display = 'block';
          identityLabel.textContent = 'NIM (for Mahasiswa)';
          identityInput.placeholder = 'Masukkan NIM';
          identityInput.name = 'identity_number';
        } else if (userType === 'dosen') {
          identityField.style.display = 'block';
          identityLabel.textContent = 'NIP/NUP (for Dosen)';
          identityInput.placeholder = 'Masukkan NIP/NUP';
          identityInput.name = 'identity_number';
        } else {
          identityField.style.display = 'none';
          identityInput.name = '';
        }
      }

      // Initialize the function on page load to handle old input
      document.addEventListener('DOMContentLoaded', function() {
        toggleIdentityField();
      });
    </script>
  </body>
</html>