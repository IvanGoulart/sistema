<div class="container mt-4">

  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Criar Agendamento</h5>
    </div>

    <div class="card-body">

      {{-- Serviço --}}
      <div class="mb-3">
        <label for="service" class="form-label fw-bold">Selecione um serviço</label>
        <select id="service" class="form-select" wire:model.live="selectedService">
          <option value="">Selecione um serviço</option>
          @foreach($services as $service)
            <option value="{{ $service->id }}">{{ $service->name }}</option>
          @endforeach
        </select>

        @error('selectedService')
        <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      {{-- Profissional --}}
      <div class="mb-3">
        <label for="user" class="form-label fw-bold">Selecione um profissional</label>
        <select id="user" class="form-select" wire:model.live="selectedEmployee">

          @if($employees && count($employees) > 0)
            <option value="">Selecione</option>
            @foreach($employees as $employee)
              <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
          @else
            <option value="">Nenhum profissional disponível</option>
          @endif

        </select>
      </div>

      {{-- Data --}}
      <div class="mb-4">
        <label for="day" class="form-label fw-bold">Selecione uma data</label>
        <input
          type="date"
          id="day"
          class="form-control"
          wire:model.live="selectedDay"
        >
      </div>

      {{-- Horários --}}
      <div class="mb-4">
        <h5 class="mb-3">Horários Disponíveis</h5>

        <div class="row g-2">

          @if(!empty($scheduleEmployeeAvailable))

            @foreach($scheduleEmployeeAvailable as $hour)

              <div class="col-md-3 col-6">
                <button
                  type="button"
                  class="btn w-100
                  {{ $selectedHour == $hour ? 'btn-success' : 'btn-outline-primary' }}"
                  wire:click="selectHour('{{ $hour }}')">

                  {{ $hour }}

                </button>
              </div>

            @endforeach

          @else

            <div class="col-12">
              <div class="alert alert-warning">
                Nenhum horário disponível.
              </div>
            </div>

          @endif

        </div>
      </div>

      {{-- Horário selecionado --}}
      @if($selectedHour)
        <div class="alert alert-info">
          Horário selecionado: <strong>{{ $selectedHour }}</strong>
        </div>
      @endif

      {{-- Botão salvar --}}
      <div class="d-grid">
        <button
          type="button"
          class="btn btn-primary btn-lg"
          wire:click="createSchedule"
          @disabled(!$selectedHour)
        >
          Salvar Agendamento
        </button>
      </div>

    </div>
  </div>

</div>
