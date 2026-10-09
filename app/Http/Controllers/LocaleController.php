<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    //
    public function index($locale)
    {
        // 只接受白名單內的語系，其餘一律退回預設語系，避免寫入無效值污染 session
        $supported = config('app.available_locales', ['en']);

        if (! in_array($locale, $supported, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        session()->put('locale', $locale);
        return response()->json(['message' => $locale]);
    }
}
