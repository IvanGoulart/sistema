<?php

namespace App\Livewire\Schedule;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminAgenda extends Component
{
    public string $weekStart;

    public ?int $filterEmployeeId = null;

    public $employees = [];

    public array $days = [];

    public array $appointments = [];

    public ?int $confirmingCancelId = null;

    public function mount(): void
    {
        $this->weekStart = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $this->loadEmployees();
        $this->loadAppointments();
    }

    private function tenantId(): int
    {
        return session('tenant_id') ?? 1;
    }

    public function previousWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->format('Y-m-d');
        $this->confirmingCancelId = null;
        $this->loadAppointments();
    }

    public function nextWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->format('Y-m-d');
        $this->confirmingCancelId = null;
        $this->loadAppointments();
    }

    public function thisWeek(): void
    {
        $this->weekStart = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $this->confirmingCancelId = null;
        $this->loadAppointments();
    }

    public function updatedFilterEmployeeId(): void
    {
        $this->confirmingCancelId = null;
        $this->loadAppointments();
    }

    public function loadEmployees(): void
    {
        $this->employees = DB::table('users')
            ->join('user_permissions', 'users.id', '=', 'user_permissions.user_id')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
            ->where('user_permissions.tenant_id', $this->tenantId())
            ->where('permissions.name', 'employee')
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();
    }

    public function loadAppointments(): void
    {
        $start = Carbon::parse($this->weekStart);
        $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

        // Monta os 7 dias da semana
        $this->days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $this->days[] = [
                'date' => $date->format('Y-m-d'),
                'label' => ucfirst($date->locale('pt_BR')->isoFormat('dddd')),
                'short' => $date->format('d/m'),
                'isToday' => $date->isToday(),
                'isPast' => $date->isPast() && ! $date->isToday(),
            ];
        }

        $query = DB::table('schedules')
            ->join('services', 'services.id', '=', 'schedules.service_id')
            ->join('users as employees', 'employees.id', '=', 'schedules.employee_id')
            ->join('users as clients', 'clients.id', '=', 'schedules.client_id')
            ->where('schedules.tenant_id', $this->tenantId())
            ->where('schedules.cancel', false)
            ->whereBetween('schedules.day', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->select(
                'schedules.id',
                'schedules.day',
                'schedules.hour',
                'services.name as service_name',
                'employees.name as employee_name',
                'clients.name as client_name',
            )
            ->orderBy('schedules.day')
            ->orderBy('schedules.hour');

        if ($this->filterEmployeeId) {
            $query->where('schedules.employee_id', $this->filterEmployeeId);
        }

        $grouped = $query->get()->groupBy('day');

        $this->appointments = [];
        foreach ($grouped as $date => $items) {
            $this->appointments[$date] = $items->toArray();
        }
    }

    public function confirmCancel(int $id): void
    {
        $this->confirmingCancelId = $id;
    }

    public function dismissCancel(): void
    {
        $this->confirmingCancelId = null;
    }

    public function cancelAppointment(int $id): void
    {
        DB::table('schedules')
            ->where('id', $id)
            ->where('tenant_id', $this->tenantId())
            ->update(['cancel' => true, 'updated_at' => now()]);

        session()->flash('message', 'Agendamento cancelado com sucesso.');
        $this->confirmingCancelId = null;
        $this->loadAppointments();
    }

    public function weekLabel(): string
    {
        $start = Carbon::parse($this->weekStart)->locale('pt_BR');
        $end = $start->copy()->addDays(6);

        if ($start->month === $end->month) {
            return $start->format('d').' – '.$end->format('d \d\e F \d\e Y');
        }

        return $start->format('d \d\e M').' – '.$end->format('d \d\e M \d\e Y');
    }

    public function totalForWeek(): int
    {
        return array_sum(array_map('count', $this->appointments));
    }

    public function render()
    {
        return view('livewire.schedule.admin-agenda');
    }
}
