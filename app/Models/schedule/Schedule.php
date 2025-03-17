<?php

namespace App\Models\schedule;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Schedule extends Model
{
  protected $table = 'schedules'; // Nome da tabela

  protected $fillable = [
    'employee_id',
    'client_id',
    'service_id',
    'day',
    'cancel',
    'cancel',
  ];
}
