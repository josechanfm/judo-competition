<?php

namespace App\Http\Controllers\Api\Competition\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function get(Request $request)
    {
        return response()->json([
            'contest' => $request->user(),
        ]);
    }
}
