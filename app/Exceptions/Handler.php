<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if (!empty($request->route()->action['middleware'][0])) {
            if ($request->route()->action['middleware'][0] == 'api') {
                return response()->json([
                    'status' => 90,
                    'message' => 'Chưa có thông tin đăng nhập'
                ], 401);
            }
        }
        // Trả về trang đăng nhập nếu không phải là API
        return redirect()->guest(route('login'));
    }

    public function render($request, Throwable $e)
    {
        // Xử lý lỗi CSRF token mismatch (419)
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {

            // Clear session cũ (session lỗi)
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Nếu request là AJAX => trả JSON
            // if ($request->expectsJson()) {
            //     return response()->json([
            //         'status' => 419,
            //         'message' => 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.'
            //     ], 419);
            // }

            // Nếu request là web => về login
            return redirect()
                ->route('login')
                ->with('error', 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.');
        }

        return parent::render($request, $e);
    }
}
