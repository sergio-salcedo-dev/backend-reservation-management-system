<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Helpers\AppointmentDateHelper;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = AppointmentDateHelper::getFirstAppointmentOfDate(Carbon::now());
        $patient = Patient::firstOrCreate((new PatientFactory())->definition());

        return [
            'patient_id' => $patient->id,
            'type' => 'revision',
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addHour(),
        ];
    }
}
