<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\services\Services;
use App\Models\schedule\AvailableEmployeeSchedule;
use App\Interfaces\Schedule\ScheduleRepositoryInterface;

class FormCreateAgenda extends Component
{
  public $selectedService;
  public $services = [];
  public $employees = [];
  public $selectedEmployee;
  public $selectedHour;
  public $selectedDay;
  public $scheduleEmployeeAvailable = [];

  public function mount()
  {
    $this->services = Services::all();
  }

  public function updateUsers()
  {
    $this->selectedEmployee = null;
    $this->scheduleEmployeeAvailable = [];
    $this->selectedHour = null;
    $this->selectedDay = null;
    $this->employees = [];

    if ($this->selectedService) {
      $service = Services::with('users')->find($this->selectedService);
      $this->employees = $service ? $service->users : [];
    }
  }

  public function listEmployeeAvailable()
  {
    $this->scheduleEmployeeAvailable = [];
    $this->selectedHour = null;

    if (!$this->selectedEmployee || !$this->selectedDay) {
      return;
    }

    // 1) janelas disponíveis do dia
    $windows = AvailableEmployeeSchedule::where('employee_id', $this->selectedEmployee)
      ->where('date', $this->selectedDay)
      ->get(['start_time', 'end_time']);

    if ($windows->isEmpty()) {
      return;
    }

    // 2) horários já agendados (não cancelados)
    $booked = DB::table('schedules')
      ->where('employee_id', $this->selectedEmployee)
      ->where('day', $this->selectedDay)
      ->where('cancel', false)
      ->pluck('hour')
      ->map(fn($t) => Carbon::parse($t)->format('H:i'))
      ->toArray();

    // 3) gerar slots (ex.: 30 min)
    $slotMinutes = 30;
    $free = [];

    foreach ($windows as $w) {
      $start = Carbon::parse($w->start_time);
      $end   = Carbon::parse($w->end_time);

      for ($t = $start->copy(); $t->lt($end); $t->addMinutes($slotMinutes)) {
        $hhmm = $t->format('H:i');
        if (!in_array($hhmm, $booked, true)) {
          $free[] = $hhmm;
        }
      }
    }

    $free = array_values(array_unique($free));
    sort($free);

    $this->scheduleEmployeeAvailable = $free;
  }

  public function selectHour(string $hour)
  {
    $this->selectedHour = $hour;
  }

  public function createSchedule(ScheduleRepositoryInterface $scheduleRepository)
  {
    $this->validate([
      'selectedService' => 'required|exists:services,id',
      'selectedEmployee' => 'required|exists:users,id',
      'selectedDay' => 'required|date',
      'selectedHour' => 'required',
    ]);

    // garante no backend que ainda está livre
    $exists = DB::table('schedules')
      ->where('employee_id', $this->selectedEmployee)
      ->where('day', $this->selectedDay)
      ->where('hour', $this->selectedHour)
      ->where('cancel', false)
      ->exists();

    if ($exists) {
      $this->addError('selectedHour', 'Esse horário acabou de ser ocupado. Escolha outro.');
      $this->listEmployeeAvailable();
      return;
    }

    $scheduleData = [
      'service_id' => $this->selectedService,
      'employee_id' => $this->selectedEmployee,
      'client_id' => auth()->id(),
      'day' => $this->selectedDay,
      'hour' => $this->selectedHour,
      'cancel' => false,
    ];

    $scheduleRepository->createSchedule($scheduleData);

    session()->flash('message', 'Agenda salva com sucesso!');

    // recarrega para o horário sumir da lista
    $this->listEmployeeAvailable();

    // se quiser resetar tudo:
    // $this->reset(['selectedService', 'selectedEmployee', 'selectedDay', 'selectedHour', 'scheduleEmployeeAvailable']);
  }


  public function cancelSchedule($scheduleId)
  {
    DB::table('schedules')
      ->where('id', $scheduleId)
      ->update([
        'cancel' => true,
        'updated_at' => now()
      ]);

    session()->flash('message', 'Agendamento cancelado com sucesso.');

    $this->listEmployeeAvailable();
  }

  public function updatedSelectedService()
  {
    $this->updateUsers();          // recalcula employees
    $this->listEmployeeAvailable(); // recalcula horários (vai limpar se faltar dados)
  }

  public function updatedSelectedEmployee()
  {
    $this->listEmployeeAvailable();
  }

  public function updatedSelectedDay()
  {
    $this->listEmployeeAvailable();
  }

  public function render()
  {
    return view('livewire.form-create-agenda');
  }


}
