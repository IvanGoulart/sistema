<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\services\Services;
use App\Models\schedule\EmployeeWeeklySchedule;
use App\Interfaces\Schedule\ScheduleRepositoryInterface;
use App\Events\ScheduleCreated;
use App\Models\Schedule;

class FormCreateAgenda extends Component
{
    public $selectedService = null;
    public $services;
    public $employees = [];
    public $selectedEmployee = null;
    public $selectedHour = null;
    public $selectedDay = null;
    public $scheduleEmployeeAvailable = [];
    public array $upcomingSchedules = [];
    public array $pastSchedules = [];
    public string $minDate;
    public ?int $confirmingCancelId = null;

    public function mount(): void
    {
        $this->minDate = now()->format('Y-m-d');
        $this->services = Services::all();
        $this->loadMySchedules();
    }

    public function updatedSelectedService(): void
    {
        $this->selectedEmployee = null;
        $this->selectedDay = null;
        $this->selectedHour = null;
        $this->employees = [];
        $this->scheduleEmployeeAvailable = [];

        if ($this->selectedService) {
            $service = Services::with('users')->find($this->selectedService);
            $this->employees = $service ? $service->users->all() : [];
        }
    }

    public function updatedSelectedEmployee(): void
    {
        $this->selectedHour = null;
        $this->listEmployeeAvailable();
    }

    public function updatedSelectedDay(): void
    {
        if ($this->selectedDay && $this->selectedDay < $this->minDate) {
            $this->selectedDay = $this->minDate;
        }
        $this->selectedHour = null;
        $this->listEmployeeAvailable();
    }

    public function listEmployeeAvailable(): void
    {
        $this->scheduleEmployeeAvailable = [];
        $this->selectedHour = null;

        if (!$this->selectedEmployee || !$this->selectedDay) {
            return;
        }

        $dayOfWeek = Carbon::parse($this->selectedDay)->dayOfWeek;

        $windows = EmployeeWeeklySchedule::where('tenant_id', session('tenant_id') ?? 1)
            ->where('employee_id', $this->selectedEmployee)
            ->where('day_of_week', $dayOfWeek)
            ->get(['start_time', 'end_time']);

        if ($windows->isEmpty()) {
            return;
        }

        $booked = DB::table('schedules')
            ->where('employee_id', $this->selectedEmployee)
            ->where('day', $this->selectedDay)
            ->where('cancel', false)
            ->pluck('hour')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $free = [];
        foreach ($windows as $w) {
            $start = Carbon::parse($w->start_time);
            $end   = Carbon::parse($w->end_time);
            for ($t = $start->copy(); $t->lt($end); $t->addMinutes(30)) {
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

    public function selectHour(string $hour): void
    {
        $this->selectedHour = $hour;
    }

    public function createSchedule(ScheduleRepositoryInterface $scheduleRepository): void
    {
        $this->validate([
            'selectedService'  => 'required|exists:services,id',
            'selectedEmployee' => 'required|exists:users,id',
            'selectedDay'      => 'required|date|after_or_equal:today',
            'selectedHour'     => 'required',
        ], [
            'selectedService.required'   => 'Selecione um serviço.',
            'selectedEmployee.required'  => 'Selecione um profissional.',
            'selectedDay.required'       => 'Selecione uma data.',
            'selectedDay.after_or_equal' => 'Não é possível agendar para datas passadas.',
            'selectedHour.required'      => 'Selecione um horário.',
        ]);

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
            'service_id'  => $this->selectedService,
            'employee_id' => $this->selectedEmployee,
            'client_id'   => auth()->id(),
            'day'         => $this->selectedDay,
            'hour'        => $this->selectedHour,
            'cancel'      => false,
        ];

        $scheduleRepository->createSchedule($scheduleData);

        $schedule = Schedule::where('client_id', auth()->id())
            ->where('service_id', $this->selectedService)
            ->where('employee_id', $this->selectedEmployee)
            ->where('day', $this->selectedDay)
            ->where('hour', $this->selectedHour)
            ->latest()
            ->first();

        if ($schedule) {
            ScheduleCreated::dispatch($schedule);
        }

        session()->flash('message', 'Agendamento realizado com sucesso!');

        $this->selectedService = null;
        $this->selectedEmployee = null;
        $this->selectedDay = null;
        $this->selectedHour = null;
        $this->employees = [];
        $this->scheduleEmployeeAvailable = [];

        $this->loadMySchedules();
    }

    public function loadMySchedules(): void
    {
        if (!auth()->check()) {
            $this->upcomingSchedules = [];
            $this->pastSchedules = [];
            return;
        }

        $today = now()->format('Y-m-d');

        $base = DB::table('schedules')
            ->join('services', 'services.id', '=', 'schedules.service_id')
            ->join('users as employees', 'employees.id', '=', 'schedules.employee_id')
            ->where('schedules.client_id', auth()->id())
            ->where('schedules.cancel', false)
            ->select(
                'schedules.id',
                'schedules.day',
                'schedules.hour',
                'services.name as service_name',
                'employees.name as employee_name'
            );

        $this->upcomingSchedules = (clone $base)
            ->where('schedules.day', '>=', $today)
            ->orderBy('schedules.day')
            ->orderBy('schedules.hour')
            ->get()
            ->toArray();

        $this->pastSchedules = (clone $base)
            ->where('schedules.day', '<', $today)
            ->orderByDesc('schedules.day')
            ->orderByDesc('schedules.hour')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function confirmCancel(int $id): void
    {
        $this->confirmingCancelId = $id;
    }

    public function dismissCancel(): void
    {
        $this->confirmingCancelId = null;
    }

    public function cancelSchedule(int $scheduleId): void
    {
        $schedule = Schedule::find($scheduleId);

        DB::table('schedules')
            ->where('id', $scheduleId)
            ->where('client_id', auth()->id())
            ->update(['cancel' => true, 'updated_at' => now()]);

        if ($schedule) {
            \App\Events\ScheduleCancelled::dispatch($schedule);
        }

        session()->flash('message', 'Agendamento cancelado com sucesso.');
        $this->confirmingCancelId = null;
        $this->loadMySchedules();
        $this->listEmployeeAvailable();
    }

    public function render()
    {
        return view('livewire.form-create-agenda');
    }
}
