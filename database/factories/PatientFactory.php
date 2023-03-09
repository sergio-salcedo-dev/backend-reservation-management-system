<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fakerPhp = Faker::create('es_ES');

        return [
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => str_replace(['-', ' '], '', $fakerPhp->phoneNumber),
            'dni' => $fakerPhp->dni(),
        ];
    }
}
