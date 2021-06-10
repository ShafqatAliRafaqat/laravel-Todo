<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        // $this->renderable(function(Exception $exception, $request) {
            // findOrFail Exception handler
            // if ( $exception instanceof ModelNotFoundException) {
            //     return (new ResponseBuilder(404, __('Sorry, We did not find your record.')))->build();
            // }
            // For 404 routes
            // if ($exception instanceof NotFoundHttpException) {
            //     return (new ResponseBuilder(404, __('Requested API not found!')))->build();
            // }
            // Validator validation fail Exception handling
            // if ($exception instanceof ValidationException) {
            //     $errors = $exception->validator->errors()->getMessages();
            //     $firstErrorMessage = array_first($errors);
            //     return (new ResponseBuilder(400, __($firstErrorMessage[0])))->build();
            // }
        // });
    }
}
