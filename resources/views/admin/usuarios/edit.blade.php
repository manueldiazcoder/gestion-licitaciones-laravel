@extends('layouts.base')

@section('title', "Editar Usuario: {$user->name}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-person-gear me-2"></i>Editar Usuario
    </h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle me-2"></i>Corregí los siguientes errores:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border">
    <div class="card-body p-4">
        <form action="{{ route('usuarios.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required maxlength="255">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required maxlength="255">
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Rol <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="operador" @selected(old('role', $user->role) === 'operador')>Operador</option>
                        <option value="usuario" @selected(old('role', $user->role) === 'usuario')>Usuario</option>
                    </select>
                </div>
            </div>

            <hr class="delimitadorSuperior mt-4 mb-3">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i>Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
