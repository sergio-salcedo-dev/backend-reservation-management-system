<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function build(): AppointmentConfirmationEmail
    {
        return $this->subject('Appointment Confirmation')
            ->view('appointments.confirmation')
            ->with([
                'patientFullName' => ucwords($this->appointment->patient->full_name),
                'date' => Carbon::parse($this->appointment->start_date)->format('Y-m-d'),
                'time' => Carbon::parse($this->appointment->start_date)->format('H:i'),
                'duration' => Carbon::parse($this->appointment->start_date)
                    ->diffInHours(Carbon::parse($this->appointment->end_date)),
                'type' => $this->appointment->type,
            ]);
    }
}
