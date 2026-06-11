<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Bootstrap CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  {{-- Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <title>@yield('title', 'Sistema de Gestión de Licitaciones')</title>
</head>

<body>

  {{-- Navbar estilo original --}}
  <nav class="navbar-app">
    <a class="navbar-app-brand" href="{{ route('home') }}">
      <i class="bi bi-clipboard-data"></i> Gesti&oacute;n Licitaciones
    </a>

    @auth
      <ul class="navbar-app-nav">
        <li>
          <a href="{{ route('licitaciones.index') }}" class="@if(Request::routeIs('licitaciones.*')) active @endif">
            <i class="bi bi-search"></i> Consultar
          </a>
        </li>
        @if (Auth::user()->esAdmin())
          <li>
            <a href="{{ route('licitaciones.create') }}" class="@if(Request::routeIs('licitaciones.create') || Request::routeIs('licitaciones.store')) active @endif">
              <i class="bi bi-plus-circle"></i> Crear
            </a>
          </li>
        @endif
        <li>
          <a href="{{ route('reportes.index') }}" class="@if(Request::routeIs('reportes.*')) active @endif">
            <i class="bi bi-bar-chart-fill"></i> Reportes
          </a>
        </li>
        @if (Auth::user()->esAdmin())
          <li>
            <a href="{{ route('responsables.index') }}" class="@if(Request::routeIs('responsables.*')) active @endif">
              <i class="bi bi-people"></i> Responsables
            </a>
          </li>
          <li>
            <a href="{{ route('usuarios.index') }}" class="@if(Request::routeIs('usuarios.*')) active @endif">
              <i class="bi bi-person-gear"></i> Usuarios
            </a>
          </li>
        @endif
      </ul>

      <div class="navbar-user">
        <i class="bi bi-person-circle"></i>
        {{ Auth::user()->name }}
        {!! Auth::user()->getRolBadge() !!}
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-light" title="Cerrar sesión">
            <i class="bi bi-box-arrow-right"></i>
          </button>
        </form>
      </div>
    @else
      <div class="navbar-user">
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">
          <i class="bi bi-box-arrow-in-right"></i> Ingresar
        </a>
        <a href="{{ route('register') }}" class="btn btn-sm btn-light">
          <i class="bi bi-person-plus"></i> Registrarse
        </a>
      </div>
    @endauth
  </nav>

  {{-- Flash alerts --}}
  <div class="alert-flash">
    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
  </div>

  {{-- Contenido principal --}}
  <div class="app-content">
    @yield('content')
  </div>

  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
          crossorigin="anonymous"></script>

  {{-- Auto-dismiss flash --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (el) {
          var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
          bsAlert.close();
        });
      }, 5000);
    });
  </script>
</body>

</html>
