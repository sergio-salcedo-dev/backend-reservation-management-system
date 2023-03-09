<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentRepositoryInterface
{
    public function getAppointmentsByDate(Carbon $date): Collection;

    public function createAppointment(?Patient $patient, string $appointmentType, Carbon $appointmentDate): Appointment;

    public function getLatestAppointmentOfDate(Carbon $date): Appointment;

    public function hasDateAppointments(Carbon $date): bool;

    public function getAppointmentsCountOnDate(Carbon $date): int;
}
