<?php

namespace App\Exceptions;

use Exception;
use http\Env\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $isApiCall = strpos($request->getUri(), '/api/') !== false;

        if ($isApiCall) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'error' => 'Resource not found',
                ], Response::HTTP_NOT_FOUND);
            } elseif ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'error' => 'Endpoint not found',
                ], Response::HTTP_NOT_FOUND);
            } elseif ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'error' => 'Method not allowed',
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } else {
            return parent::render($request, $e);
        }
    }
}
