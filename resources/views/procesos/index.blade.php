@extends('layouts.base')

@section('title', 'Consultar Procesos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-search me-2"></i>Consultar Procesos</h4>
    @if (Auth::user()?->esAdmin())
        <a href="{{ route('procesos.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Proceso
        </a>
    @endif
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('procesos.index') }}" class="row g-2 mb-4">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Buscar por objeto, descripción..."
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <select name="moneda" class="form-select form-select-sm">
            <option value="">Todas las monedas</option>
            <option value="COP" @selected(request('moneda') === 'COP')>COP</option>
            <option value="USD" @selected(request('moneda') === 'USD')>USD</option>
            <option value="EUR" @selected(request('moneda') === 'EUR')>EUR</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="desde" class="form-control form-control-sm"
               value="{{ request('desde') }}" placeholder="Desde">
    </div>
    <div class="col-md-2">
        <input type="date" name="hasta" class="form-control form-control-sm"
               value="{{ request('hasta') }}" placeholder="Hasta">
    </div>
    <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-fill">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="{{ route('procesos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x-circle"></i>
        </a>
    </div>
</form>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table table-dark table-hover table-bordered align-middle">
        <thead class="table-secondary text-dark">
            <tr>
                <th>#</th>
                <th>Objeto</th>
                <th>Presupuesto</th>
                <th>Moneda</th>
                <th>Inicio</th>
                <th>Cierre</th>
                {{-- <th>Creado</th> --}}
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($procesos as $proceso)
                <tr>
                    <td class="text-center fw-bold">{{ $proceso->codigo_proceso }}</td>
                    <td>
                        <a href="{{ route('procesos.show', $proceso) }}" class="text-decoration-none fw-semibold">
                            {{ Str::limit($proceso->objeto, 60) }}
                        </a>
                        @if ($proceso->actividad)
                            <br><small class="text-muted">{{ Str::limit($proceso->actividad, 40) }}</small>
                        @endif
                    </td>
                    <td class="text-end">{{ $proceso->presupuesto_formateado }}</td>
                    <td>{{ $proceso->moneda }}</td>
                    <td>
                        <span class="small">
                            <i class="bi bi-calendar"></i> {{ $proceso->fecha_inicio->format('d/m/Y') }}
                            <br><i class="bi bi-clock"></i> {{ $proceso->hora_inicio }}
                        </span>
                    </td>
                    <td>
                        <span class="small">
                            <i class="bi bi-calendar"></i> {{ $proceso->fecha_cierre->format('d/m/Y') }}
                            <br><i class="bi bi-clock"></i> {{ $proceso->hora_cierre }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('procesos.show', $proceso) }}"
                               class="btn btn-outline-info btn-sm" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if (Auth::user()?->esAdmin())
                                <a href="{{ route('procesos.edit', $proceso) }}"
                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('procesos.destroy', $proceso) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este proceso?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox me-2"></i>No se encontraron procesos.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="d-flex justify-content-center">
    {{ $procesos->links() }}
</div>
@endsection
