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

    <title>Login - Parkir System</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/perfect-scrollbar.css') }}" />

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/css/form-validation.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
  </head>

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
  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
          <!-- Login -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
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
              <!-- /Logo -->
              <h4 class="mb-1">Selamat Datang!</h4>
              <p class="mb-6">Silakan masuk ke akun anda dan mulailah parkir.</p>

              <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-6 form-control-validation">
                  <label for="login" class="form-label">Username / NIP / NUP / NIM</label>
                  <input
                    type="text"
                    class="form-control @error('login') is-invalid @enderror"
                    id="login"
                    name="login"
                    placeholder="Masukkan username, NIP, NUP, or NIM"
                    value="{{ old('login') }}"
                    autofocus />
                    @error('login')
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
                <div class="my-8">
                  <div class="d-flex justify-content-between">
                    <div class="form-check mb-0 ms-2">
                      <input class="form-check-input" type="checkbox" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }} />
                      <label class="form-check-label" for="remember-me"> Ingat akun saya </label>
                    </div>
                    <a href="{{ route('password.request') }}">
                      <p class="mb-0">Lupa Password?</p>
                    </a>
                  </div>
                </div>
                <div class="mb-6">
                  <button class="btn btn-orange d-grid w-100" type="submit">Login</button>
                </div>
              </form>

              <p class="text-center">
                <span>Anda pengguna baru?</span>
                <a href="{{ route('register') }}">
                  <span>Sign up disini!</span>
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
          <!-- /Login -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->

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

    <!-- Vendors JS -->
    <script src="{{ asset('assets/js/popular.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/auto-focus.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
  </body>
</html>
