<?php

namespace App\Interfaces\Schedule;

interface ScheduleRepositoryInterface
{
  public function createSchedule(array $scheduleData);
  public function getSchedules();
}
