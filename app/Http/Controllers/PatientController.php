<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\PatientRepositoryInterface;
use App\Exceptions\Patient\PatientNotFoundException;
use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\Message\ErrorMessageResource;
use App\Http\Resources\Patient\PatientResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PatientController extends Controller
{
    public function __construct(private PatientRepositoryInterface $patientRepository)
    {
    }

    public function store(StorePatientRequest $request): JsonResponse|PatientResource
    {
        $attributes = $request->validated();

        try {
            $patient = $this->patientRepository->createPatient($attributes);

            return new PatientResource($patient);
        } catch (Throwable $e) {
//            $message = 'Something went wrong.';
            $message = $e->getMessage();
            $errorMessageResource = new ErrorMessageResource(['message' => $message]);

            return $errorMessageResource->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(string $dni): PatientResource
    {
        $patient = $this->patientRepository->findByDni($dni);

        if (!$patient) {
            throw new PatientNotFoundException;
        }

        return new PatientResource($patient);
    }
}
