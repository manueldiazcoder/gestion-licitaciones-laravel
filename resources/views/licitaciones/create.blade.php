@extends('layouts.base')

@section('title', 'Nueva Licitación')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-plus-circle me-2"></i>Nueva Licitación
    </h4>
    <a href="{{ route('licitaciones.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
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

@if ($responsables->isEmpty())
    <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        <span>
            <strong>No hay responsables registrados.</strong>
            Para crear una licitación primero debe
            <a href="{{ route('responsables.create') }}" class="alert-link">registrar un responsable</a>.
        </span>
    </div>
@endif

<form action="{{ route('licitaciones.store') }}" method="POST">
    @csrf

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- DATOS DEL PROCESO --}}
            <h5 class="text-secondary mb-3">Datos de la licitación</h5>
            <hr class="delimitadorSuperior">

            <div class="contenedorInformacion">
                <div class="objeto full-width">
                    <label for="objeto" class="form-label fw-semibold">Objeto <span class="text-danger">*</span></label>
                    <input type="text" name="objeto" id="objeto"
                           class="form-control @error('objeto') is-invalid @enderror"
                           value="{{ old('objeto') }}" required maxlength="500"
                           placeholder="Ej: Adquisición de equipos de cómputo">
                </div>

                <div class="descripcion full-width">
                    <label for="descripcion" class="form-label fw-semibold">Descripción / Alcance</label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                              class="form-control @error('descripcion') is-invalid @enderror"
                              maxlength="2000" placeholder="Describa el alcance de la licitación">{{ old('descripcion') }}</textarea>
                </div>

                <div class="moneda">
                    <label for="moneda" class="form-label fw-semibold">Moneda</label>
                    <select name="moneda" id="moneda"
                            class="form-select @error('moneda') is-invalid @enderror" required>
                        <option value="COP" @selected(old('moneda') === 'COP')>🇨🇴 COP (Peso colombiano)</option>
                        <option value="USD" @selected(old('moneda') === 'USD')>🇺🇸 USD (Dólar)</option>
                        <option value="EUR" @selected(old('moneda') === 'EUR')>🇪🇺 EUR (Euro)</option>
                    </select>
                </div>

                <div class="presupuesto">
                    <label for="presupuesto" class="form-label fw-semibold">Presupuesto <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="presupuesto" id="presupuesto"
                               class="form-control @error('presupuesto') is-invalid @enderror"
                               value="{{ old('presupuesto') }}" required min="0" step="1" placeholder="0">
                    </div>
                </div>

                <div class="estado">
                    <label for="estado" class="form-label fw-semibold">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        @foreach (\App\Models\Proceso::ESTADOS as $est)
                            <option value="{{ $est }}" @selected(old('estado', 'Borrador') === $est)>{{ $est }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="responsable">
                    <label for="responsable_id" class="form-label fw-semibold">Responsable <span class="text-danger">*</span></label>
                    <select name="responsable_id" id="responsable_id" class="form-select" required {{ $responsables->isEmpty() ? 'disabled' : '' }}>
                        <option value="">— Seleccione —</option>
                        @foreach ($responsables as $r)
                            <option value="{{ $r->id }}" @selected(old('responsable_id') == $r->id)>
                                {{ $r->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @if ($responsables->isEmpty())
                        <div class="form-text text-danger">Debe registrar un responsable primero.</div>
                    @endif
                </div>
            </div>

            {{-- CRONOGRAMA --}}
            <hr class="mt-4">
            <h5 class="text-secondary mb-3">Cronograma</h5>
            <hr class="delimitadorSuperior">

            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label for="fecha_inicio" class="form-label fw-semibold">Fecha inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                           class="form-control @error('fecha_inicio') is-invalid @enderror"
                           value="{{ old('fecha_inicio') }}" required>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="hora_inicio" class="form-label fw-semibold">Hora inicio <span class="text-danger">*</span></label>
                    <input type="time" name="hora_inicio" id="hora_inicio"
                           class="form-control @error('hora_inicio') is-invalid @enderror"
                           value="{{ old('hora_inicio') }}" required>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="fecha_cierre" class="form-label fw-semibold">Fecha cierre <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_cierre" id="fecha_cierre"
                           class="form-control @error('fecha_cierre') is-invalid @enderror"
                           value="{{ old('fecha_cierre') }}" required>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="hora_cierre" class="form-label fw-semibold">Hora cierre <span class="text-danger">*</span></label>
                    <input type="time" name="hora_cierre" id="hora_cierre"
                           class="form-control @error('hora_cierre') is-invalid @enderror"
                           value="{{ old('hora_cierre') }}" required>
                </div>
            </div>

            {{-- BOTONES --}}
            <hr class="mt-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('licitaciones.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Guardar Licitación
                </button>
            </div>

        </div>
    </div>
</form>
@endsection
