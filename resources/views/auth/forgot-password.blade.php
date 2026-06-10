@extends('layouts.base')

@section('title', 'Recuperar contraseña')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-lock fs-1 text-primary"></i>
                    <h4 class="mt-2">Recuperar contraseña</h4>
                    <p class="text-muted small">
                        Ingresá tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                    </p>
                </div>

                {{-- Mensaje de éxito --}}
                @if (session('status'))
                    <div class="alert alert-success small" role="alert">
                        <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               placeholder="tu@correo.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send me-1"></i>Enviar enlace
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
