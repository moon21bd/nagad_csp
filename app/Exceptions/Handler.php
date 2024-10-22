<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            // Check if it's a Laratrust permission error
            if ($exception->getStatusCode() === Response::HTTP_FORBIDDEN) {
                return response()->json([
                    'message' => 'User does not have the necessary access rights.',
                    'error' => 'permission_denied',
                    'status' => Response::HTTP_FORBIDDEN,
                ], Response::HTTP_FORBIDDEN);
            }

            if ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                return response()->json([
                    'message' => 'Too many login attempts. Please try again later.',
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

        }

        return parent::render($request, $exception);

    }
}
