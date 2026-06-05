<?php

namespace App\Http\Controllers\schedule;

use App\Http\Controllers\Controller;

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

  public function adminAgenda()
  {
    return view('content.schedule.admin-agenda');
  }

  public function availability()
  {
    return view('content.schedule.employee-availability');
  }

  public function clientCreate()
  {
    return view('schedule.client-create-1');
  }
}
