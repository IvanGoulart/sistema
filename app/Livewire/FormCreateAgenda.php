<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\services\Services;
use App\Interfaces\Schedule\ScheduleRepositoryInterface;

class FormCreateAgenda extends Component
{
  public $selectedService;   // ID do serviço selecionado
  public $services = [];     // Lista de serviços
  public $users = [];        // Lista de usuários relacionados ao serviço
  public $selectedUser;      // ID do usuário selecionado

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
      $this->users = $service ? $service->users : [];
    } else {
      $this->users = [];
    }
  }

  // Salva a agenda
  public function createSchedule(ScheduleRepositoryInterface $scheduleRepository)
  {
    $validated = $this->validate([
      'selectedService' => 'required|exists:services,id',
      'selectedUser' => 'required|exists:users,id',
    ]);
    // Verifica se o repositório foi injetado corretamente
    if (is_null($scheduleRepository)) {
      throw new \Exception('ScheduleRepository não foi injetado corretamente.');
    }

    $scheduleData = [
      'service_id' => $this->selectedService,
      'user_id' => $this->selectedUser,
      // Adicione outros campos necessários aqui
    ];

    $scheduleRepository->createSchedule($scheduleData);

    session()->flash('message', 'Agenda salva com sucesso!');
    $this->reset(['selectedService', 'selectedUser']);
  }

  // Renderiza a view
  public function render()
  {
    return view('livewire.form-create-agenda');
  }
}
