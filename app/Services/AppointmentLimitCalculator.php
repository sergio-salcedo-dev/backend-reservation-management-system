<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AppointmentLimitCalculatorInterface;
use Carbon\Carbon;

class AppointmentLimitCalculator implements AppointmentLimitCalculatorInterface
{
    public function getMaxAppointmentsPerDay(
        $startTime = self::OPENING_TIME,
        $endTime = self::CLOSING_TIME,
        $appointmentDurationInMinutes = self::APPOINTMENT_DURATION_IN_MINUTES
    ): int {
        $today = Carbon::today();
        $start = $today->copy()->setTimeFromTimeString($startTime);
        $end = $today->copy()->setTimeFromTimeString($endTime);
        $differenceInMinutes = $end->diffInMinutes($start);

        return intval(floor($differenceInMinutes / $appointmentDurationInMinutes));
    }
}
