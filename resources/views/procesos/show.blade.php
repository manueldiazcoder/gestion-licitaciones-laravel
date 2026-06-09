@extends('layouts.base')

@section('title', "Proceso #{$proceso->codigo_proceso}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
        <i class="bi bi-file-text me-2"></i>Proceso #{{ $proceso->codigo_proceso }}
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('procesos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
        @if (Auth::user()?->esAdmin())
            <a href="{{ route('procesos.edit', $proceso) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card bg-dark border-secondary">
    <div class="card-body">
        <div class="row g-3">
            {{-- Objeto --}}
            <div class="col-12">
                <label class="text-muted small text-uppercase">Objeto</label>
                <p class="fw-semibold mb-0">{{ $proceso->objeto }}</p>
            </div>

            {{-- Actividad --}}
            @if ($proceso->actividad)
                <div class="col-12">
                    <label class="text-muted small text-uppercase">Actividad</label>
                    <p class="mb-0">{{ $proceso->actividad }}</p>
                </div>
            @endif

            {{-- Descripción --}}
            <div class="col-12">
                <label class="text-muted small text-uppercase">Descripción / Alcance</label>
                <p class="mb-0 text-justify">{{ $proceso->descripcion }}</p>
            </div>

            <hr class="border-secondary">

            {{-- Presupuesto y moneda --}}
            <div class="col-md-4">
                <label class="text-muted small text-uppercase">Presupuesto</label>
                <p class="h5 mb-0">{{ $proceso->presupuesto_formateado }}</p>
            </div>
            <div class="col-md-4">
                <label class="text-muted small text-uppercase">Moneda</label>
                <p class="mb-0">{{ $proceso->moneda }}</p>
            </div>

            <hr class="border-secondary">

            {{-- Fechas --}}
            <div class="col-md-3">
                <label class="text-muted small text-uppercase">Fecha de inicio</label>
                <p class="mb-0">{{ $proceso->fecha_inicio->format('d/m/Y') }}</p>
                <small class="text-muted">{{ $proceso->hora_inicio }}</small>
            </div>
            <div class="col-md-3">
                <label class="text-muted small text-uppercase">Fecha de cierre</label>
                <p class="mb-0">{{ $proceso->fecha_cierre->format('d/m/Y') }}</p>
                <small class="text-muted">{{ $proceso->hora_cierre }}</small>
            </div>

            <hr class="border-secondary">

            {{-- Metadatos --}}
            <div class="col-md-6">
                <label class="text-muted small text-uppercase">Creado</label>
                <p class="mb-0 small">{{ $proceso->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <label class="text-muted small text-uppercase">Última modificación</label>
                <p class="mb-0 small">{{ $proceso->updated_at?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
