<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use App\Models\ErrorLog;
use InvalidArgumentException;
use Illuminate\Database\QueryException;


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
            if ($e instanceof HttpException) {
              //$statusCode = $e->getCode();
               $statusCode = $e->getStatusCode();
            } else {
                $statusCode = 500;
            }
            
            if ($e instanceof InvalidArgumentException) {
                
                    $statusCode = 404;  // Adjust 'errors.500' to your custom error view
            }

            if ($e instanceof QueryException) {
                // Log the SQL query that caused the exception
                $statusCode = $e->getCode();

                \Log::error('SQL Query Error: ' . $e->getSql());
                \Log::error('Bindings: ' . implode(', ', $e->getBindings()));
                 
            }

            // if ($e instanceof NotFoundHttpException) {
              
            //     $statusCode = 404; // Ensure 404 status code is assigned for NotFoundHttpException
            // }


            $errorLogObj = new ErrorLog();
            $errorLogObj->page_url = url()->current();
            $errorLogObj->error_message = $e->getMessage();
            $errorLogObj->error_code =$statusCode;
            $errorLogObj->user_ip_address = request()->ip();
            $errorLogObj->user_location = json_encode(request()->attributes->get('user_location'));

            // if(\Auth::check()){
            //     $errorLogObj->user_id = \Auth::user()->id;
            // }
            $errorLogObj->save();
        });

    }

    public function render($request, Throwable $e)
    {
        // if ($request->is('api/*')) {
        //     $controller = app(BaseController::class);
        //     if($e->getMessage() != 'Unauthenticated.'){
        //         return $controller->errorResponse($e->getMessage(),code: 400);
        //     }else{
        //         return $controller->unauthorizeToken("Invalid token passed while accessing api.",401);
        //     }
        // }
        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $e)
    {
        // if ($request->expectsJson()) {
        //     $controller = app(BaseController::class);
        //     return $controller->unauthorizeToken($e->getMessage() ?: 'Invalid or missing token.');
        // }

        return redirect()->guest(route('login'));
    }
}
