<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AppointmentCreatorInterface;
use App\Contracts\AppointmentDateFinderInterface;
use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\ExceptionHandlerInterface;
use App\Contracts\PatientRepositoryInterface;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AppointmentCreator implements AppointmentCreatorInterface
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
        private readonly PatientRepositoryInterface $patientRepository,
        private readonly ExceptionHandlerInterface $exceptionHandler,
        private readonly AppointmentDateFinderInterface $appointmentDateFinder,
    ) {
    }

    public function create(StoreAppointmentRequest $request): Appointment|Response
    {
        try {
            $patient = $this->patientRepository->findByDni($request->validated('dni'));
            $appointmentDate = $this->appointmentDateFinder->findAvailableAppointmentDate($patient);

            return $this->appointmentRepository->createAppointment(
                $patient,
                $request->validated('appointment_type'),
                $appointmentDate
            );
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }
}
