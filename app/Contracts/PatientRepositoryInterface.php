<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Patient;
use Carbon\Carbon;

interface PatientRepositoryInterface
{
    public function createPatient(array $attributes): Patient;

    public function findByDni(string $dni): ?Patient;

    public function hasPatientAppointmentOnDate(Patient $patient, Carbon $date): bool;
}
