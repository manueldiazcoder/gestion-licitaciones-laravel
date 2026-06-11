@extends('layouts.base')

@section('title', 'Consultar Licitaciones')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-search me-2"></i>Consultar Licitaciones
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('licitaciones.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
        </a>
        @if (Auth::user()?->esAdmin())
            <a href="{{ route('licitaciones.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Nueva Licitación
            </a>
        @endif
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('licitaciones.index') }}" class="card shadow-sm border mb-4">
    <div class="card-body">
        <h6 class="card-title text-secondary mb-3">
            <i class="bi bi-funnel me-1"></i>Buscar licitaciones
        </h6>
        <div class="row g-3">
            {{-- Fila 1: todos los inputs de filtro --}}
            <div class="col-md-1">
                <label class="form-label fw-semibold small"># ID</label>
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
                        <option value="{{ $r->id }}" @selected(request('responsable_id') == $r->id)>
                            {{ $r->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach (\App\Models\Licitacion::ESTADOS as $est)
                        <option value="{{ $est }}" @selected(request('estado') === $est)>{{ $est }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm"
                       value="{{ request('desde') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm"
                       value="{{ request('hasta') }}">
            </div>
        </div>
        {{-- Fila 2: botones abajo a la derecha --}}
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('licitaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-circle me-1"></i>Limpiar
            </a>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search me-1"></i>Buscar
            </button>
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
            @forelse ($licitaciones as $licitacion)
                <tr>
                    <td class="text-center fw-bold">{{ $licitacion->codigo_licitacion }}</td>
                    <td>
                        <a href="{{ route('licitaciones.show', $licitacion) }}" class="text-decoration-none fw-semibold" style="color: var(--color-primary);">
                            {{ Str::limit($licitacion->objeto, 60) }}
                        </a>
                        @if ($licitacion->actividad)
                            <br><small class="text-muted">{{ Str::limit($licitacion->actividad, 40) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ \App\Models\Licitacion::COLORES_ESTADO[$licitacion->estado] ?? 'bg-secondary' }}">
                            {{ $licitacion->estado }}
                        </span>
                    </td>
                    <td>{{ $licitacion->responsable?->nombre_completo ?: '—' }}</td>
                    <td class="small">
                        <i class="bi bi-calendar"></i> {{ $licitacion->fecha_inicio->format('d/m/Y') }}
                        <br><i class="bi bi-clock"></i> {{ $licitacion->hora_inicio }}
                    </td>
                    <td class="small">
                        <i class="bi bi-calendar"></i> {{ $licitacion->fecha_cierre->format('d/m/Y') }}
                        <br><i class="bi bi-clock"></i> {{ $licitacion->hora_cierre }}
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('licitaciones.show', $licitacion) }}"
                               class="btn btn-outline-info btn-sm" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if (Auth::user()?->esAdmin())
                                <a href="{{ route('licitaciones.edit', $licitacion) }}"
                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('licitaciones.destroy', $licitacion) }}"
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
                        <i class="bi bi-inbox me-2"></i>No se encontraron licitaciones.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="d-flex justify-content-center mt-3">
    {{ $licitaciones->links() }}
</div>
@endsection
