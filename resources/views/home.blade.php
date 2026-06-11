@extends('layouts.base')

@section('title', 'Dashboard')

@section('content')

@auth
  {{-- Bienvenida --}}
  <div class="mb-4 text-center">
    <h2 class="fw-bold">
      <i class="bi bi-person-circle me-2"></i>Bienvenido, {{ Auth::user()->name }}
    </h2>
    <p class="lead text-muted">
      Rol: {!! Auth::user()->getRolBadge() !!}
    </p>
  </div>

  {{-- Accesos directos --}}
  <hr class="delimitadorSuperior">

  <div class="dashboard-grid">
    <a href="{{ route('procesos.index') }}" class="dashboard-card">
      <div class="card-icon"><i class="bi bi-search"></i></div>
      <h3>Consultar</h3>
      <p>Buscá y revisá procesos existentes</p>
    </a>

    @if (Auth::user()->esAdmin())
      <a href="{{ route('procesos.create') }}" class="dashboard-card">
        <div class="card-icon"><i class="bi bi-plus-circle"></i></div>
        <h3>Crear</h3>
        <p>Registrá un nuevo proceso de licitación</p>
      </a>

      <a href="{{ route('responsables.index') }}" class="dashboard-card">
        <div class="card-icon"><i class="bi bi-people"></i></div>
        <h3>Responsables</h3>
        <p>Administrá los responsables de los procesos</p>
      </a>

      <a href="{{ route('usuarios.index') }}" class="dashboard-card">
        <div class="card-icon"><i class="bi bi-person-gear"></i></div>
        <h3>Usuarios</h3>
        <p>Gestión de usuarios del sistema</p>
      </a>
    @endif

    <a href="{{ route('reportes.index') }}" class="dashboard-card">
      <div class="card-icon"><i class="bi bi-bar-chart-fill"></i></div>
      <h3>Reportes</h3>
      <p>Estadísticas y reportes del sistema</p>
    </a>
  </div>

@else
  {{-- Landing público --}}
  <div class="text-center" style="padding-top: 4rem;">
    <h1 class="fw-bold display-4 mb-3" style="color: var(--color-primary);">
      <i class="bi bi-clipboard-data me-2"></i>Gestión de Licitaciones
    </h1>
    <p class="lead text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
      Sistema para la administración y seguimiento de procesos de contratación pública.
    </p>
    <div class="d-flex justify-content-center gap-3">
      <a href="{{ route('login') }}" class="btn btn-lg px-4" style="background: var(--color-primary); color: #fff;">
        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
      </a>
      <a href="{{ route('register') }}" class="btn btn-lg btn-outline-secondary px-4">
        <i class="bi bi-person-plus me-1"></i>Registrarse
      </a>
    </div>
  </div>
@endauth

@endsection
