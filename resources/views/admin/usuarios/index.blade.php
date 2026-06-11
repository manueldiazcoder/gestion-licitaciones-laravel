@extends('layouts.base')

@section('title', 'Usuarios del Sistema')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-person-gear me-2"></i>Usuarios del Sistema
    </h4>
</div>

<div class="card shadow-sm border">
    <div class="card-body p-0">
        <table class="table table-licitaciones table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-center">Licitaciones</th>
                    <th>Registro</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $u)
                    <tr>
                        <td class="text-center fw-bold">{{ $u->id }}</td>
                        <td class="fw-semibold">
                            <i class="bi bi-person-circle me-1"></i>{{ $u->name }}
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>{!! $u->getRolBadge() !!}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $u->licitaciones_creadas_count }}</span>
                        </td>
                        <td class="small">{{ $u->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('usuarios.edit', $u) }}"
                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($u->id !== Auth::id())
                                    <form action="{{ route('usuarios.destroy', $u) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este usuario?')">
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
                            <i class="bi bi-inbox me-2"></i>No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $usuarios->links() }}
</div>
@endsection
