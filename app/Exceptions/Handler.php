<?php

namespace App\Exceptions;

use App\Constants\ResponseCode;
use App\Traits\ResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

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

    protected $jsonUrl = [
        'admin/upload',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        if (!($e instanceof ApiException)) {
            parent::report($e);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ApiException) {
            return $this->exception_response($request, $e->getMessage(), $e->getCode());
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->exception_response($request, 'Not Found', ResponseCode::NOT_FOUND);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->exception_response($request, 'Method not allowed', ResponseCode::METHOD_NOT_ALLOWED);
        }

        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return $this->exception_response($request, '非法操作', ResponseCode::ILLEGAL_OPERATION);
        }

        if ($e instanceof ValidationException) {
            return $this->exception_response($request, $e->validator->errors()->first(), ResponseCode::PARAM_ERR);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->exception_response($request, 'Not Fount Target', ResponseCode::NOT_FOUND);
        }

        if ($e instanceof \Exception) {
            return $this->exception_response($request, $e->getMessage(), ResponseCode::FAIL);
        }

        return parent::render($request, $e);
    }

    /**
     * @param $request
     * @param AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->responseError(__('登录失效'), ResponseCode::UNAUTH);
    }

    /**
     * @param $request
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function exception_response($request, $message, $code)
    {
        return $this->responseError($message, $code);
    }
}
