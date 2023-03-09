<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\AppointmentCreatorInterface;
use App\Contracts\EmailSenderInterface;
use App\Contracts\ExceptionHandlerInterface;
use App\Exceptions\AppointmentCreationException;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentBookerMutex;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AppointmentBookerController extends Controller
{


    public function __construct(
        private readonly AppointmentCreatorInterface $appointmentCreator,
        private readonly EmailSenderInterface $emailSender,
        private readonly AppointmentBookerMutex $appointmentBookerMutex,
        private readonly ExceptionHandlerInterface $exceptionHandler,
    ) {
    }

    public function book(StoreAppointmentRequest $request): JsonResource|Response
    {
        try {
            $appointment = $this->bookAppointment($request);
            $this->emailSender->sendAppointmentConfirmationEmail($appointment);

            return new AppointmentResource($appointment);
        } catch (LockTimeoutException $e) {
            return $this->exceptionHandler->handle(
                $e,
                'Could not acquire the appointment booking mutex',
                Response::HTTP_CONFLICT
            );
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }

    /** @throws AppointmentCreationException */
    private function bookAppointment(StoreAppointmentRequest $request): Appointment
    {
        $appointment = $this->appointmentBookerMutex->blockAppointmentCreation(
            function () use ($request) {
                return $this->appointmentCreator->create($request);
            }
        );

        if (!($appointment instanceof Appointment)) {
            throw new AppointmentCreationException('Appointment creation failed');
        }

        return $appointment;
    }
}
