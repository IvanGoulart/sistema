<?php

namespace App\Repositories;

use App\Interfaces\Schedule\ScheduleRepositoryInterface;
use App\Models\schedule\Schedule;
use RuntimeException;

class ScheduleRepository implements ScheduleRepositoryInterface
{
  public function createSchedule(array $scheduleData)
  {
    $tenantId = session('tenant_id') ?? 1;
    if (! $tenantId) {
      throw new RuntimeException('Tenant ativo não encontrado na sessão.');
    }

    $schedule = new Schedule();
    $schedule->tenant_id   = $tenantId;
    $schedule->service_id  = $scheduleData['service_id'];
    $schedule->employee_id = $scheduleData['employee_id'];
    $schedule->client_id   = $scheduleData['client_id'];
    $schedule->day         = $scheduleData['day'];
    $schedule->hour        = $scheduleData['hour'];

    $schedule->save();
  }

  public function getSchedules()
  {
    $tenantId = session('tenant_id') ?? 1;

    if (!$tenantId) {
      throw new RuntimeException('Tenant ativo não encontrado na sessão.');
    }

    return Schedule::where('tenant_id', $tenantId)->get();
  }

  public function getScheduleEmployeeAvailableById(int $id)
  {
    $tenantId = session('tenant_id');

    if (! $tenantId) {
      throw new RuntimeException('Tenant ativo não encontrado na sessão.');
    }

    return Schedule::where('tenant_id', $tenantId)
      ->where('employee_id', $id)
      ->get();
  }
}
