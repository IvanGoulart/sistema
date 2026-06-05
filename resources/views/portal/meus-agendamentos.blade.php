@extends('layouts.portal')

@section('title', 'Meus Agendamentos')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1">Meus Agendamentos</h4>
        <p class="text-muted mb-0">Histórico e próximos atendimentos</p>
    </div>
    <a href="{{ route('portal.agendar') }}" class="btn btn-brand">
        <i class="mdi mdi-plus me-1"></i> Novo Agendamento
    </a>
</div>

@if(session('message'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Próximos --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-3 pb-3">
        <div class="d-flex align-items-center justify-content-center text-white"
             style="width:38px;height:38px;border-radius:8px;background:var(--brand);">
            <i class="mdi mdi-calendar-clock-outline"></i>
        </div>
        <div>
            <h5 class="card-title mb-0">Próximos Atendimentos</h5>
            <small class="text-muted">Agendamentos confirmados</small>
        </div>
        @if($upcoming->count())
            <span class="badge rounded-pill ms-auto" style="background:var(--brand);">{{ $upcoming->count() }}</span>
        @endif
    </div>

    <div class="card-body {{ $upcoming->isEmpty() ? '' : 'p-0' }}">
        @if($upcoming->isEmpty())
            <div class="text-center py-4">
                <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size:3rem;opacity:.4"></i>
                <p class="text-muted mt-2 mb-2">Você não tem agendamentos futuros.</p>
                <a href="{{ route('portal.agendar') }}" class="btn btn-sm btn-brand">
                    <i class="mdi mdi-plus me-1"></i> Agendar agora
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Data</th>
                            <th>Horário</th>
                            <th>Serviço</th>
                            <th>Profissional</th>
                            <th class="text-end pe-4">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcoming as $s)
                            <tr>
                                <td class="ps-4">
                                    <i class="mdi mdi-calendar-outline me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($s->day)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge fw-semibold"
                                          style="background:#e8e8ff;color:var(--brand);">
                                        {{ substr($s->hour, 0, 5) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge fw-semibold"
                                          style="background:rgba(255,171,0,.15);color:#a07800;">
                                        {{ $s->service_name }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <i class="mdi mdi-account-tie-outline me-1"></i>
                                    {{ $s->employee_name }}
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST"
                                          action="{{ route('portal.cancelar', $s->id) }}"
                                          onsubmit="return confirm('Deseja cancelar este agendamento?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="mdi mdi-close me-1"></i>Cancelar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Histórico --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-3 pb-3">
        <div class="d-flex align-items-center justify-content-center bg-secondary text-white"
             style="width:38px;height:38px;border-radius:8px;">
            <i class="mdi mdi-history"></i>
        </div>
        <div>
            <h5 class="card-title mb-0">Histórico</h5>
            <small class="text-muted">Atendimentos realizados e cancelados</small>
        </div>
    </div>

    <div class="card-body {{ $past->isEmpty() ? '' : 'p-0' }}">
        @if($past->isEmpty())
            <div class="text-center py-4">
                <i class="mdi mdi-calendar-remove-outline text-muted" style="font-size:3rem;opacity:.4"></i>
                <p class="text-muted mt-2 mb-0">Nenhum histórico encontrado.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Data</th>
                            <th>Horário</th>
                            <th>Serviço</th>
                            <th>Profissional</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($past as $s)
                            <tr class="text-muted">
                                <td class="ps-4">{{ \Carbon\Carbon::parse($s->day)->format('d/m/Y') }}</td>
                                <td>{{ substr($s->hour, 0, 5) }}</td>
                                <td>{{ $s->service_name }}</td>
                                <td>{{ $s->employee_name }}</td>
                                <td>
                                    @if($s->cancel)
                                        <span class="badge fw-semibold" style="background:rgba(255,62,29,.15);color:#c0392b;">Cancelado</span>
                                    @else
                                        <span class="badge fw-semibold" style="background:rgba(40,199,111,.15);color:#1a7a45;">Concluído</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
