<?php

namespace App\Listeners;

use App\Events\ScheduleCreated;
use App\Mail\ScheduleConfirmation;
use Illuminate\Support\Facades\Mail;

class SendScheduleConfirmationEmail
{
    public function handle(ScheduleCreated $event): void
    {
        try {
            $schedule = $event->schedule->load('service', 'employee', 'client');
            Mail::to($schedule->client->email)->queue(new ScheduleConfirmation($schedule));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de confirmação de agendamento: ' . $e->getMessage());
        }
    }
}
