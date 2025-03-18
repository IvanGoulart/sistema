<?php

namespace App\Repositories;

use App\Interfaces\Schedule\ScheduleRepositoryInterface;
use App\Models\schedule\Schedule;

class ScheduleRepository implements ScheduleRepositoryInterface
{

  public function createSchedule(array $scheduleData)
  {
    dd($scheduleData);
  }

  public function getSchedules()
  {
    return Schedule::all();
  }
}
