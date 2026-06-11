@extends('layouts.base')

@section('title', 'Reportes y Estadísticas')

@section('content')
{{-- Encabezado --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="mb-0 fw-bold" style="color: var(--color-primary);">
        <i class="bi bi-bar-chart-fill me-2"></i>Reportes y Estadísticas
    </h4>
    <div class="d-flex gap-2">
        @if (Auth::user()?->esAdmin())
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="collapse" data-bs-target="#exportPanel">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar CSV
            </button>
        @endif
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

{{-- Panel de exportación colapsable --}}
@if (Auth::user()?->esAdmin())
<div class="collapse mb-4" id="exportPanel">
    <div class="card border-success shadow-sm">
        <div class="card-header bg-success text-white py-2">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar licitaciones
        </div>
        <div class="card-body">
            <form action="{{ route('reportes.export') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Objeto, descripción…">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Moneda</label>
                    <select name="moneda" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="COP">🇨🇴 COP</option>
                        <option value="USD">🇺🇸 USD</option>
                        <option value="EUR">🇪🇺 EUR</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-download me-1"></i> Descargar CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Tarjetas de totales (estilo vanilla) --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: rgba(26, 54, 93, 0.05);">
            <div class="card-body text-center py-4">
                <i class="bi bi-clipboard-data" style="font-size: 2rem; color: var(--color-primary);"></i>
                <h3 class="mt-2 mb-0 fw-bold text-nowrap">{{ number_format($totalLicitaciones) }}</h3>
                <p class="text-muted mb-0 small">Total Licitaciones</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: rgba(39, 103, 73, 0.05);">
            <div class="card-body text-center py-4">
                <i class="bi bi-cash-stack" style="font-size: 2rem; color: #276749;"></i>
                <h3 class="mt-2 mb-0 fw-bold text-nowrap">${{ number_format($presupuestoTotal, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0 small">Presupuesto Global</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: rgba(13, 202, 240, 0.05);">
            <div class="card-body text-center py-4">
                <i class="bi bi-calculator" style="font-size: 2rem; color: #0dcaf0;"></i>
                <h3 class="mt-2 mb-0 fw-bold text-nowrap">${{ number_format($promedioPresupuesto, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0 small">Presupuesto Promedio</p>
            </div>
        </div>
    </div>
</div>

{{-- Fila de tablas: por estado y presupuesto por estado --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-1"></i> Licitaciones por Estado</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-licitaciones mb-0">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th class="text-end">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($licitacionesPorEstado as $row)
                                <tr>
                                    <td>
                                        <span class="badge {{ \App\Models\Licitacion::COLORES_ESTADO[$row['estado']] ?? 'bg-secondary' }}">
                                            {{ $row['estado'] }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold text-nowrap">{{ number_format($row['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">No hay licitaciones registradas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-1"></i> Presupuesto por Estado</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-licitaciones mb-0">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Presupuesto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($presupuestoPorEstado as $row)
                                <tr>
                                    <td>
                                        <span class="badge {{ \App\Models\Licitacion::COLORES_ESTADO[$row['estado']] ?? 'bg-secondary' }}">
                                            {{ $row['estado'] }}
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">{{ number_format($row['cantidad']) }}</td>
                                    <td class="text-end fw-semibold text-nowrap">${{ number_format((int) $row['presupuesto_total'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No hay licitaciones registradas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Segunda fila: distribución por moneda + próximos a cerrar --}}
<div class="row g-3 mb-4">
    {{-- Distribución por moneda --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-currency-exchange me-1"></i> Distribución por Moneda</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-3">
                        <thead>
                            <tr>
                                <th>Moneda</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Presupuesto</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($porMoneda as $item)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">
                                            @switch($item->moneda)
                                                @case('COP') 🇨🇴 @break
                                                @case('USD') 🇺🇸 @break
                                                @case('EUR') 🇪🇺 @break
                                            @endswitch
                                            {{ $item->moneda }}
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">{{ number_format($item->total) }}</td>
                                    <td class="text-end text-nowrap">${{ number_format((float) $item->presupuesto, 0, ',', '.') }}</td>
                                    <td class="text-end text-nowrap">{{ $item->porcentaje }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Progress bars --}}
                @if ($porMoneda->isNotEmpty())
                    @php $maxPct = $porMoneda->max('porcentaje'); @endphp
                    @foreach ($porMoneda as $item)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span>
                                    @switch($item->moneda)
                                        @case('COP') 🇨🇴 @break
                                        @case('USD') 🇺🇸 @break
                                        @case('EUR') 🇪🇺 @break
                                    @endswitch
                                    <strong>{{ $item->moneda }}</strong>
                                </span>
                                <span class="text-nowrap">{{ $item->porcentaje }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $item->moneda === 'COP' ? 'success' : ($item->moneda === 'USD' ? 'primary' : 'warning') }}"
                                     role="progressbar"
                                     style="width: {{ $maxPct > 0 ? ($item->porcentaje / $maxPct) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Próximos a cerrar --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Próximos a Cerrar (30 días)</h6>
            </div>
            <div class="card-body">
                @if ($proximosCerrar->isEmpty())
                    <p class="text-muted text-center my-4">No hay licitaciones próximas a cerrar.</p>
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
                                        <td class="text-nowrap">
                                            <a href="{{ route('licitaciones.show', $p) }}"
                                               class="text-decoration-none fw-semibold" style="color: var(--color-primary);">
                                                #{{ $p->codigo_licitacion }}
                                            </a>
                                        </td>
                                        <td>{{ Str::limit($p->objeto, 35) }}</td>
                                        <td class="text-nowrap">
                                            <span class="badge bg-{{ $p->fecha_cierre->diffInDays(now()) <= 7 ? 'danger' : 'warning' }}">
                                                {{ $p->fecha_cierre->format('d/m/Y') }}
                                            </span>
                                            <small class="text-muted ms-1">{{ $p->fecha_cierre->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-end text-nowrap">${{ number_format($p->presupuesto, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Licitaciones creadas por mes (bar chart estilo vanilla mejorado) --}}
@if ($meses->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-calendar-week me-1"></i> Licitaciones Creadas por Mes</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    @php $maxTotal = $meses->max('total'); @endphp
                    @foreach ($meses as $m)
                        <div class="text-center" style="min-width: 55px;">
                            <div class="small text-muted mb-1 text-nowrap">{{ $m->total }}</div>
                            <div class="rounded mx-auto"
                                 style="background: var(--color-primary-light);
                                        height: {{ max(($m->total / max($maxTotal, 1)) * 120, 8) }}px;
                                        width: 36px;"></div>
                            <div class="small mt-1 text-nowrap">{{ $m->mes }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
