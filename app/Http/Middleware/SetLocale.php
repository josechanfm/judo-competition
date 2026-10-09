<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * 依 session 記住的語系設定 App locale；
     * 沒有（或不是支援的語系）時使用 config('app.locale')，也就是預設語系。
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $supported = config('app.available_locales', ['en']);
        $locale = $request->session()->get('locale');

        if (! is_string($locale) || ! in_array($locale, $supported, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
