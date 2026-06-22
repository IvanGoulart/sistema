<?php

namespace App\Events;

use App\Models\Schedule;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduleCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Schedule $schedule)
    {
    }
}
