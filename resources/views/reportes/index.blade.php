@extends('layouts.base')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid py-4">

    {{-- Título --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h2 class="mb-0">
            <i class="bi bi-bar-chart-fill me-2"></i>Reportes
        </h2>
        @if (Auth::user()?->esAdmin())
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="collapse" data-bs-target="#exportPanel">
                <i class="bi bi-download me-1"></i>Exportar CSV
            </button>
        @endif
    </div>

    {{-- Panel de exportación (colapsable) --}}
    @if (Auth::user()?->esAdmin())
    <div class="collapse mb-4" id="exportPanel">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar procesos
            </div>
            <div class="card-body">
                <form action="{{ route('reportes.export') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Objeto, descripción…">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Moneda</label>
                        <select name="moneda" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="COP">COP</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-download me-1"></i>Descargar CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Cards de resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <div class="text-primary display-6 mb-2"><i class="bi bi-folder"></i></div>
                    <h5 class="card-title">Total procesos</h5>
                    <p class="display-5 fw-bold text-primary mb-0">{{ number_format($totalProcesos) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <div class="text-success display-6 mb-2"><i class="bi bi-coin"></i></div>
                    <h5 class="card-title">Presupuesto total</h5>
                    <p class="display-6 fw-bold text-success mb-0">${{ number_format($presupuestoTotal, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <div class="text-info display-6 mb-2"><i class="bi bi-calculator"></i></div>
                    <h5 class="card-title">Presupuesto promedio</h5>
                    <p class="display-6 fw-bold text-info mb-0">${{ number_format($promedioPresupuesto, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Distribución por moneda --}}
        <div class="col-lg-6">
            <div class="card border-secondary h-100">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-pie-chart me-1"></i>Distribución por moneda
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Moneda</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Presupuesto</th>
                                <th class="text-end">Promedio</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($porMoneda as $item)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $item->moneda }}</span></td>
                                    <td class="text-end">{{ number_format($item->total) }}</td>
                                    <td class="text-end">${{ number_format((float) $item->presupuesto, 0, ',', '.') }}</td>
                                    <td class="text-end">${{ number_format((float) $item->promedio, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ $item->porcentaje }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Progress bars --}}
                    @if ($porMoneda->isNotEmpty())
                        @php $maxPct = $porMoneda->max('porcentaje'); @endphp
                        <div class="mt-3">
                            @foreach ($porMoneda as $item)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between small">
                                        <span><strong>{{ $item->moneda }}</strong></span>
                                        <span>{{ $item->porcentaje }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $item->moneda === 'COP' ? 'success' : ($item->moneda === 'USD' ? 'primary' : 'warning') }}"
                                             role="progressbar"
                                             style="width: {{ ($item->porcentaje / $maxPct) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Próximos a cerrar --}}
        <div class="col-lg-6">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-clock-history me-1"></i>Próximos a cerrar (30 días)
                </div>
                <div class="card-body">
                    @if ($proximosCerrar->isEmpty())
                        <p class="text-muted text-center my-4">No hay procesos próximos a cerrar.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Objeto</th>
                                        <th>Cierre</th>
                                        <th class="text-end">Presupuesto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proximosCerrar as $p)
                                        <tr>
                                            <td>
                                                <a href="{{ route('procesos.show', $p) }}" class="text-decoration-none">
                                                    {{ $p->codigo_proceso }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($p->objeto, 40) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $p->fecha_cierre->diffInDays(now()) <= 7 ? 'danger' : 'warning' }}">
                                                    {{ $p->fecha_cierre->format('d/m/Y') }}
                                                    ({{ $p->fecha_cierre->diffForHumans() }})
                                                </span>
                                            </td>
                                            <td class="text-end">${{ number_format($p->presupuesto, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- row --}}

    {{-- Procesos por mes --}}
    @if ($meses->isNotEmpty())
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-calendar-week me-1"></i>Procesos creados por mes
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-end gap-3 flex-wrap">
                        @foreach ($meses as $m)
                            <div class="text-center" style="min-width: 60px;">
                                <div class="small text-muted mb-1">{{ $m->total }}</div>
                                <div class="bg-info rounded"
                                     style="height: {{ max($m->total * 20, 8) }}px; width: 40px;"></div>
                                <div class="small mt-1">{{ $m->mes }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>{{-- container --}}
@endsection
