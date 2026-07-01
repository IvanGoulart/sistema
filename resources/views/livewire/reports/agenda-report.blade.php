<div>

{{-- Filtros (ocultos na impressão) --}}
<div class="card mb-4 no-print">
    <div class="card-header d-flex align-items-center gap-3 pb-3">
        <div class="d-flex align-items-center justify-content-center text-white"
             style="width:38px;height:38px;border-radius:8px;background:#696cff;">
            <i class="mdi mdi-filter-outline"></i>
        </div>
        <h5 class="card-title mb-0">Filtros</h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold">Data inicial</label>
                <input type="date" class="form-control @error('dateStart') is-invalid @enderror"
                       wire:model="dateStart">
                @error('dateStart') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold">Data final</label>
                <input type="date" class="form-control @error('dateEnd') is-invalid @enderror"
                       wire:model="dateEnd">
                @error('dateEnd') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label fw-semibold">Profissional</label>
                <select class="form-select" wire:model="employeeId">
                    <option value="">Todos</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 col-sm-6">
                <label class="form-label fw-semibold">Serviço</label>
                <select class="form-select" wire:model="serviceId">
                    <option value="">Todos</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100" wire:click="generate" wire:loading.attr="disabled">
                    <span wire:loading wire:target="generate" class="spinner-border spinner-border-sm me-1"></span>
                    <i class="mdi mdi-magnify me-1" wire:loading.remove wire:target="generate"></i>
                    Gerar
                </button>
            </div>

        </div>
    </div>
</div>

@if($generated)

{{-- Cabeçalho de impressão --}}
<div class="print-only mb-4">
    <h4 class="mb-1">Relatório de Agendamentos</h4>
    <p class="text-muted mb-0">
        Período: {{ \Carbon\Carbon::parse($dateStart)->format('d/m/Y') }}
        até {{ \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') }}
        &nbsp;|&nbsp; Gerado em: {{ now()->format('d/m/Y H:i') }}
    </p>
</div>

{{-- Cards de resumo --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;border-radius:50%;background:#696cff;flex-shrink:0;">
                    <i class="mdi mdi-calendar-check-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Total</div>
                    <h3 class="mb-0 fw-bold">{{ $total }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center"
                     style="width:46px;height:46px;border-radius:50%;background:rgba(40,199,111,.15);color:#28c76f;flex-shrink:0;">
                    <i class="mdi mdi-check-circle-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Realizados</div>
                    <h3 class="mb-0 fw-bold text-success">{{ $done }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center"
                     style="width:46px;height:46px;border-radius:50%;background:rgba(255,62,29,.15);color:#ff3e1d;flex-shrink:0;">
                    <i class="mdi mdi-close-circle-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Cancelados</div>
                    <h3 class="mb-0 fw-bold text-danger">{{ $cancelled }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center"
                     style="width:46px;height:46px;border-radius:50%;background:rgba(255,171,0,.15);color:#ffab00;flex-shrink:0;">
                    <i class="mdi mdi-percent-outline mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Cancelamentos</div>
                    <h3 class="mb-0 fw-bold text-warning">{{ $cancelRate }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center"
                     style="width:46px;height:46px;border-radius:50%;background:rgba(105,108,255,.15);color:#696cff;flex-shrink:0;">
                    <i class="mdi mdi-cash-multiple mdi-24px"></i>
                </div>
                <div>
                    <div class="text-muted small">Faturamento <span class="text-muted" style="font-size:.7rem">(realizados)</span></div>
                    <h3 class="mb-0 fw-bold" style="color:#696cff;">R$ {{ number_format($revenue, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Tabela --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between pb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center text-white"
                 style="width:38px;height:38px;border-radius:8px;background:#696cff;">
                <i class="mdi mdi-table-large"></i>
            </div>
            <div>
                <h5 class="card-title mb-0">Detalhamento</h5>
                <small class="text-muted">
                    {{ \Carbon\Carbon::parse($dateStart)->format('d/m/Y') }}
                    até {{ \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') }}
                </small>
            </div>
        </div>
        <button class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()">
            <i class="mdi mdi-printer-outline me-1"></i> Imprimir
        </button>
    </div>

    <div class="card-body {{ $appointments->isEmpty() ? '' : 'p-0' }}">
        @if($appointments->isEmpty())
            <div class="text-center py-5">
                <i class="mdi mdi-calendar-remove-outline text-muted" style="font-size:3rem;opacity:.4"></i>
                <p class="text-muted mt-2 mb-0">Nenhum agendamento encontrado para o período.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Data</th>
                            <th>Horário</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Profissional</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $a)
                            <tr>
                                <td class="ps-4">
                                    <i class="mdi mdi-calendar-outline me-1 text-muted no-print"></i>
                                    {{ \Carbon\Carbon::parse($a->day)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">{{ substr($a->hour, 0, 5) }}</span>
                                </td>
                                <td class="fw-semibold">{{ $a->client_name }}</td>
                                <td>
                                    <span class="badge bg-label-warning">{{ $a->service_name }}</span>
                                </td>
                                <td class="text-muted">{{ $a->employee_name }}</td>
                                <td>
                                    @if($a->cancel)
                                        <span class="badge bg-label-danger">Cancelado</span>
                                    @else
                                        <span class="badge bg-label-success">Realizado</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 {{ $a->cancel ? 'text-muted text-decoration-line-through' : 'fw-semibold' }}">
                                    @if($a->service_price !== null)
                                        R$ {{ number_format($a->service_price, 2, ',', '.') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">Total (realizados)</th>
                            <th class="text-end pe-4">R$ {{ number_format($revenue, 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

@else

{{-- Estado inicial --}}
<div class="card">
    <div class="card-body text-center py-5">
        <i class="mdi mdi-chart-bar text-muted" style="font-size:4rem;opacity:.3"></i>
        <h5 class="text-muted mt-3 mb-1">Nenhum relatório gerado</h5>
        <p class="text-muted small mb-0">Selecione o período e clique em <strong>Gerar</strong> para visualizar os dados.</p>
    </div>
</div>

@endif

<style>
@media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    .portal-nav, .layout-navbar, .layout-menu, .layout-footer,
    .content-backdrop, nav, aside { display: none !important; }
    .layout-page { padding: 0 !important; }
    .layout-container { display: block !important; }
    .content-wrapper { margin: 0 !important; padding: 1rem !important; }
    .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    .table { font-size: 12px; }
}
.print-only { display: none; }
</style>

</div>
