@extends('layouts.base')

@section('title', 'Inicio')

@section('content')

<main>
  <div class="row justify-content-center">
    <div class="col-md-8 text-center">

      @auth
        <div class="mb-4">
          <h2 class="fw-bold">
            <i class="bi bi-person-circle me-2"></i>Bienvenido, {{ Auth::user()->name }}
          </h2>
          <p class="lead text-muted">
            Rol: {!! Auth::user()->getRolBadge() !!}
          </p>
        </div>

        <hr class="border-secondary mb-4">

        <div class="row g-3 justify-content-center">
          <div class="col-md-4">
            <a href="{{ route('procesos.index') }}" class="text-decoration-none">
              <div class="card bg-dark border-primary h-100 shadow-sm">
                <div class="card-body text-center py-4">
                  <i class="bi bi-search display-4 text-primary mb-3 d-block"></i>
                  <h5 class="card-title text-white">Consultar</h5>
                  <p class="card-text text-muted small">Buscá y revisá procesos existentes</p>
                </div>
              </div>
            </a>
          </div>

          @if (Auth::user()->esAdmin())
            <div class="col-md-4">
              <a href="{{ route('procesos.create') }}" class="text-decoration-none">
                <div class="card bg-dark border-success h-100 shadow-sm">
                  <div class="card-body text-center py-4">
                    <i class="bi bi-plus-circle display-4 text-success mb-3 d-block"></i>
                    <h5 class="card-title text-white">Crear</h5>
                    <p class="card-text text-muted small">Creá un nuevo proceso de licitación</p>
                  </div>
                </div>
              </a>
            </div>
          @endif
        </div>
      @else
        <div class="mt-5">
          <h1 class="fw-bold display-4 mb-3">
            <i class="bi bi-clipboard-data me-2"></i>Gestión de Licitaciones
          </h1>
          <p class="lead text-muted mb-4">
            Sistema para la administración y seguimiento de procesos de contratación pública.
          </p>
          <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
              <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
              <i class="bi bi-person-plus me-1"></i>Registrarse
            </a>
          </div>
        </div>
      @endauth

    </div>
  </div>
</main>
@endsection