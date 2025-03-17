<?php

namespace App\Repositories;

use App\Interfaces\Schedule\ScheduleRepositoryInterface;
use App\Models\schedule\Schedule;

class PermissionRepository implements ScheduleRepositoryInterface
{

  public function createSchedule(array $data)
  {
    return Schedule::create($data);
  }

  public function getSchedules()
  {
    return Schedule::all();
  }
}
