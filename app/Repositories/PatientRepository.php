<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\PatientRepositoryInterface;
use App\Models\Patient;
use Carbon\Carbon;

class PatientRepository implements PatientRepositoryInterface
{
    public function createPatient(array $attributes): Patient
    {
        return Patient::create($this->formatAttributes($attributes));
    }

    public function findByDni(string $dni): ?Patient
    {
        return Patient::where('dni', $dni)->first();
    }

    private function formatAttributes(array $attributes): array
    {
        return [
            'full_name' => mb_convert_case($attributes['full_name'], MB_CASE_TITLE, 'UTF-8'),
            'email' => mb_strtolower($attributes['email'], 'UTF-8'),
            'phone' => $attributes['phone'],
            'dni' => mb_strtoupper($attributes['dni'], 'UTF-8'),
        ];
    }

    public function hasPatientAppointmentOnDate(Patient $patient, Carbon $date): bool
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        return $patient->appointments()->whereBetween('start_date', [$startOfDay, $endOfDay])->exists();
    }
}
