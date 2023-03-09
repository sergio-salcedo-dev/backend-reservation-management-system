<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ExceptionHandlerInterface;
use App\Helpers\AppEnvironmentHelper;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(
        Throwable $exception,
        ?string $customMessage = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): Response {
        return response()->json(['message' => $this->getErrorMessage($exception, $customMessage)], $statusCode);
    }

    private function getErrorMessage(Throwable $exception, ?string $customMessage): string
    {
        $message = $customMessage ?? 'Something went wrong.';

        return AppEnvironmentHelper::isLocalOrTestingEnvironment() ? $exception->getMessage() : $message;
    }
}
