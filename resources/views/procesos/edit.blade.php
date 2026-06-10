@extends('layouts.base')

@section('title', "Editar Proceso #{$proceso->codigo_proceso}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-pencil me-2"></i>Editar Proceso #{{ $proceso->codigo_proceso }}
    </h4>
    <a href="{{ route('procesos.show', $proceso) }}" class="btn btn-outline-secondary btn-sm">
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
        <form action="{{ route('procesos.update', $proceso) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Información básica --}}
            <h5 class="mb-3 fw-bold" style="color: var(--color-primary);">
                <i class="bi bi-info-circle me-2"></i>Información Básica
            </h5>
            <div class="contenedorInformacion mb-4">
                <div>
                    <label for="objeto" class="form-label form-label-sm">Objeto <span class="text-danger">*</span></label>
                    <input type="text" name="objeto" id="objeto"
                           class="form-control @error('objeto') is-invalid @enderror"
                           value="{{ old('objeto', $proceso->objeto) }}" required maxlength="500">
                </div>
                <div>
                    <label for="actividad" class="form-label form-label-sm">Actividad</label>
                    <input type="text" name="actividad" id="actividad"
                           class="form-control @error('actividad') is-invalid @enderror"
                           value="{{ old('actividad', $proceso->actividad) }}" maxlength="255">
                </div>
                <div class="full-width">
                    <label for="descripcion" class="form-label form-label-sm">Descripción / Alcance <span class="text-danger">*</span></label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                              class="form-control @error('descripcion') is-invalid @enderror"
                              required maxlength="2000">{{ old('descripcion', $proceso->descripcion) }}</textarea>
                </div>
            </div>

            <hr class="delimitadorSuperior">

            {{-- Presupuesto --}}
            <h5 class="mb-3 mt-3 fw-bold" style="color: var(--color-primary);">
                <i class="bi bi-cash me-2"></i>Presupuesto
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="presupuesto" class="form-label form-label-sm">Monto <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="presupuesto" id="presupuesto"
                               class="form-control @error('presupuesto') is-invalid @enderror"
                               value="{{ old('presupuesto', $proceso->presupuesto) }}" required min="0">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="moneda" class="form-label form-label-sm">Moneda <span class="text-danger">*</span></label>
                    <select name="moneda" id="moneda"
                            class="form-select @error('moneda') is-invalid @enderror" required>
                        <option value="COP" @selected(old('moneda', $proceso->moneda) === 'COP')>COP (Peso colombiano)</option>
                        <option value="USD" @selected(old('moneda', $proceso->moneda) === 'USD')>USD (Dólar)</option>
                        <option value="EUR" @selected(old('moneda', $proceso->moneda) === 'EUR')>EUR (Euro)</option>
                    </select>
                </div>
            </div>

            <hr class="delimitadorSuperior">

            {{-- Cronograma --}}
            <h5 class="mb-3 mt-3 fw-bold" style="color: var(--color-primary);">
                <i class="bi bi-calendar-event me-2"></i>Cronograma
            </h5>
            <div class="contenedorCronograma mb-4">
                <div>
                    <label for="fecha_inicio" class="form-label form-label-sm">Fecha inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                           class="form-control @error('fecha_inicio') is-invalid @enderror"
                           value="{{ old('fecha_inicio', $proceso->fecha_inicio->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label for="hora_inicio" class="form-label form-label-sm">Hora inicio <span class="text-danger">*</span></label>
                    <input type="time" name="hora_inicio" id="hora_inicio"
                           class="form-control @error('hora_inicio') is-invalid @enderror"
                           value="{{ old('hora_inicio', $proceso->hora_inicio) }}" required>
                </div>
                <div>
                    <label for="fecha_cierre" class="form-label form-label-sm">Fecha cierre <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_cierre" id="fecha_cierre"
                           class="form-control @error('fecha_cierre') is-invalid @enderror"
                           value="{{ old('fecha_cierre', $proceso->fecha_cierre->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label for="hora_cierre" class="form-label form-label-sm">Hora cierre <span class="text-danger">*</span></label>
                    <input type="time" name="hora_cierre" id="hora_cierre"
                           class="form-control @error('hora_cierre') is-invalid @enderror"
                           value="{{ old('hora_cierre', $proceso->hora_cierre) }}" required>
                </div>
            </div>

            <hr class="delimitadorSuperior">

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('procesos.show', $proceso) }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i>Actualizar Proceso
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
