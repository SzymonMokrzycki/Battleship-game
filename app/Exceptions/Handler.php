<?php

namespace App\Exceptions;

use Throwable;
use ErrorException;
use BadMethodCallException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        /*$this->renderable(function (NotFoundHttpException $e, $request) {
            if($request->is('api/*')){
                return response()->json(['message' => 'Object not found'], 404);
            }
        });

        $this->renderable(function (ErrorException $e, $request) {
            if($request->is('api/*')){
                return response()->json(['message' => 'Read object on null'], 500);
            }
        });

        $this->renderable(function (QueryException $e, $request) {
            if($request->is('api/*')){
                return response()->json(['message' => 'Server error'], 500);
            }
        });

        $this->renderable(function (RouteNotFoundException $e, $request) {
            if($request->is('api/*') && Auth::check()){
                return response()->json(['message' => 'Server error'], 500);
            }
        });

        $this->renderable(function (RouteNotFoundException $e, $request) {
            if($request->is('api/*') && !Auth::check()){
                return response()->json(['message' => 'You are not logged in'], 401);
            }
        });

        $this->renderable(function (HttpException $e, $request) {
            if($e->getStatusCode() == 403){
                return response()->json(['message' => 'Access denied'], 403);
            }
        });

        $this->renderable(function (BadMethodCallException $e, $request) {
            return response()->json(['message' => 'Resource dosent exist'], 500);
        });*/
    }

    public function render($request, Throwable $e) {
        if($request->is('api/*')){
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
