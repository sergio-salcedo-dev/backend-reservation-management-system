<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EmailSenderInterface;
use App\Mail\AppointmentConfirmationEmail;
use App\Models\Appointment;
use Illuminate\Contracts\Mail\Mailer;

class EmailSender implements EmailSenderInterface
{
    public function __construct(private readonly Mailer $mailer)
    {
    }

    public function sendAppointmentConfirmationEmail(Appointment $appointment): void
    {
        $email = new AppointmentConfirmationEmail($appointment);

        $this->mailer->to($appointment->patient->email)->send($email);
    }
}
