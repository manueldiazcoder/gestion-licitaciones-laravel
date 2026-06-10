@extends('layouts.base')

@section('title', "Editar Responsable: {$responsable->nombre_completo}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-pencil me-2"></i>Editar Responsable
    </h4>
    <a href="{{ route('responsables.index') }}" class="btn btn-outline-secondary btn-sm">
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
        <form action="{{ route('responsables.update', $responsable) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre_completo" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_completo" id="nombre_completo"
                           class="form-control @error('nombre_completo') is-invalid @enderror"
                           value="{{ old('nombre_completo', $responsable->nombre_completo) }}" required maxlength="255">
                </div>
                <div class="col-md-6">
                    <label for="numero_telefono" class="form-label">Número de teléfono <span class="text-danger">*</span></label>
                    <input type="text" name="numero_telefono" id="numero_telefono"
                           class="form-control @error('numero_telefono') is-invalid @enderror"
                           value="{{ old('numero_telefono', $responsable->numero_telefono) }}" required maxlength="30">
                </div>
                <div class="col-md-6">
                    <label for="correo_electronico" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="correo_electronico" id="correo_electronico"
                           class="form-control @error('correo_electronico') is-invalid @enderror"
                           value="{{ old('correo_electronico', $responsable->correo_electronico) }}" required maxlength="255">
                </div>
            </div>

            <hr class="delimitadorSuperior mt-4 mb-3">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('responsables.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i>Actualizar Responsable
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
