<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class AppointmentBookerMutex
{
    private const MUTEX_PREFIX = 'appointment_mutex_';
    private const EXPIRES_IN_SECONDS = 60;
    private const BLOCKING_IN_SECONDS = 5;

    /**
     * Acquire the mutex to prevent other requests from booking an appointment
     */
    public function blockAppointmentCreation(Closure $callback)
    {
        $mutexKey = self::MUTEX_PREFIX . time();

        return Cache::lock($mutexKey, self::EXPIRES_IN_SECONDS)->block(self::BLOCKING_IN_SECONDS, $callback);
    }
}
