<?php

namespace App\Exceptions;

use BadMethodCallException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        // $this->reportable(function (Throwable $e) {
           
        // });
    }

    public function render($request, Throwable $e)  
    {
        if ($e instanceof NotFoundHttpException) {

            return $this->errorResponse($e,'Route not found',Response::HTTP_NOT_FOUND);
            
        }elseif ($e instanceof UnauthorizedHttpException) {

            return $this->errorResponse($e,'UNAUTHORIZED ',Response::HTTP_UNAUTHORIZED);

        }elseif ($e instanceof MethodNotAllowedHttpException) {

            return $this->errorResponse($e,'Method not allowed ',Response::HTTP_METHOD_NOT_ALLOWED);

        }elseif ($e instanceof MassAssignmentException) {

            return $this->errorResponse($e, '',500);

        }elseif ($e instanceof ModelNotFoundException) {

            return $this->errorResponse($e,'Data not found with ID '.$request->id);

        } elseif ($e instanceof BadMethodCallException) {

           return $this->errorResponse($e,'Method does not exist',Response::HTTP_BAD_REQUEST);
        } 
        else {
           return $this->errorResponse($e);
        }
    }

    private function errorResponse($e, $message = '',$code = 404){
        return response()->json([
            'status' => false,
            'errors' => $e->getMessage(),
            'message' => $message,
        ],$code);
    }
}
