<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $fillable = ['service_id', 'employee_id', 'client_id', 'day', 'hour', 'cancel'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
