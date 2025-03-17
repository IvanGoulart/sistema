<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\services\Services; // Ajustado para namespace padrão
use App\Models\User;

class FormCreateAgenda extends Component
{
  public $selectedService = '';   // ID do serviço selecionado
  public $services = [];          // Lista de serviços
  public $users = [];             // Lista de usuários relacionados ao serviço

  // Inicializa o componente
  public function mount()
  {
    // Carrega todos os serviços ao iniciar
    $this->services = Services::all();
  }

  // Atualiza a lista de usuários com base no serviço selecionado
  public function updateUsers()
  {
    if ($this->selectedService) {
      // Carrega o serviço selecionado com os usuários relacionados
      $service = Services::with('users')->find($this->selectedService);
      $this->users = $service ? $service->users : [];
    } else {
      $this->users = []; // Sem serviço selecionado, usuários vazios
    }
  }

  // Renderiza a view
  public function render()
  {
    return view('livewire.form-create-agenda');
  }
}
