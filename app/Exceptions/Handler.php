<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Http;
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
     *
     * @return void
     */
    public function register()
    {
        $response = [
            'message' => '',
            'errors' => [],
            'success' => false,
            'data' => [],
        ];

        $this->renderable(function (ThrottleRequestsException $e, $request) use ($response) {
            if ($request->is('api/*')) {
                $response['message'] = ! empty($e->getMessage()) ? $e->getMessage() : 'Too many Attempts';
                return response()->json($response, 429);
            }
        });
        $this->renderable(function (NotFoundHttpException $e, $request) use ($response) {
            if ($request->is('api/*')) {
                $response['message'] = ! empty($e->getMessage()) ? $e->getMessage() : 'Record not found.';
                return response()->json($response, 404);
            }
        });
        $this->reportable(function (Throwable $e) use ($response) {
            $request = request();
            if ($request->is('api/*')) {
                $response['message'] = ! empty($e->getMessage()) ? $e->getMessage() : 'Throwable Exception';
                return response()->json($response, 422);
            }
        });
        $this->renderable(function (Exception $e, $request) use ($response) {
            if ($request->is('api/*')) {
                $response['message'] = ! empty($e->getMessage()) ? $e->getMessage() : 'Exception';
                return response()->json($response, 422);
            }
        });
    }
}
