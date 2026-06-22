<?php

namespace App\Listeners;

use App\Events\ScheduleCancelled;
use App\Mail\ScheduleCancellation;
use Illuminate\Support\Facades\Mail;

class SendScheduleCancellationEmail
{
    public function handle(ScheduleCancelled $event): void
    {
        try {
            $schedule = $event->schedule->load('service', 'employee', 'client');
            Mail::to($schedule->client->email)->queue(new ScheduleCancellation($schedule));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de cancelamento de agendamento: ' . $e->getMessage());
        }
    }
}
