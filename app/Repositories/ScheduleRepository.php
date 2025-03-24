<?php

namespace App\Repositories;

use App\Interfaces\Schedule\ScheduleRepositoryInterface;
use App\Models\schedule\Schedule;

class ScheduleRepository implements ScheduleRepositoryInterface
{

  public function createSchedule(array $scheduleData)
  {
    $schedule = new Schedule();
    $schedule->service_id = $scheduleData['service_id'];
    $schedule->employee_id = $scheduleData['employee_id'];
    $schedule->client_id = $scheduleData['client_id'];
    $schedule->day = $scheduleData['day'];
    $schedule->hour = $scheduleData['hour'];

    $schedule->save();
  }

  public function getSchedules()
  {
    return Schedule::all();
  }
}