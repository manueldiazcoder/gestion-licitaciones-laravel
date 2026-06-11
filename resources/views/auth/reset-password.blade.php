@extends('layouts.base')

@section('title', 'Restablecer contraseña')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-key fs-1 text-primary"></i>
                    <h4 class="mt-2">Restablecer contraseña</h4>
                    <p class="text-muted small">
                        Ingresá tu nueva contraseña.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $email) }}"
                               required
                               readonly
                               placeholder="tu@correo.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva contraseña</label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required
                               minlength="8"
                               autofocus
                               placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Confirmar contraseña</label>
                        <input type="password"
                               name="password_confirmation"
                               id="password-confirm"
                               class="form-control"
                               required
                               minlength="8"
                               placeholder="Repetí la contraseña">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle me-1"></i>Restablecer
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
