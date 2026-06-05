<?php

namespace App\Livewire\Reports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AgendaReport extends Component
{
    public string $dateStart = '';
    public string $dateEnd   = '';
    public string $employeeId = '';
    public string $serviceId  = '';

    public bool $generated = false;

    public $appointments;
    public int $total      = 0;
    public int $done       = 0;
    public int $cancelled  = 0;
    public float $cancelRate = 0;

    public function mount(): void
    {
        $this->dateStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateEnd   = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function generate(): void
    {
        $this->validate([
            'dateStart' => 'required|date',
            'dateEnd'   => 'required|date|after_or_equal:dateStart',
        ], [
            'dateStart.required'        => 'Informe a data inicial.',
            'dateEnd.required'          => 'Informe a data final.',
            'dateEnd.after_or_equal'    => 'A data final deve ser igual ou posterior à inicial.',
        ]);

        $tenantId = session('tenant_id') ?? 1;

        $query = DB::table('schedules')
            ->join('users as client', 'schedules.client_id', '=', 'client.id')
            ->join('users as emp',    'schedules.employee_id', '=', 'emp.id')
            ->join('services',        'schedules.service_id', '=', 'services.id')
            ->where('services.tenant_id', $tenantId)
            ->whereBetween('schedules.day', [$this->dateStart, $this->dateEnd])
            ->select(
                'schedules.id',
                'schedules.day',
                'schedules.hour',
                'schedules.cancel',
                'client.name as client_name',
                'emp.name as employee_name',
                'services.name as service_name'
            )
            ->orderBy('schedules.day')
            ->orderBy('schedules.hour');

        if ($this->employeeId !== '') {
            $query->where('schedules.employee_id', $this->employeeId);
        }

        if ($this->serviceId !== '') {
            $query->where('schedules.service_id', $this->serviceId);
        }

        $this->appointments = $query->get();
        $this->total        = $this->appointments->count();
        $this->cancelled    = $this->appointments->where('cancel', true)->count();
        $this->done         = $this->total - $this->cancelled;
        $this->cancelRate   = $this->total > 0
            ? round(($this->cancelled / $this->total) * 100, 1)
            : 0;

        $this->generated = true;
    }

    public function render()
    {
        $tenantId  = session('tenant_id') ?? 1;

        $employees = DB::table('users')
            ->join('user_permissions', 'users.id', '=', 'user_permissions.user_id')
            ->join('permissions', 'user_permissions.code_permission', '=', 'permissions.id')
            ->where('user_permissions.tenant_id', $tenantId)
            ->where('permissions.name', 'employee')
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();

        $services = DB::table('services')
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('livewire.reports.agenda-report', compact('employees', 'services'));
    }
}
