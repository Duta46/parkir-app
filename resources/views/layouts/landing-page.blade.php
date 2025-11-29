<!-- resources/views/layouts/landing.blade.php -->
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sistem Parkir')</title>
  <meta name="description" content="@yield('description', 'Sistem Parkir Kampus')">

  <!-- Bootstrap 5 CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Fullscreen carousel */
    .landing-carousel,
    .landing-carousel .carousel-item,
    .landing-carousel .carousel-item img {
      height: 100vh;
      min-height: 480px;
      object-fit: cover;
    }

    .landing-intro {
      padding: 4rem 1rem;
    }

    .landing-footer {
      background: #0b1b2b;
      color: #fff;
      padding: 2rem 1rem;
    }

    .overlay-search {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 2;
      color: #fff;
      text-align: center;
    }

    /* Responsive constraint for intro content */
    .intro-content {
      max-width: 1000px;
      margin: 0 auto;
    }
  </style>

  @stack('head')
</head>
<body>
  @yield('body')

  <!-- Bootstrap JS bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
