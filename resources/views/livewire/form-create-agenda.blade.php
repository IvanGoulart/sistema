<div>

    {{-- Flash --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    {{-- Formulário de Agendamento --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center pb-3">
            <div class="avatar avatar-sm me-3 bg-label-primary rounded">
                <i class="mdi mdi-calendar-plus mdi-24px"></i>
            </div>
            <div>
                <h5 class="card-title mb-0">Novo Agendamento</h5>
                <small class="text-muted">Selecione o serviço, profissional, data e horário</small>
            </div>
        </div>

        <div class="card-body pt-0">

            <div class="row g-4">

                {{-- Serviço --}}
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="mdi mdi-briefcase-outline me-1 text-primary"></i>
                        Serviço
                    </label>
                    <select class="form-select @error('selectedService') is-invalid @enderror"
                            wire:model.live="selectedService">
                        <option value="">Selecione um serviço...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedService')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Profissional --}}
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="mdi mdi-account-tie-outline me-1 text-primary"></i>
                        Profissional
                    </label>
                    <select class="form-select @error('selectedEmployee') is-invalid @enderror"
                            wire:model.live="selectedEmployee"
                            @disabled(!$selectedService)>
                        @if(!$selectedService)
                            <option value="">Selecione um serviço primeiro</option>
                        @elseif(empty($employees))
                            <option value="">Nenhum profissional disponível</option>
                        @else
                            <option value="">Selecione um profissional...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('selectedEmployee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Data --}}
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="mdi mdi-calendar-outline me-1 text-primary"></i>
                        Data
                    </label>
                    <input type="date"
                           class="form-control @error('selectedDay') is-invalid @enderror"
                           wire:model.live="selectedDay"
                           min="{{ $minDate }}"
                           @disabled(!$selectedEmployee)>
                    @error('selectedDay')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Loading de disponibilidade --}}
                <div class="col-12" wire:loading wire:target="updatedSelectedDay,updatedSelectedEmployee,updatedSelectedService">
                    <div class="d-flex align-items-center gap-2 text-primary">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <small>Verificando disponibilidade...</small>
                    </div>
                </div>

                {{-- Horários disponíveis --}}
                @if($selectedEmployee && $selectedDay)
                    <div class="col-12" wire:loading.remove wire:target="updatedSelectedDay,updatedSelectedEmployee,updatedSelectedService">
                        <label class="form-label">
                            <i class="mdi mdi-clock-outline me-1 text-primary"></i>
                            Horários Disponíveis
                            @if(!empty($scheduleEmployeeAvailable))
                                <span class="badge bg-label-success ms-1">{{ count($scheduleEmployeeAvailable) }} disponíveis</span>
                            @endif
                        </label>

                        @error('selectedHour')
                            <div class="alert alert-danger py-2 mb-3">
                                <i class="mdi mdi-alert-circle-outline me-1"></i> {{ $message }}
                            </div>
                        @enderror

                        @if(!empty($scheduleEmployeeAvailable))
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($scheduleEmployeeAvailable as $hour)
                                    <button type="button"
                                            wire:click="selectHour('{{ $hour }}')"
                                            wire:key="hour-{{ $hour }}"
                                            class="btn btn-sm {{ $selectedHour === $hour ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }} d-flex align-items-center gap-1">
                                        <i class="mdi mdi-clock-time-four-outline"></i>
                                        {{ $hour }}
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning d-flex align-items-center py-2 mb-0">
                                <i class="mdi mdi-calendar-remove-outline me-2 fs-5"></i>
                                <span>Nenhum horário disponível para esta data. Tente outra data ou profissional.</span>
                            </div>
                        @endif
                    </div>
                @endif

            </div>

            {{-- Resumo e confirmação --}}
            @if($selectedHour)
                <div class="mt-4 p-4 border rounded-3 bg-light">
                    <h6 class="mb-3 d-flex align-items-center gap-2">
                        <i class="mdi mdi-clipboard-check-outline text-success fs-5"></i>
                        Resumo do Agendamento
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <p class="mb-1 text-muted small">Serviço</p>
                            <p class="mb-0 fw-semibold">
                                {{ collect($services)->firstWhere('id', $selectedService)?->name ?? '—' }}
                            </p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="mb-1 text-muted small">Profissional</p>
                            <p class="mb-0 fw-semibold">
                                {{ collect($employees)->firstWhere('id', (int) $selectedEmployee)?->name ?? '—' }}
                            </p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="mb-1 text-muted small">Data</p>
                            <p class="mb-0 fw-semibold">
                                {{ \Carbon\Carbon::parse($selectedDay)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="mb-1 text-muted small">Horário</p>
                            <p class="mb-0 fw-semibold">{{ $selectedHour }}</p>
                        </div>
                    </div>
                    <button type="button"
                            class="btn btn-success w-100"
                            wire:click="createSchedule"
                            wire:loading.attr="disabled"
                            wire:target="createSchedule">
                        <span wire:loading.remove wire:target="createSchedule">
                            <i class="mdi mdi-check me-1"></i> Confirmar Agendamento
                        </span>
                        <span wire:loading wire:target="createSchedule">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Salvando...
                        </span>
                    </button>
                </div>
            @endif

        </div>
    </div>

    {{-- Meus Agendamentos --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between pb-3">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-3 bg-label-info rounded">
                    <i class="mdi mdi-calendar-month mdi-24px"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">Meus Agendamentos</h5>
                    <small class="text-muted">Próximos atendimentos</small>
                </div>
            </div>
            @if(!empty($upcomingSchedules))
                <span class="badge bg-primary rounded-pill">{{ count($upcomingSchedules) }}</span>
            @endif
        </div>

        <div class="card-body pt-0">

            @if(empty($upcomingSchedules))
                <div class="text-center py-5">
                    <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size: 3.5rem; opacity: .4;"></i>
                    <p class="text-muted mt-2 mb-0">Você não possui agendamentos futuros.</p>
                    <small class="text-muted">Use o formulário acima para agendar um atendimento.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Serviço</th>
                                <th>Profissional</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingSchedules as $s)
                                <tr wire:key="upcoming-{{ $s->id }}">
                                    <td>
                                        <span class="badge bg-label-primary">{{ $s->service_name }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-xs bg-label-secondary rounded-circle">
                                                <i class="mdi mdi-account-outline"></i>
                                            </div>
                                            {{ $s->employee_name }}
                                        </div>
                                    </td>
                                    <td>
                                        <i class="mdi mdi-calendar-outline me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($s->day)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <i class="mdi mdi-clock-outline me-1 text-muted"></i>
                                        {{ substr($s->hour, 0, 5) }}
                                    </td>
                                    <td class="text-end">
                                        @if($confirmingCancelId === $s->id)
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <small class="text-danger fw-semibold">Confirmar cancelamento?</small>
                                                <button class="btn btn-danger btn-sm"
                                                        wire:click="cancelSchedule({{ $s->id }})"
                                                        wire:loading.attr="disabled">
                                                    <i class="mdi mdi-check me-1"></i>Sim
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm"
                                                        wire:click="dismissCancel">
                                                    Não
                                                </button>
                                            </div>
                                        @else
                                            <button class="btn btn-outline-danger btn-sm"
                                                    wire:click="confirmCancel({{ $s->id }})">
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

            {{-- Histórico recente --}}
            @if(!empty($pastSchedules))
                <div class="mt-4 pt-3 border-top">
                    <p class="text-muted small mb-2 d-flex align-items-center gap-1">
                        <i class="mdi mdi-history"></i>
                        Histórico recente
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <tbody>
                                @foreach($pastSchedules as $s)
                                    <tr wire:key="past-{{ $s->id }}" class="text-muted">
                                        <td><span class="badge bg-label-secondary">{{ $s->service_name }}</span></td>
                                        <td>{{ $s->employee_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($s->day)->format('d/m/Y') }}</td>
                                        <td>{{ substr($s->hour, 0, 5) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
