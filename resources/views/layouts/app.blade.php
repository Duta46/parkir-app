<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <meta name="description" content="@yield('description', '')" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMD/CDQeB9i5vT2/Qx8F7E41R/t3N4h1F355xL5J8H2XQvU1hQvFqR6uP6aA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/flag-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/iconify-icons.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/cards-advance.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/js/template.customizer.js') }}"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
</head>

<style>
    .layout-menu,
    .menu-vertical {
        background-color: #11224D !important;
    }

    .menu-inner .menu-item .menu-link {
        color: #ffffff !important;
    }

    .menu-inner .menu-item.active>.menu-link,
    .menu-inner .menu-item.open>.menu-link {
        background-color: #fb8c00 !important;
        color: #ffffff !important;
    }

    .menu-vertical .ti,
    .layout-menu .ti {
        background-color: #ffffff !important;
    }

    .tabler-x {}

    .menu-inner .menu-item:not(.active):hover>.menu-link {
        background-color: #f77f00 !important;
        color: #ffffff !important;
    }

    .menu .menu-sub .menu-item .menu-link::before {
        color: #ffffff !important;
    }

    .menu .menu-sub>.menu-item>.menu-link::before {
        background-color: #ffffff !important;
    }

    .menu-toggle::after {
        background-color: #ffffff !important;
    }

    .app-brand-text.demo.menu-text {
        color: #ffffff !important;
    }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu">
                <div class="app-brand demo">
                    <a href="/" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Universitas"
                                style="height: 50px; width: auto;" />
                        </span>
                        <span
                            class="app-brand-text demo menu-text fw-bold ms-3">{{ config('app.name', 'Parkir') }}</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
                        <i class="icon-base ti tabler-x d-block d-xl-none"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    {{-- <li class="menu-item">
              <a href="/" class="menu-link">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
              </a>
            </li> --}}

                    <!-- Add more menu items here -->
                    <li class="menu-item">
                        <a href="/dashboard" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-smart-home"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>

                    @auth
                        @if (auth()->user()->hasRole('Admin'))
                            <!-- Parking Management Menu -->
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <i class="menu-icon icon-base ti tabler-car"></i>
                                    <div data-i18n="Manajemen Parkir">Manajemen Parkir</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="{{ route('parking.management.index') }}" class="menu-link">
                                            <div data-i18n="Dashboard">Dashboard</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('parking.transactions.index') }}" class="menu-link">
                                            <div data-i18n="Transaksi">Transaksi</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('parking.management.all') }}" class="menu-link">
                                            <div data-i18n="Semua Data">Semua Data</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('admin.scan.barcode.page') }}" class="menu-link">
                                            <div data-i18n="Scan Barcode">Pindai Barcode</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @elseif(auth()->user()->hasRole('Petugas'))
                            <!-- Parking Management Menu for Petugas (same as Admin) -->
                            <li class="menu-item">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <i class="menu-icon icon-base ti tabler-car"></i>
                                    <div data-i18n="Manajemen Parkir">Manajemen Parkir</div>
                                </a>
                                <ul class="menu-sub">
                                    <li class="menu-item">
                                        <a href="{{ route('parking.management.index') }}" class="menu-link">
                                            <div data-i18n="Dashboard">Dashboard</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('parking.transactions.index') }}" class="menu-link">
                                            <div data-i18n="Transaksi">Transaksi</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('parking.management.all') }}" class="menu-link">
                                            <div data-i18n="Semua Data">Semua Data</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('admin.scan.barcode.page') }}" class="menu-link">
                                            <div data-i18n="Scan Barcode">Pindai Barcode</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @elseif(auth()->user()->hasRole('Pengguna') || in_array(auth()->user()->user_type, ['Dosen', 'Mahasiswa', 'pegawai']))
                            <!-- Parking Menu for Dosen, Mahasiswa, and Pegawai -->
                            <li class="menu-item">
                                <a href="{{ route('parking.history') }}" class="menu-link">
                                    <i class="menu-icon icon-base fa-solid fa-car"></i>
                                    <div data-i18n="Parkir">Parkir</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('scan.barcode.page') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-scan"></i>
                                    <div data-i18n="Scan Barcode">Pindai Barcode</div>
                                </a>
                            </li>
                        @endif
                    @endauth

                    @if (auth()->user()->hasRole('Admin'))
                        <li class="menu-item">
                            <a href="/users" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-users"></i>
                                <div data-i18n="Pengguna">Pengguna</div>
                            </a>
                        </li>
                    @endif
                        
                    <li class="menu-item">
                        <a href="{{ route('settings') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-settings"></i>
                            <div data-i18n="Pengaturan">Pengaturan</div>
                        </a>
                    </li>

                    <!-- Authentication -->
                    @auth
                        <li class="menu-item">
                            <a href="{{ route('logout') }}" class="menu-link"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="menu-icon icon-base ti tabler-logout"></i>
                                <div data-i18n="Logout">Logout</div>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @else
                        <li class="menu-item">
                            <a href="{{ route('login') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-login"></i>
                                <div data-i18n="Login">Login</div>
                            </a>
                        </li>
                    @endauth
                </ul>
            </aside>

            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);"
                    class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="ti tabler-menu icon-base"></i>
                    <i class="ti tabler-chevron-right icon-base"></i>
                </a>
            </div>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base ti tabler-menu-2 icon-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item navbar-search-wrapper px-md-0 px-2 mb-0">
                                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0"
                                    href="javascript:void(0);">
                                    <span class="d-inline-block text-body-secondary fw-normal"
                                        id="autocomplete"></span>
                                </a>
                            </div>
                        </div>

                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                            <!-- Style Switcher -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                                    id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center active"
                                            data-bs-theme-value="light" aria-pressed="false">
                                            <span><i class="icon-base ti tabler-sun icon-22px me-3"
                                                    data-icon="sun"></i>Light</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center"
                                            data-bs-theme-value="dark" aria-pressed="true">
                                            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                                    data-icon="moon-stars"></i>Dark</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center"
                                            data-bs-theme-value="system" aria-pressed="false">
                                            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                                    data-icon="device-desktop-analytics"></i>System</span>
                                        </button>
                                    </li>
                                </ul>
                            </li>
                            <!-- / Style Switcher-->

                            <!-- Notification -->
                            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                    aria-expanded="false">
                                    <span class="position-relative">
                                        <i class="icon-base ti tabler-bell icon-22px text-heading"></i>
                                        <span
                                            class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-0">
                                    <li class="dropdown-menu-header border-bottom">
                                        <div class="dropdown-header d-flex align-items-center py-3">
                                            <h6 class="mb-0 me-auto">Notification</h6>
                                            <div class="d-flex align-items-center h6 mb-0">
                                                <span class="badge bg-label-primary me-2">3 New</span>
                                                <a href="javascript:void(0)"
                                                    class="dropdown-notifications-all p-2 btn btn-icon"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Mark all as read"><i
                                                        class="icon-base ti tabler-mail-opened text-heading"></i></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-notifications-list scrollable-container">
                                        <ul class="list-group list-group-flush">
                                            <li
                                                class="list-group-item list-group-item-action dropdown-notifications-item">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar">
                                                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                                                class="rounded-circle" />
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="small mb-1">Welcome!</h6>
                                                        <small class="mb-1 d-block text-body">Welcome to
                                                            {{ config('app.name') }}</small>
                                                        <small class="text-body-secondary">Just now</small>
                                                    </div>
                                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                                        <a href="javascript:void(0)"
                                                            class="dropdown-notifications-read"><span
                                                                class="badge badge-dot"></span></a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <!--/ Notification -->

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        @auth
                                            <img src="{{ auth()->user()->profile_photo_path ? asset(auth()->user()->profile_photo_path) : (auth()->user()->profile_photo_url ?? asset('assets/img/avatars/1.png')) }}"
                                                alt class="rounded-circle" />
                                        @else
                                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                                class="rounded-circle" />
                                        @endauth
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item mt-0" href="javascript:void(0);">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar avatar-online">
                                                        @auth
                                                            <img src="{{ auth()->user()->profile_photo_path ? asset(auth()->user()->profile_photo_path) : (auth()->user()->profile_photo_url ?? asset('assets/img/avatars/1.png')) }}"
                                                                alt class="rounded-circle" />
                                                        @else
                                                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                                                class="rounded-circle" />
                                                        @endauth
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">@auth {{ Auth::user()->name }}
                                                        @else
                                                        Guest @endauth
                                                    </h6>
                                                    <small class="text-body-secondary">@auth {{ Auth::user()->email }}
                                                        @else
                                                        User @endauth
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1 mx-n2"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('settings') }}">
                                            <i class="icon-base ti tabler-settings me-3 icon-md"></i><span
                                                class="align-middle">Pengaturan</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1 mx-n2"></div>
                                    </li>
                                    @auth
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="icon-base ti tabler-logout me-3 icon-md"></i><span
                                                    class="align-middle">Logout</span>
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item" href="{{ route('login') }}">
                                                <i class="icon-base ti tabler-login me-3 icon-md"></i><span
                                                    class="align-middle">Login</span>
                                            </a>
                                        </li>
                                    @endauth
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="text-body">
                                    © {{ date('Y') }}, Sistem Parkir QR Code - {{ config('app.name', 'Laravel') }}
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    Aplikasi Manajemen Parkir Berbasis QR Code
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

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
    <script src="{{ asset('assets/js/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/swiper.js') }}"></script>
    <script src="{{ asset('assets/js/datatables-bootstrap5.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    @yield('scripts')

    @stack('scripts')
</body>

</html>
