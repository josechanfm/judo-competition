<?php

namespace Modules\Admin\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class LocaleController extends Controller
{
    public function index($locale)
    {
        app()->setLocale($locale);
        session()->put('locale', $locale);
        return response()->json(['message' => 'ok']);
    }
}
