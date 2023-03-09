<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AppointmentDateFinderInterface;
use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\PatientRepositoryInterface;
use App\Exceptions\Appointment\AppointmentDateNotFoundException;
use App\Helpers\AppointmentDateHelper;
use App\Models\Patient;
use Carbon\Carbon;

class AppointmentDateFinder implements AppointmentDateFinderInterface
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
        private readonly PatientRepositoryInterface $patientRepository,
        private readonly AppointmentAvailabilityServiceInterface $appointmentAvailabilityChecker,
    ) {
    }

    public function findAvailableAppointmentDate(Patient $patient): Carbon
    {
        $date = $this->appointmentAvailabilityChecker->findAppointmentDate($patient);

        if (!$this->appointmentRepository->hasDateAppointments($date)) {
            return AppointmentDateHelper::getFirstAppointmentOfDate($date);
        }

        if (!$this->patientRepository->hasPatientAppointmentOnDate($patient, $date)) {
            $latestAppointmentOfDate = $this->appointmentRepository->getLatestAppointmentOfDate($date);

            return Carbon::parse($latestAppointmentOfDate->start_date)->addHour();
        }

        /**
         * should never enter here
         * @noinspection PhpUnhandledExceptionInspection
         */
        throw new AppointmentDateNotFoundException('Appointment date not found');
    }
}
