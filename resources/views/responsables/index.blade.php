@extends('layouts.base')

@section('title', 'Responsables')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-people me-2"></i>Responsables
    </h4>
    <a href="{{ route('responsables.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Responsable
    </a>
</div>

<div class="card shadow-sm border">
    <div class="card-body p-0">
        <table class="table table-licitaciones table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre completo</th>
                    <th>Teléfono</th>
                    <th>Correo electrónico</th>
                    <th class="text-center">Procesos</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($responsables as $r)
                    <tr>
                        <td class="text-center fw-bold">{{ $r->id }}</td>
                        <td class="fw-semibold">{{ $r->nombre_completo }}</td>
                        <td>{{ $r->numero_telefono }}</td>
                        <td>
                            @if ($r->correo_electronico)
                                <a href="mailto:{{ $r->correo_electronico }}" class="text-decoration-none">{{ $r->correo_electronico }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $r->procesos_count }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('responsables.edit', $r) }}"
                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('responsables.destroy', $r) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este responsable?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox me-2"></i>No hay responsables registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $responsables->links() }}
</div>
@endsection
