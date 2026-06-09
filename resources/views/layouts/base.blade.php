<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- CDN PARA PODER USAR BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <!-- CDN PARA PODER USAR LOS ICONOS BOOTSTRAP -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <title>@yield('title', 'Sistema de Gestión de Licitaciones')</title>
</head>

<body class="bg-dark text-white d-flex flex-column min-vh-100">

  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary mb-4">
    <div class="container">
      <a class="navbar-brand fw-bold" href="{{ route('home') }}">
        <i class="bi bi-clipboard-data me-2"></i>Gestión Licitaciones
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav me-auto">
          @auth
            <li class="nav-item">
              <a class="nav-link" href="{{ route('procesos.index') }}">
                <i class="bi bi-search me-1"></i>Consultar
              </a>
            </li>
            @if (Auth::user()->esAdmin())
              <li class="nav-item">
                <a class="nav-link" href="{{ route('procesos.create') }}">
                  <i class="bi bi-plus-circle me-1"></i>Crear
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                  <i class="bi bi-gear me-1"></i>Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-dark">
                  <li><a class="dropdown-item" href="#">Usuarios</a></li>
                  <li><a class="dropdown-item" href="#">Reportes</a></li>
                </ul>
              </li>
            @endif
          @endauth
        </ul>

        <ul class="navbar-nav">
          @auth
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                {!! Auth::user()->getRolBadge() !!}
              </a>
              <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                <li>
                  <span class="dropdown-item-text text-muted small">
                    {{ Auth::user()->email }}
                  </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                      <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
                    </button>
                  </form>
                </li>
              </ul>
            </li>
          @else
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">
                <i class="bi bi-person-plus me-1"></i>Registrarse
              </a>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  {{-- Contenido principal --}}
  <div class="container flex-grow-1">
    @yield('content')
  </div>

  {{-- Footer --}}
  <footer class="bg-dark border-top border-secondary mt-5 py-3 text-center text-muted small">
    <div class="container">
      <i class="bi bi-clipboard-data me-1"></i>
      Sistema de Gestión de Licitaciones &mdash; {{ date('Y') }}
    </div>
  </footer>

  <!-- CDN PARA PODER USAR BOOTSTRAP -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>