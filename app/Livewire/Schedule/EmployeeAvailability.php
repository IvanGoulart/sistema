<?php

namespace App\Livewire\Schedule;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\schedule\EmployeeWeeklySchedule;

class EmployeeAvailability extends Component
{
    public $employees = [];
    public ?int $selectedEmployeeId = null;

    // Profissional (employee sem admin) só edita a própria disponibilidade.
    public bool $lockedToSelf = false;

    public array $schedule = [];

    private const DAYS = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado',
    ];

    public function mount(): void
    {
        $this->resetSchedule();

        $user = auth()->user();
        if ($user && ! $user->isAdmin() && $user->isProfessional()) {
            // Profissional: trava no próprio cadastro e já carrega a agenda dele.
            $this->lockedToSelf = true;
            $this->selectedEmployeeId = $user->id;
            $this->updatedSelectedEmployeeId();

            return;
        }

        $this->loadEmployees();
    }

    private function tenantId(): int
    {
        return session('tenant_id') ?? 1;
    }

    public function loadEmployees(): void
    {
        $this->employees = DB::table('users')
            ->join('user_permissions', 'users.id', '=', 'user_permissions.user_id')
            ->where('user_permissions.tenant_id', $this->tenantId())
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();
    }

    private function resetSchedule(): void
    {
        $this->schedule = [];
        foreach (self::DAYS as $dow => $label) {
            $this->schedule[$dow] = [
                'label'   => $label,
                'enabled' => false,
                'start'   => '08:00',
                'end'     => '18:00',
            ];
        }
    }

    public function updatedSelectedEmployeeId(): void
    {
        $this->resetSchedule();

        if (!$this->selectedEmployeeId) {
            return;
        }

        $rows = EmployeeWeeklySchedule::where('tenant_id', $this->tenantId())
            ->where('employee_id', $this->selectedEmployeeId)
            ->get();

        foreach ($rows as $row) {
            $this->schedule[$row->day_of_week]['enabled'] = true;
            $this->schedule[$row->day_of_week]['start']   = substr($row->start_time, 0, 5);
            $this->schedule[$row->day_of_week]['end']     = substr($row->end_time, 0, 5);
        }
    }

    public function save(): void
    {
        // Profissional só salva a própria disponibilidade.
        if ($this->lockedToSelf) {
            $this->selectedEmployeeId = auth()->id();
        }

        if (!$this->selectedEmployeeId) {
            $this->addError('selectedEmployeeId', 'Selecione um profissional.');
            return;
        }

        $errors = [];
        foreach ($this->schedule as $dow => $day) {
            if (!$day['enabled']) {
                continue;
            }
            if ($day['start'] >= $day['end']) {
                $errors[] = "Em {$day['label']}: o horário de início deve ser anterior ao horário de fim.";
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addError("schedule.{$this->selectedEmployeeId}", $error);
            }
            session()->flash('error', implode(' | ', $errors));
            return;
        }

        $tenantId    = $this->tenantId();
        $employeeId  = $this->selectedEmployeeId;

        EmployeeWeeklySchedule::where('tenant_id', $tenantId)
            ->where('employee_id', $employeeId)
            ->delete();

        foreach ($this->schedule as $dow => $day) {
            if (!$day['enabled']) {
                continue;
            }

            EmployeeWeeklySchedule::create([
                'tenant_id'   => $tenantId,
                'employee_id' => $employeeId,
                'day_of_week' => $dow,
                'start_time'  => $day['start'],
                'end_time'    => $day['end'],
            ]);
        }

        session()->flash('message', 'Disponibilidade salva com sucesso!');
    }

    public function render()
    {
        return view('livewire.schedule.employee-availability');
    }
}
