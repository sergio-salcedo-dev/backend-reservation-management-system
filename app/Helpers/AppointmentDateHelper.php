<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;

class AppointmentDateHelper
{
    const TIMEZONE = 'Europe/Madrid';

    public static function getFirstAppointmentOfDate(Carbon $date): Carbon
    {
        return $date->copy()
            ->setTimezone(AppointmentDateHelper::TIMEZONE)
            ->startOfDay()
            ->addHours(10);
    }
}
