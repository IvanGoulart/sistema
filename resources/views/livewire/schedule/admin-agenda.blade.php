<div>

    {{-- Flash --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Barra de navegação --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                {{-- Navegação de semana --}}
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-icon btn-outline-secondary btn-sm"
                            wire:click="previousWeek" title="Semana anterior">
                        <i class="mdi mdi-chevron-left"></i>
                    </button>

                    <div class="text-center" style="min-width: 220px;">
                        <span class="fw-semibold">{{ $this->weekLabel() }}</span>
                        <div class="text-muted small">
                            {{ $this->totalForWeek() }}
                            {{ $this->totalForWeek() === 1 ? 'agendamento' : 'agendamentos' }} na semana
                        </div>
                    </div>

                    <button class="btn btn-icon btn-outline-secondary btn-sm"
                            wire:click="nextWeek" title="Próxima semana">
                        <i class="mdi mdi-chevron-right"></i>
                    </button>

                    <button class="btn btn-sm btn-outline-primary ms-1"
                            wire:click="thisWeek">
                        Hoje
                    </button>
                </div>

                {{-- Filtro por profissional --}}
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0 flex-shrink-0">
                        <i class="mdi mdi-account-outline me-1"></i>Profissional:
                    </label>
                    <select class="form-select form-select-sm" wire:model.live="filterEmployeeId"
                            style="min-width: 200px;">
                        <option value="">Todos</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Loading --}}
    <div wire:loading wire:target="previousWeek,nextWeek,thisWeek,updatedFilterEmployeeId"
         class="text-center py-4">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-2 mb-0">Carregando agenda...</p>
    </div>

    {{-- Dias da semana --}}
    <div wire:loading.remove wire:target="previousWeek,nextWeek,thisWeek,updatedFilterEmployeeId">
        <div class="row g-3">
            @foreach($days as $day)
                @php
                    $dayAppointments = $appointments[$day['date']] ?? [];
                    $count = count($dayAppointments);
                @endphp

                <div class="col-12">
                    <div class="card {{ $day['isToday'] ? 'border-primary' : '' }}">

                        {{-- Cabeçalho do dia --}}
                        <div class="card-header d-flex align-items-center justify-content-between py-2
                                    {{ $day['isToday'] ? 'bg-primary text-white' : ($day['isPast'] ? 'bg-light' : '') }}">
                            <div class="d-flex align-items-center gap-2">
                                @if($day['isToday'])
                                    <span class="badge bg-white text-primary rounded-pill fw-bold">HOJE</span>
                                @endif
                                <span class="{{ $day['isToday'] ? 'text-white' : ($day['isPast'] ? 'text-muted' : '') }} fw-semibold">
                                    {{ $day['label'] }}, {{ $day['short'] }}
                                </span>
                            </div>
                            @if($count > 0)
                                <span class="badge {{ $day['isToday'] ? 'bg-white text-primary' : 'bg-label-primary' }} rounded-pill">
                                    {{ $count }} {{ $count === 1 ? 'agendamento' : 'agendamentos' }}
                                </span>
                            @endif
                        </div>

                        {{-- Conteúdo do dia --}}
                        <div class="card-body {{ $count === 0 ? 'py-3' : 'p-0' }}">

                            @if($count === 0)
                                <p class="text-muted small mb-0 d-flex align-items-center gap-1">
                                    <i class="mdi mdi-calendar-blank-outline"></i>
                                    Nenhum agendamento para este dia.
                                </p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4" style="width: 80px;">Horário</th>
                                                <th>Cliente</th>
                                                <th>Serviço</th>
                                                <th>Profissional</th>
                                                <th class="text-end pe-4">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dayAppointments as $appt)
                                                <tr wire:key="appt-{{ $appt->id }}"
                                                    class="{{ $day['isPast'] ? 'text-muted' : '' }}">

                                                    <td class="ps-4">
                                                        <span class="badge bg-label-{{ $day['isToday'] ? 'primary' : ($day['isPast'] ? 'secondary' : 'info') }} fw-semibold">
                                                            {{ substr($appt->hour, 0, 5) }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-xs bg-label-secondary rounded-circle flex-shrink-0">
                                                                <i class="mdi mdi-account-outline" style="font-size: .8rem;"></i>
                                                            </div>
                                                            <span>{{ $appt->client_name }}</span>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-label-warning">{{ $appt->service_name }}</span>
                                                    </td>

                                                    <td class="text-muted">
                                                        <i class="mdi mdi-account-tie-outline me-1"></i>
                                                        {{ $appt->employee_name }}
                                                    </td>

                                                    <td class="text-end pe-4">
                                                        @if($confirmingCancelId === $appt->id)
                                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                                <small class="text-danger fw-semibold">Confirmar cancelamento?</small>
                                                                <button class="btn btn-danger btn-sm"
                                                                        wire:click="cancelAppointment({{ $appt->id }})">
                                                                    <i class="mdi mdi-check me-1"></i>Sim
                                                                </button>
                                                                <button class="btn btn-outline-secondary btn-sm"
                                                                        wire:click="dismissCancel">
                                                                    Não
                                                                </button>
                                                            </div>
                                                        @else
                                                            <button class="btn btn-outline-danger btn-sm"
                                                                    wire:click="confirmCancel({{ $appt->id }})">
                                                                <i class="mdi mdi-close me-1"></i>Cancelar
                                                            </button>
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
                </div>
            @endforeach
        </div>
    </div>

</div>
