<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Appointment;

interface EmailSenderInterface
{
    public function sendAppointmentConfirmationEmail(Appointment $appointment): void;
}
