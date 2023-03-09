<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Symfony\Component\HttpFoundation\Response;

interface AppointmentCreatorInterface
{
    public function create(StoreAppointmentRequest $request): Appointment|Response;
}
