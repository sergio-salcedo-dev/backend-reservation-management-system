<?php

declare(strict_types=1);

namespace App\Contracts;

interface AppointmentLimitCalculatorInterface
{
    public const OPENING_TIME = '10:00';
    public const CLOSING_TIME = '22:00';
    public const APPOINTMENT_DURATION_IN_MINUTES = 60;

    public function getMaxAppointmentsPerDay(
        $startTime = self::OPENING_TIME,
        $endTime = self::CLOSING_TIME,
        $appointmentDurationInMinutes = self::APPOINTMENT_DURATION_IN_MINUTES
    ): int;
}
