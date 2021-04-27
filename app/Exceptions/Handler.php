<?php

namespace App\Exceptions;

use App\Common\HttpCode;
use App\Events\BaseEvent;
use App\Http\Supports\ApiResponse;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    use BaseEvent;
    use ApiResponse;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }
        try {
            if(App::environment('production', 'staging')){
                logError($exception);
            }
        } catch (Exception $ex) {
            throw $exception; // throw the original exception
        }
    }

    protected function _getArea()
    {
        try {
            $url = url()->current();
        } catch (Exception $e) {
            $url = '';
        }
        switch (true) {
            case strpos($url, getBackendAlias()) !== false:
                return 'backend';
                break;
            case strpos($url, getFrontendAlias()) !== false:
                return 'frontend';
                break;
            default:
                return '';
                break;
        }
    }

    protected function convertExceptionToResponse(Throwable $e)
    {
        if (config('app.debug')) {
            return parent::convertExceptionToResponse($e);
        }

        try {
            DB::connection()->getPdo();
            if (!DB::connection()->getDatabaseName()) {
                die(trans('messages.db_not_connect'));
            }
        } catch (\Exception $e) {
            die(trans('messages.db_not_connect'));
        }

        view()->replaceNamespace('errors', [
            app_path('Views/errors'),
            __DIR__ . '/Views',
        ]);

        $area = $this->_getArea();
        return response()->view('errors::' . $area . '.500', [
            'exception' => $e,
            'area' => $this->_getArea(), 'title' => config('app.title')
        ], 500);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request $request
     * @param  Exception $exception
     * @return JsonResponse|RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        logError($exception);
        if (!$request->expectsJson()) {
            if ($exception instanceof NotFoundHttpException && $this->_getArea() == 'backend' && !backendGuard()->check()) {
                $params = ['return_url' => $request->fullUrl()];
                return redirect()->route('login', $params);
            }
            if ($exception instanceof PermissionException) {
                if ($request->headers->get('referer') && $request->session()->has('errors') && !$request->session()->get('errors')->has('403')) {
                    return redirect()->back()->withErrors(['403' => $exception->getMessage()]);
                }
                return redirect(route($request->route()->controller->getBackUrlDefault()))->withErrors(['403' => $exception->getMessage()]);
            }
            if ($exception instanceof RedirectException) {
                return redirect($exception->getBackUrl())->withErrors([$exception->getCode() => $exception->getMessage()]);
            }
            if ($exception instanceof UnauthorizedHttpException) {
                return $this->unauthenticatedApi($request, $exception);
            }
            return parent::render($request, $exception);
        }
        $exception = $this->prepareException($exception);

        if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            return $exception->getResponse();
        }
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {
            return response()->json($exception->getMessage());
        }

        $response = [];
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = HttpCode::INTERNAL_SERVER_ERROR;
        }
        $message = HttpCode::getMessageForCode($statusCode);
        $response['message'] = $message ? $message : $exception->getMessage();

        if (config('app.debug')) {
            $response['trace'] = $exception->getTrace();
            $response['code'] = $statusCode;
        }

        return $this->renderErrorJson($statusCode, $response);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse|Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->renderErrorJson(HttpCode::UNAUTHORIZED, ['message' => HttpCode::getMessageForCode(HttpCode::UNAUTHORIZED)]);
        }

        return redirect()->guest(route('login'));
    }

    protected function unauthenticatedApi($request, UnauthorizedHttpException $exception)
    {
        return response()->json([
            'errorCode' => HttpCode::EC_UNAUTHENTICATED,
            'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_UNAUTHENTICATED)
        ]);
    }
}
