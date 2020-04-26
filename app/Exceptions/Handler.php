<?php

namespace App\Exceptions;

use App\Alice\ApiResponser;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    private $apiResponser;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public function __construct(ApiResponser $apiResponser) {
        $this->apiResponser = $apiResponser;
    }

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
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception) {
        if($exception instanceof HttpException){
            $code = $exception->getStatusCode();
            $message = Response::$statusTexts[$code];

            return $this->apiResponser->error($message, $code);
        }

        if($exception instanceof ModelNotFoundException){
            $model = strtolower(class_basename($exception->getModel()));

            return $this->apiResponser->error('Model '.$model.' not found', Response::HTTP_NOT_FOUND);
        }

        if($exception instanceof AuthorizationException){
            return $this->apiResponser->error($exception->getMessage(), Response::HTTP_FORBIDDEN);
        }
        if($exception instanceof AuthenticationException){
            return $this->apiResponser->error($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if($exception instanceof ValidationException){
            $errors = $exception->validator->errors()->getMessages();
            return $this->apiResponser->error($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(env('APP_DEBUG', FALSE)){
            return parent::render($request, $exception);
        }

        return $this->apiResponser->error('Unexpected error. Try later!', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
