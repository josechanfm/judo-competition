<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    //
    public function index($locale)
    {
        app()->setLocale($locale);
        session()->put('locale', $locale);
        return response()->json(['message' => $locale]);
    }
}
