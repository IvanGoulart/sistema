<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\services\Services;
use App\Interfaces\Schedule\ScheduleRepositoryInterface;

class FormCreateAgenda extends Component
{
  public $selectedService; // ID do serviço selecionado
  public $services = []; // Lista de serviços
  public $employees = []; // Lista de usuários relacionados ao serviço
  public $selectedEmployee; // ID do usuário selecionado
  public $selectedHour; // Hora selecionada
  public $selectedDay; // Dia selecionado

  //private $scheduleRepository;

  public function mount()
  {
    $this->services = Services::all();
  }
  // Atualiza a lista de usuários com base no serviço selecionado
  public function updateUsers()
  {
    if ($this->selectedService) {
      $service = Services::with('users')->find($this->selectedService);

      $this->employees = $service ? $service->users : [];
    } else {
      $this->employees = [];
    }
  }

  // Salva a agenda
  public function createSchedule(ScheduleRepositoryInterface $scheduleRepository)
  {
    $validated = $this->validate([
      'selectedService' => 'required|exists:services,id',
      'selectedEmployee' => 'required|exists:users,id',
    ]);

    // Verifica se o repositório foi injetado corretamente
    if (is_null($scheduleRepository)) {
      throw new \Exception('ScheduleRepository não foi injetado corretamente.');
    }

    //dd($this->selectedService);
    $scheduleData = [
      'service_id' => $this->selectedService,
      'employee_id' => $this->selectedEmployee,
      'client_id' => auth()->user()->id,
      'day' => $this->selectedDay,
      'hour' => $this->selectedHour,
      'cancel' => false,
    ];

    $scheduleRepository->createSchedule($scheduleData);

    session()->flash('message', 'Agenda salva com sucesso!');
    $this->reset(['selectedService', 'selectedEmployee']);
  }

  // Renderiza a view
  public function render()
  {
    return view('livewire.form-create-agenda');
  }
}
