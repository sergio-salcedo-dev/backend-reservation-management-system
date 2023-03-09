<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\AppointmentRepositoryInterface;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getAppointmentsByDate(Carbon $date): Collection
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        return Appointment::whereBetween('start_date', [$start, $end])->get();
    }

    public function createAppointment(?Patient $patient, string $appointmentType, Carbon $appointmentDate): Appointment
    {
        return Appointment::create([
            'patient_id' => $patient->id,
            'type' => $appointmentType,
            'start_date' => $appointmentDate,
            'end_date' => $appointmentDate->copy()->addHour(),
        ]);
    }

    public function getLatestAppointmentOfDate(Carbon $date): Appointment
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        return Appointment::whereBetween('start_date', [$startOfDay, $endOfDay])->latest('start_date')->first();
    }

    public function hasDateAppointments(Carbon $date): bool
    {
        return $this->getAppointmentsCountOnDate($date) > 0;
    }

    public function getAppointmentsCountOnDate(Carbon $date): int
    {
        return $this->getAppointmentsByDate($date)->count();
    }
}
