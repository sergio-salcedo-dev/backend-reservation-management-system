<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\AppointmentCreatorInterface;
use App\Contracts\AppointmentDateFinderInterface;
use App\Contracts\AppointmentLimitCalculatorInterface;
use App\Contracts\AppointmentRepositoryInterface;
use App\Contracts\EmailSenderInterface;
use App\Contracts\ExceptionHandlerInterface;
use App\Contracts\PatientRepositoryInterface;
use App\Repositories\AppointmentRepository;
use App\Repositories\PatientRepository;
use App\Services\AppointmentAvailabilityService;
use App\Services\AppointmentAvailabilityServiceInterface;
use App\Services\AppointmentCreator;
use App\Services\AppointmentDateFinder;
use App\Services\AppointmentLimitCalculator;
use App\Services\EmailSender;
use App\Services\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /** Register services. */
    public function register(): void
    {
        $this->app->bind(PatientRepositoryInterface::class, PatientRepository::class);
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->bind(EmailSenderInterface::class, EmailSender::class);
        $this->app->bind(AppointmentCreatorInterface::class, AppointmentCreator::class);
        $this->app->bind(ExceptionHandlerInterface::class, ExceptionHandler::class);
        $this->app->bind(AppointmentDateFinderInterface::class, AppointmentDateFinder::class);
        $this->app->bind(AppointmentLimitCalculatorInterface::class, AppointmentLimitCalculator::class);
        $this->app->bind(AppointmentAvailabilityServiceInterface::class, AppointmentAvailabilityService::class);
    }

    /** Bootstrap services. */
    public function boot(): void
    {
        //
    }
}
