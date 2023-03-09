<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Patient;
use Carbon\Carbon;

interface AppointmentAvailabilityServiceInterface
{
    public function findAppointmentDate(Patient $patient): Carbon;
}
