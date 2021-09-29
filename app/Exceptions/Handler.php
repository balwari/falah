<?php

namespace App\Exceptions;

use App\Helper\JsonApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    private $responseData;
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
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        $e = $this->prepareException($e);
        return $this->prepareJsonApiErrorResponse($request, $e);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonApiErrorResponse($request, Throwable $e)
    {
        if ($e instanceof FatalThrowableError && config('app.debug')) {
           $this->responseData =  JsonApiResponse::create(true, 500, 'Fatal Error Something went wrong.', $e->getMessage() . ' in ' . $e->getFile() . ', line no ' . $e->getLine(), []);

        } elseif ($e instanceof FatalThrowableError) {
            $this->responseData =  JsonApiResponse::create(true, 500, 'Whoops, looks like something went wrong.','', []);

        } elseif ($e instanceof NotFoundHttpException) {
            $this->responseData =  JsonApiResponse::create(true, 404, 'End Point Not Found.','', []);

        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $this->responseData =  JsonApiResponse::create(true, 405, 'Method Not Allowed.','', []);

        } elseif ($e instanceof AuthenticationException) {
            $this->responseData =  JsonApiResponse::create(true, 401, ucwords('Access token invalid or expired'),'', []);

        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        } else {
            $this->responseData =  JsonApiResponse::create(true, 500, 'Something went wrong.', $e->getMessage() . ' in ' . $e->getFile() . ', line no ' . $e->getLine(), []);
        }
        return $this->createNewJsonResponse($this->responseData, $e);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errorBag = collect();
        $messageData = $e->errors();
        foreach ($messageData as $key => $value) {
            $errorBag->push($value);
        }
        // $errors = implode(', ', array_flatten($errorBag));
        // $errors = array_flatten($errorBag);
        $errors = array();
        array_walk_recursive($errorBag, function($a) use (&$errors) { $errors[] = $a; });
        $this->responseData =  JsonApiResponse::create(true, $e->status, $errors[0],'', []);
        return response()->json($this->responseData, $e->status);
    }

    protected function createNewJsonResponse($responseData, $e)
    {
        return new JsonResponse(
            $responseData,
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

}
