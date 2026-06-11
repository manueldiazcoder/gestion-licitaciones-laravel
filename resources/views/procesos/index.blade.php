@extends('layouts.base')

@section('title', 'Consultar Procesos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-search me-2"></i>Consultar Procesos
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('procesos.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
        </a>
        @if (Auth::user()?->esAdmin())
            <a href="{{ route('procesos.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Nuevo Proceso
            </a>
        @endif
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('procesos.index') }}" class="card shadow-sm border mb-4">
    <div class="card-body">
        <h6 class="card-title text-secondary mb-3">
            <i class="bi bi-funnel me-1"></i>Buscar procesos
        </h6>
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold small">ID Proceso</label>
                <input type="number" name="codigo" class="form-control form-control-sm"
                       placeholder="Número" value="{{ request('codigo') }}" min="1">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Objeto / Descripción</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Palabra clave" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Responsable</label>
                <select name="responsable_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach ($responsables as $r)
                        <option value="{{ $r->codigo_responsable }}" @selected(request('responsable_id') == $r->codigo_responsable)>
                            {{ $r->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach (\App\Models\Proceso::ESTADOS as $est)
                        <option value="{{ $est }}" @selected(request('estado') === $est)>{{ $est }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label fw-semibold small">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm"
                       value="{{ request('desde') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label fw-semibold small">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm"
                       value="{{ request('hasta') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('procesos.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </div>
    </div>
</form>

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table table-licitaciones table-bordered align-middle mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Objeto</th>
                <th>Estado</th>
                <th>Responsable</th>
                <th>Inicio</th>
                <th>Cierre</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($procesos as $proceso)
                <tr>
                    <td class="text-center fw-bold">{{ $proceso->codigo_proceso }}</td>
                    <td>
                        <a href="{{ route('procesos.show', $proceso) }}" class="text-decoration-none fw-semibold" style="color: var(--color-primary);">
                            {{ Str::limit($proceso->objeto, 60) }}
                        </a>
                        @if ($proceso->actividad)
                            <br><small class="text-muted">{{ Str::limit($proceso->actividad, 40) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ \App\Models\Proceso::COLORES_ESTADO[$proceso->estado] ?? 'bg-secondary' }}">
                            {{ $proceso->estado }}
                        </span>
                    </td>
                    <td>{{ $proceso->responsable?->nombre_completo ?: '—' }}</td>
                    <td class="small">
                        <i class="bi bi-calendar"></i> {{ $proceso->fecha_inicio->format('d/m/Y') }}
                        <br><i class="bi bi-clock"></i> {{ $proceso->hora_inicio }}
                    </td>
                    <td class="small">
                        <i class="bi bi-calendar"></i> {{ $proceso->fecha_cierre->format('d/m/Y') }}
                        <br><i class="bi bi-clock"></i> {{ $proceso->hora_cierre }}
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
<div class="d-flex justify-content-center mt-3">
    {{ $procesos->links() }}
</div>
@endsection
