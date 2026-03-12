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
  public function index()
  {
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('content.schedule.schedule-create');
  }

  public function clientCreate()
  {
    return view('schedule.client-create-1');
  }
}
