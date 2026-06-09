@extends('layouts.base')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </h4>
            </div>
            <div class="card-body p-4">

                {{-- Mensaje de éxito post-registro --}}
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Errores del formulario --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>Correo electrónico
                        </label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="admin@example.com">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-1"></i>Contraseña
                        </label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               id="remember"
                               name="remember">
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                {{-- OAuth --}}
                <div class="text-center mb-3">
                    <p class="text-muted small mb-2">O iniciá sesión con</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('oauth.redirect', 'google') }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-google me-1"></i>Google
                        </a>
                        <a href="{{ route('oauth.redirect', 'github') }}" class="btn btn-outline-dark btn-sm">
                            <i class="bi bi-github me-1"></i>GitHub
                        </a>
                    </div>
                </div>

                <hr>

                <div class="text-center">
                    <p class="mb-1">
                        ¿No tenés cuenta?
                        <a href="{{ route('register') }}">Registrate acá</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
