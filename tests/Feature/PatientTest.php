<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    private string $storePatientRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storePatientRoute = route('patients.store');
    }

    public function test_it_creates_a_patient_when_validation_passed(): void
    {
        $patient = Patient::factory()->make();
        $data = [
            'full_name' => $patient->full_name,
            'email' => $patient->email,
            'phone' => $patient->phone,
            'dni' => $patient->dni,
        ];

        $this->makeStorePatientRequest($data)->assertCreated();
        $this->assertDatabaseHas(Patient::class, $data);
        $this->assertDatabaseCount(Patient::class, 1);
    }

    /** @dataProvider invalidStorePatientRequestDataProvider */
    public function test_it_does_not_store_a_patient_if_errors_on_validation(array $data): void
    {
        $this->makeStorePatientRequest($data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['full_name', 'email', 'phone', 'dni']);

        $this->assertDatabaseCount(Patient::class, 0);
    }

    public function test_it_does_not_store_a_patient_if_email_already_exists(): void
    {
        $existingPatient = Patient::factory()->create();

        $this->makeStorePatientRequest([
            'full_name' => 'John Doe',
            'email' => $existingPatient->email,
            'phone' => '1234567890',
            'dni' => $existingPatient->dni,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'dni']);

        $this->assertDatabaseCount(Patient::class, 1);
    }

    public function test_it_searches_for_a_patient_by_id(): void
    {
        $patient = Patient::factory()->create();

        $this->makeSearchPatientRequest($patient->dni)
            ->assertOk()
            ->assertJsonFragment([
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'dni' => $patient->dni,
                'email' => $patient->email,
                'phone' => $patient->phone,
            ]);
    }

    public function test_search_returns_not_found_if_patient_does_not_exist(): void
    {
        $nonExistentDni = '123456789';

        $this->makeSearchPatientRequest($nonExistentDni)
            ->assertNotFound()
            ->assertJson([
                'message' => 'Patient not found',
                'errors' => ["message" => "Patient not found"],
            ]);
    }

    private function makeStorePatientRequest(array $data = []): TestResponse
    {
        return $this->postJson($this->storePatientRoute, $data);
    }

    private function makeSearchPatientRequest(string $dni): TestResponse
    {
        return $this->post(route('patients.search', ['dni' => $dni]));
    }

    private function invalidStorePatientRequestDataProvider(): array
    {
        return [
            [[]],
            [
                [
                    'full_name' => str_repeat('a', 256),
                    'email' => 'invalid-email',
                    'phone' => str_repeat('1', 16),
                    'dni' => 'invalid-dni',
                ],
            ],
        ];
    }
}
