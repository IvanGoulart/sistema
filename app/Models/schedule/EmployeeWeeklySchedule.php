<?php

namespace App\Models\schedule;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EmployeeWeeklySchedule extends Model
{
    protected $table = 'employee_weekly_schedules';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
