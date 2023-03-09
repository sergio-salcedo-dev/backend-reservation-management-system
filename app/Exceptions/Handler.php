<?php

namespace App\Exceptions;

use App\Http\Resources\Message\ErrorMessageResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return $this->modelNotFoundResponse($e);
            }
        });
    }

    /**
     * @return Response|void
     */
    private function modelNotFoundResponse(Throwable $e)
    {
        if ($e instanceof NotFoundHttpException) {
            $previousException = $e->getPrevious();

            if ($previousException instanceof ModelNotFoundException) {
                $modelsNamespace = "App\Models\\";
                $modelWithNamespace = $previousException->getModel();
                $modelWithoutNamespace = str_replace($modelsNamespace, '', $modelWithNamespace);
                $errorMessageResource = new ErrorMessageResource(['message' => "$modelWithoutNamespace not found"]);

                return $errorMessageResource->response()->setStatusCode(Response::HTTP_NOT_FOUND);
            }
        }
    }
}
