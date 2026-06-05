<div>

    {{-- Flash --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Cabeçalho --}}
    <div class="mb-4">
        <h4 class="mb-1">Disponibilidade dos Profissionais</h4>
        <p class="text-muted mb-0">Defina os dias e horários de trabalho de cada profissional</p>
    </div>

    {{-- Seleção de profissional --}}
    <div class="card mb-4">
        <div class="card-body">
            <label class="form-label">
                <i class="mdi mdi-account-tie-outline me-1 text-primary"></i>
                Selecione o Profissional
            </label>
            <select class="form-select @error('selectedEmployeeId') is-invalid @enderror"
                    wire:model.live="selectedEmployeeId"
                    style="max-width: 400px;">
                <option value="">Selecione um profissional...</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
            @error('selectedEmployeeId')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            @if($employees->isEmpty())
                <small class="text-warning d-flex align-items-center gap-1 mt-2">
                    <i class="mdi mdi-alert-outline"></i>
                    Nenhum profissional encontrado. Cadastre usuários primeiro.
                </small>
            @endif
        </div>
    </div>

    {{-- Grade semanal --}}
    @if($selectedEmployeeId)
        <div wire:loading wire:target="updatedSelectedEmployeeId" class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2">Carregando disponibilidade...</p>
        </div>

        <div wire:loading.remove wire:target="updatedSelectedEmployeeId">
            <div class="card">
                <div class="card-header d-flex align-items-center pb-3">
                    <div class="avatar avatar-sm me-3 bg-label-primary rounded">
                        <i class="mdi mdi-calendar-week mdi-24px"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Grade Semanal</h5>
                        <small class="text-muted">
                            Ative os dias e defina o horário de início e fim de cada turno
                        </small>
                    </div>
                </div>

                <div class="card-body pt-2">
                    <div class="row g-3">
                        @foreach($schedule as $dow => $day)
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 p-3 rounded border
                                            {{ $day['enabled'] ? 'border-primary bg-light' : 'border-light' }}"
                                     style="transition: all .2s;">

                                    {{-- Toggle do dia --}}
                                    <div class="form-check form-switch mb-0 flex-shrink-0" style="min-width: 130px;">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="day-{{ $dow }}"
                                               wire:model.live="schedule.{{ $dow }}.enabled">
                                        <label class="form-check-label fw-semibold {{ $day['enabled'] ? 'text-primary' : 'text-muted' }}"
                                               for="day-{{ $dow }}">
                                            {{ $day['label'] }}
                                        </label>
                                    </div>

                                    {{-- Horários (só visíveis se ativado) --}}
                                    @if($day['enabled'])
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="text-muted small mb-0 flex-shrink-0">De</label>
                                                <input type="time"
                                                       class="form-control form-control-sm"
                                                       wire:model.live="schedule.{{ $dow }}.start"
                                                       style="width: 120px;">
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="text-muted small mb-0 flex-shrink-0">até</label>
                                                <input type="time"
                                                       class="form-control form-control-sm"
                                                       wire:model.live="schedule.{{ $dow }}.end"
                                                       style="width: 120px;">
                                            </div>
                                            @if($day['start'] && $day['end'] && $day['start'] < $day['end'])
                                                @php
                                                    [$sh, $sm] = explode(':', $day['start']);
                                                    [$eh, $em] = explode(':', $day['end']);
                                                    $total = ($eh * 60 + $em) - ($sh * 60 + $sm);
                                                    $horas = intdiv($total, 60);
                                                    $mins  = $total % 60;
                                                @endphp
                                                <span class="badge bg-label-success ms-1">
                                                    {{ $horas > 0 ? "{$horas}h" : '' }}{{ $mins > 0 ? "{$mins}min" : '' }} de trabalho
                                                </span>
                                            @elseif($day['start'] && $day['end'] && $day['start'] >= $day['end'])
                                                <span class="badge bg-label-danger ms-1">
                                                    <i class="mdi mdi-alert-outline me-1"></i>Horário inválido
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">
                                            <i class="mdi mdi-minus-circle-outline me-1"></i>Não trabalha este dia
                                        </span>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Resumo rápido --}}
                    @php
                        $activeDays = collect($schedule)->filter(fn($d) => $d['enabled']);
                    @endphp
                    @if($activeDays->count() > 0)
                        <div class="mt-4 p-3 rounded bg-light border">
                            <p class="mb-1 text-muted small fw-semibold">
                                <i class="mdi mdi-information-outline me-1"></i>Resumo da disponibilidade
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($activeDays as $dow => $day)
                                    <span class="badge bg-label-primary">
                                        {{ substr($day['label'], 0, 3) }}
                                        {{ $day['start'] }}–{{ $day['end'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Botão salvar --}}
                    <div class="mt-4">
                        <button type="button"
                                class="btn btn-primary"
                                wire:click="save"
                                wire:loading.attr="disabled"
                                wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                <i class="mdi mdi-content-save me-1"></i> Salvar Disponibilidade
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1"></span> Salvando...
                            </span>
                        </button>
                        <small class="text-muted ms-3">
                            <i class="mdi mdi-information-outline me-1"></i>
                            As alterações se aplicam a todas as semanas automaticamente.
                        </small>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
