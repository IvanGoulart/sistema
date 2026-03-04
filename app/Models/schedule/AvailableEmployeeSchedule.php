<?php

namespace App\Models\schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AvailableEmployeeSchedule extends Model
{
  use HasFactory;

  protected $table = 'available_employee_schedules';

  protected $fillable = [
    'employee_id',
    'date',
    'start_time',
    'end_time'
  ];

  /**
   * Funcionário dono do horário
   */
  public function employee()
  {
    return $this->belongsTo(User::class, 'employee_id');
  }
}
