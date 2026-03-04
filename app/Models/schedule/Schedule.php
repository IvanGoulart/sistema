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
    'hour',
    'cancel'
  ];

  public function employee()
  {
    return $this->belongsTo(User::class, 'employee_id');
  }

  public function client()
  {
    return $this->belongsTo(User::class, 'client_id');
  }

  public function service()
  {
    return $this->belongsTo(\App\Models\services\Services::class, 'service_id');
  }
}
