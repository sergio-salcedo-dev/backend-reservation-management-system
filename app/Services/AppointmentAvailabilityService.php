<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AppointmentLimitCalculatorInterface;
use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\PatientRepositoryInterface;
use App\Models\Patient;
use Carbon\Carbon;

class AppointmentAvailabilityService implements AppointmentAvailabilityServiceInterface
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
        private readonly AppointmentLimitCalculatorInterface $appointmentLimitCalculator,
        private readonly PatientRepositoryInterface $patientRepository
    ) {
    }

    public function findAppointmentDate(Patient $patient): Carbon
    {
        $date = Carbon::now();

        do {
            $date = $this->findNextAvailableDate($patient, $date);
        } while (!$this->isDateAvailable($patient, $date));

        return $date;
    }

    private function isDateAvailable(Patient $patient, Carbon $date): bool
    {
        return !$this->isDateFullyBooked($date) &&
            !$this->patientRepository->hasPatientAppointmentOnDate($patient, $date);
    }

    private function findNextAvailableDate(Patient $patient, Carbon $date): Carbon
    {
        while ($date->isWeekend() || !$this->isDateAvailable($patient, $date)) {
            $date = $this->addOneDay($date);
        }

        return $date;
    }

    private function isDateFullyBooked(Carbon $date): bool
    {
        $appointmentsOfTheDateCount = $this->appointmentRepository->getAppointmentsCountOnDate($date);

        return $appointmentsOfTheDateCount >= $this->appointmentLimitCalculator->getMaxAppointmentsPerDay();
    }

    private function addOneDay(Carbon $date): Carbon
    {
        return $date->copy()->addDay();
    }
}
