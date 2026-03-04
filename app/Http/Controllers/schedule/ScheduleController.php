<?php

namespace App\Http\Controllers\schedule;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\schedule\AvailableEmployeeSchedule;
use App\Models\Schedule;

class ScheduleController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index() {}

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('content.schedule.schedule-create');
  }

  public function getFreeHours(int $employeeId, string $date, int $slotMinutes = 30): array
  {
    // 1) janelas disponíveis do dia
    $windows = AvailableEmployeeSchedule::where('employee_id', $employeeId)
      ->where('date', $date)
      ->get(['start_time', 'end_time']);

    // 2) horários já agendados (não cancelados)
    $booked = Schedule::where('employee_id', $employeeId)
      ->where('day', $date)
      ->where('cancel', false)
      ->pluck('hour')
      ->map(fn($t) => Carbon::parse($t)->format('H:i'))
      ->toArray();

    $free = [];

    foreach ($windows as $w) {
      $start = Carbon::parse($w->start_time);
      $end   = Carbon::parse($w->end_time);

      // gera slots: ex 08:00, 08:30, 09:00...
      for ($t = $start->copy(); $t->lt($end); $t->addMinutes($slotMinutes)) {
        $hhmm = $t->format('H:i');

        if (!in_array($hhmm, $booked, true)) {
          $free[] = $hhmm;
        }
      }
    }

    // remove duplicados e ordena
    $free = array_values(array_unique($free));
    sort($free);

    return $free;
  }
}
