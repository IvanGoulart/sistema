<div class="container">
  @error('selectedService')
  <span class="text-danger">{{ $message }}</span>
  @enderror
  <div class="mb-3">
    <label for="service" class="form-label">Selecione um serviço:</label>
    <select id="service" class="form-select" wire:model="selectedService" wire:change="updateUsers">
      <option value="">Selecione um serviço</option>
      @foreach($services as $service)
      <option value="{{ $service->id }}">{{ $service->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label for="user" class="form-label">Selecione um profissional:</label>
    <select id="user" class="form-select" wire:model="selectedEmployee">
      @if($employees && count($employees) > 0)
      <option value="">Selecione</option>
      @foreach($employees as $employee)
      <option value="{{ $employee->id }}">{{ $employee->name }}</option>
      @endforeach
      @else
      <option value="">Nenhum usuário relacionado ao serviço selecionado.</option>
      @endif
    </select>
  </div>
  <div class="mb-3">
    <label for="day" class="form-label">Selecione uma data:</label>
    <input type="date" id="day" class="form-control" wire:model="selectedDay" wire:change="listEmployeeAvailable">
  </div>
  <div class="container my-5">
    <h2>Horários Disponíveis para Agendamento</h2>
    <ul class="list-group">
      @if(!empty($scheduleEmployeeAvailable) && count($scheduleEmployeeAvailable) > 0)
        @foreach($scheduleEmployeeAvailable as $hour)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ $hour }}
            <button type="button" class="btn btn-primary btn-sm" wire:click="selectHour('{{ $hour }}')">
              Selecionar
            </button>
          </li>
        @endforeach
      @else
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Nenhum horário disponível.
        </li>
      @endif
    </ul>
  </div>


  <div class="mb-3">
    <button type="button" class="btn btn-primary" wire:click="createSchedule">Salvar</button>
  </div>
</div>
