<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->reportable(function (Throwable $e) {
        
        });

        /*Hanle validation exception*/
        $this->renderable(function (ValidationException $e, $request) {
            return $this->convertValidationExceptionToResponse($e, $request);
        });

         /*Handle ModelNotFoundException*/
         $this->renderable(function (ModelNotFoundException $e, $request) {
            return $this->modelNotFoundResponse("Does not exists any model with the specified identificator");
        
        });

        /*Handle NotFoundHttpException*/
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return $this->returnNotFoundResponse("The specified URL cannot be found");
        });

        /*Handle MethodNotAllowedHttpException*/
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return $this->methodNotAllowedResponse("The specified method for the request is invalid");
        
        });
        
        /*Handle BadMethodCallException*/
        $this->renderable(function (\BadMethodCallException $e, $request) {
            return $this->returnNotFoundResponse("The specified method for the request is not found");
        
        });
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
    */
    protected function unauthenticated($request, AuthenticationException $exception)
    {

        if ($this->isFrontend($request)) {
            return redirect()->route('admin.login');
        }

        return $this->notAuthorizedResponse('You are not authorized');
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
  
        $errors  = $e->validator->errors()->getMessages();
        $message = $e->getMessage();

        if ($this->isFrontend($request)) {
            return $request->ajax() ? response()->json(['message' => $message,'errors' => $errors], 400) : redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($errors);
        }

        $firstError = $e->validator->errors()->first();

        return $this->errorResponse([
                                        'status'  => false,
                                        'message' => $firstError,
                                        'errors'  => $errors

                                    ], 400);
    }

    /*function to check request type*/
    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }

    // public function render($request, Throwable $e){
    //     $response = $this->handleException($request,$e);
    //     return $response;
    // }

    // public function handleException($request,$e) {
        

    //     if($e instanceof ModelNotFoundException) {
    //         $modelName = strtolower(class_basename($e->getModel()));
    //         return $this->errorResponse("Does not exist any {$modelName} with the specified identifier",404);
    //     }

    //     if($e instanceof MethodNotAllowedHttpException) {
    //         return $this->errorResponse("The specified method for the request is invalid",405);
    //     }

    //     if($e instanceof NotFoundHttpException) {
    //         return $this->errorResponse('The specified url cannot be found',404);
    //     }

    //     if($e instanceof HttpException) {
    //         return $this->errorResponse($e->getMessage(),$e->getStatusCode());
    //     }

    //     return $this->errorResponse('Unexpected Exception. Try later',500);
    // }
}
