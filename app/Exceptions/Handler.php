<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\JsonApi;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
//        \Illuminate\Validation\ValidationException::class,
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        error_log("Exception [".get_class($e)."]: ". $e->getMessage() ." file ".$e->getFile().":".$e->getLine());
        if ($e instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
    		return response($e->getStatusCode())->json(['token_expired']);
    	} else if ($e instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {
    		return response( $e->getStatusCode())->json(['token_invalid']);
    	}

        // Handle Ajax requests that fail because the model doesn't exist
        if ($request->ajax() || $request->wantsJson()) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $className = last(explode('\\', $e->getModel()));
                return JsonApi::errorResponse(response(), 400, $className . ' not found');
            }

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                error_log("Validation error [".json_encode($e->errors())."]");
                return JsonApi::errorResponse(response(), 422, $e->getResponse());
            }

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                //error_log("Authorization backtrack ".$e->getTraceAsString());
                return JsonApi::errorResponse(response(), 403, 'Not authorized.');
            }

            if ($this->isHttpException($e)) {
                $statusCode = $e->getStatusCode();

                switch ($e->getStatusCode()) {
                    case '404':
                       return JsonApi::errorResponse(response(), 404, 'Endpoint not found');
                    case '405':
                        return JsonApi::errorResponse(response(), 405, 'Method not allowed');
                    default:
                        return JsonApi::errorResponse(response(), $statusCode, 'Unknown status.');

                }
            }
            // Try to parse 500 Errors in a bit nicer way when debug is enabled.
            if (config('app.debug')) {
                return JsonApi::errorResponse(response(), 500, "Server Exception [".class_basename($e)."]: " .$e->getMessage()." file ".$e->getFile().":".$e->getLine());
            }
        }

        return parent::render($request, $e);
    }
}
