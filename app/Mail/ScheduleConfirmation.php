<?php

namespace App\Mail;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduleConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Schedule $schedule)
    {
    }

    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Agendamento Confirmado - Salão Fácil',
        );
    }

    public function content()
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.schedule-confirmation',
            with: [
                'schedule' => $this->schedule,
                'serviceName' => $this->schedule->service->name,
                'employeeName' => $this->schedule->employee->name,
                'day' => \Carbon\Carbon::parse($this->schedule->day)->format('d/m/Y'),
                'hour' => $this->schedule->hour,
                'price' => $this->schedule->service->price ? 'R$ ' . number_format($this->schedule->service->price, 2, ',', '.') : 'Não informado',
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}
