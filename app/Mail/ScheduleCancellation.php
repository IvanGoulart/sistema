<?php

namespace App\Mail;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduleCancellation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Schedule $schedule)
    {
    }

    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Agendamento Cancelado - Salão Fácil',
        );
    }

    public function content()
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.schedule-cancellation',
            with: [
                'schedule' => $this->schedule,
                'serviceName' => $this->schedule->service->name,
                'employeeName' => $this->schedule->employee->name,
                'day' => \Carbon\Carbon::parse($this->schedule->day)->format('d/m/Y'),
                'hour' => $this->schedule->hour,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}
