<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Services\AppointmentLimitCalculator;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AppointmentBookerTest extends TestCase
{
    use DatabaseMigrations;

    private const APPOINTMENT_STRUCTURE = [
        'data' => [
            'id',
            'appointmentType',
            'dayOfWeek',
            'date',
            'time',
            'duration',
            'createdAt',
        ],
    ];

    private string $bookAppointmentRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookAppointmentRoute = route('appointments.book');
    }

    public function test_it_returns_status_code_422_if_patient_id_does_not_exist_in_db(): void
    {
        $patient = Patient::factory()->make();
        $data = [
            'dni' => $patient->dni,
            'appointment_type' => 'revision',
        ];

        $this->makeBookAppointmentRequest($data)->assertUnprocessable();

        $this->assertDatabaseCount(Appointment::class, 0);
    }

    /** @dataProvider invalidDniProvider */
    public function test_it_does_not_book_appointment_with_invalid_dni(?string $dni): void
    {
        $this->makeBookAppointmentRequest(['dni' => $dni, 'appointment_type' => 'first appointment'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['dni']);

        $this->assertDatabaseCount(Appointment::class, 0);
    }

    /** @dataProvider invalidAppointmentTypeProvider */
    public function test_it_does_not_book_appointment_with_invalid_appointment_type(?string $appointmentType): void
    {
        $this->makeBookAppointmentRequest(['dni' => $this->fakerPhp->dni(), 'appointment_type' => $appointmentType])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['appointment_type']);

        $this->assertDatabaseCount(Appointment::class, 0);
    }

    /** @dataProvider validAppointmentTypeProvider */
    public function test_it_books_the_first_appointment_of_the_day(?string $appointmentType): void
    {
        $patient = Patient::factory()->create();
        $data = [
            'dni' => $patient->dni,
            'appointment_type' => $appointmentType,
        ];

        $this->makeBookAppointmentRequest($data)->assertCreated()->assertJsonStructure(self::APPOINTMENT_STRUCTURE);

        $this->assertDatabaseCount(Appointment::class, 1);
        $this->assertDatabaseHas(Appointment::class, [
            'patient_id' => $patient->id,
            'type' => $appointmentType,
        ]);
        $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();
        $this->assertIsWeekDay($appointment->start_date);
        $this->assertAppointmentTime('10:00:00', $appointment->start_date);
        $this->assertAppointmentTime('11:00:00', $appointment->end_date);
    }

    public function test_it_does_not_book_two_appointments_on_same_day_for_same_patient(): void
    {
        $patient = Patient::factory()->create();
        $data = [
            'dni' => $patient->dni,
            'appointment_type' => 'revision',
        ];

        $this->makeBookAppointmentRequest($data)->assertCreated();
        $this->makeBookAppointmentRequest($data)->assertCreated();

        $this->assertDatabaseCount(Appointment::class, 2);
        $appointments = Appointment::where('patient_id', $patient->id)->get();
        $startDate1stAppointment = $appointments[0]->start_date;
        $startDate2ndAppointment = $appointments[1]->start_date;
        $this->assertIsWeekDay($startDate1stAppointment);
        $this->assertIsWeekDay($startDate2ndAppointment);
        $this->assertAppointmentTime('10:00:00', $startDate1stAppointment);
        $this->assertAppointmentTime('10:00:00', $startDate2ndAppointment);
        $this->assertDatesAreNotInSameDay($startDate1stAppointment, $startDate2ndAppointment);
    }

    public function test_it_books_appointments_on_same_day_for_different_patients_with_one_hour_difference(): void
    {
        $patient1 = Patient::factory()->create();
        $data1 = [
            'dni' => $patient1->dni,
            'appointment_type' => 'revision',
        ];

        $patient2 = Patient::factory()->create();
        $data2 = [
            'dni' => $patient2->dni,
            'appointment_type' => 'revision',
        ];

        $this->makeBookAppointmentRequest($data1)->assertCreated();
        $this->makeBookAppointmentRequest($data2)->assertCreated();

        $this->assertDatabaseCount(Appointment::class, 2);
        $appointments = Appointment::all();
        $this->assertAppointmentBelongsToPatient($appointments[0], $patient1);
        $this->assertAppointmentBelongsToPatient($appointments[1], $patient2);
        $startDate1stAppointment = $appointments[0]->start_date;
        $startDate2ndAppointment = $appointments[1]->start_date;
        $this->assertHoursDifferenceBetweenDates(1, $startDate1stAppointment, $startDate2ndAppointment);
        $this->assertDatesAreInSameDay($startDate1stAppointment, $startDate2ndAppointment);
        $this->assertAppointmentTime('10:00:00', $startDate1stAppointment);
        $this->assertAppointmentTime('11:00:00', $startDate2ndAppointment);
    }

    public function test_it_books_up_to_limit_appointments_per_day(): void
    {
        $maxAppointmentPerDay = $this->getMaxAppointmentsPerDay();
        $totalAppointments = $maxAppointmentPerDay + 1;
        $appointmentType = 'revision';
        Patient::factory($maxAppointmentPerDay + 1)->create();
        $patients = Patient::all();

        $this->makeBookAppointmentRequests($patients, $appointmentType);

        $this->assertDatabaseCount(Appointment::class, $totalAppointments);
        $appointments = Appointment::all();
        $firstAppointmentDate = $appointments[0]->start_date;
        $lastAppointmentDate = $appointments[count($appointments) - 1]->start_date;
        $this->assertEquals(
            $maxAppointmentPerDay,
            Appointment::whereDate('start_date', $this->getCarbon($firstAppointmentDate))->count()
        );
        $this->assertEquals(1, Appointment::whereDate('start_date', $this->getCarbon($lastAppointmentDate))->count());
        $this->assertAppointmentTime('10:00:00', $firstAppointmentDate);
        $this->assertAppointmentTime('10:00:00', $appointments[count($appointments) - 1]->start_date);
        $this->assertDatesAreNotInSameDay($firstAppointmentDate, $lastAppointmentDate);
    }

    public function test_it_does_not_book_appointments_on_weekends(): void
    {
        $this->configureApiRateLimiterPerMinute();
        $workingDays = 5;
        $maxAppointmentPerWeek = $workingDays * $this->getMaxAppointmentsPerDay();
        $appointmentType = 'revision';
        Patient::factory($maxAppointmentPerWeek * 2)->create();
        $patients = Patient::all();

        $this->makeBookAppointmentRequests($patients, $appointmentType);

        foreach (Appointment::all() as $appointment) {
            $this->assertIsWeekDay($appointment->start_date);
        }
    }

    private function invalidDniProvider(): array
    {
        return [
            [null],
            [''],
            ['0123456789012345'],
            ['invalid_dni'],
        ];
    }

    private function invalidAppointmentTypeProvider(): array
    {
        return [
            [null],
            [''],
            ['invalid_type'],
            ['first_revision'],
            ['first_appointment'],
        ];
    }

    private function validAppointmentTypeProvider(): array
    {
        return [
            ['first appointment'],
            ['revision'],
        ];
    }

    private function makeBookAppointmentRequest(array $data = []): TestResponse
    {
        return $this->postJson($this->bookAppointmentRoute, $data);
    }

    private function getCarbon(string $date): ?Carbon
    {
        return !$date ? null : Carbon::parse($date);
    }

    private function assertAppointmentTime(string $expectedTime, string $actualTime): void
    {
        $this->assertEquals($expectedTime, Carbon::parse($actualTime)->format('H:i:s'));
    }

    private function assertIsWeekDay(string $actualTime): void
    {
        $this->assertTrue(Carbon::parse($actualTime)->isWeekDay());
    }

    private function assertHoursDifferenceBetweenDates(
        int $expectedHours,
        string $firstDate,
        string $secondDate
    ): void {
        $date1 = Carbon::parse($firstDate);
        $date2 = Carbon::parse($secondDate);

        $this->assertEquals($expectedHours, $date2->diffInHours($date1));
    }

    private function assertDatesAreInSameDay(string $firstDate, string $secondDate): void
    {
        $date1 = Carbon::parse($firstDate);
        $date2 = Carbon::parse($secondDate);

        $this->assertTrue($date1->isSameDay($date2));
    }

    private function assertDatesAreNotInSameDay(string $firstDate, string $secondDate): void
    {
        $date1 = Carbon::parse($firstDate);
        $date2 = Carbon::parse($secondDate);

        $this->assertFalse($date1->isSameDay($date2));
    }

    private function assertAppointmentBelongsToPatient(Appointment $appointment, Patient $patient): void
    {
        $this->assertEquals($patient->id, $appointment->patient_id);
    }

    private function getMaxAppointmentsPerDay(): int
    {
        return (new AppointmentLimitCalculator())->getMaxAppointmentsPerDay();
    }

    private function makeBookAppointmentRequests(Collection $patients, string $appointmentType): void
    {
        foreach ($patients as $patient) {
            $data = [
                'dni' => $patient->dni,
                'appointment_type' => $appointmentType,
            ];
            $this->makeBookAppointmentRequest($data)->assertCreated();
            $this->assertDatabaseHas(Appointment::class, [
                'patient_id' => $patient->id,
                'type' => $appointmentType,
            ]);
        }
    }

    private function configureApiRateLimiterPerMinute(int $rateLimitPerMinute = 600): void
    {
        RateLimiter::for('api', function (Request $request) use ($rateLimitPerMinute) {
            return Limit::perMinute($rateLimitPerMinute)->by($request->ip());
        });

        $this->postJson($this->bookAppointmentRoute)->assertHeader('X-Ratelimit-Limit', $rateLimitPerMinute);
    }
}
