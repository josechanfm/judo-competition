<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        // API 的路由模型綁定失敗時，避免把內部模型名稱與 id 回傳給客戶端
        // （Laravel 預設訊息為 "No query results for model [App\Models\Bout] 999"）。
        // ModelNotFoundException 會先被 prepareException() 包成 NotFoundHttpException，
        // 因此這裡攔截 NotFoundHttpException 再檢查其 previous。
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($e->getPrevious() instanceof ModelNotFoundException
                && ($request->is('api/*') || $request->expectsJson())) {
                return response()->json([
                    'success' => false,
                    'message' => '找不到對應的資料',
                ], 404);
            }
        });
    }
}
