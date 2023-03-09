<?php

declare(strict_types=1);

namespace App\Contracts;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

interface ExceptionHandlerInterface
{
    public function handle(
        Throwable $exception,
        ?string $customMessage = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): Response;
}
