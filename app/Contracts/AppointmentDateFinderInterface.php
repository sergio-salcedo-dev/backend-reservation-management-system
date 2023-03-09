<?php

namespace App\Contracts;

use App\Models\Patient;
use Carbon\Carbon;

interface AppointmentDateFinderInterface
{
    public function findAvailableAppointmentDate(Patient $patient): Carbon;
}
