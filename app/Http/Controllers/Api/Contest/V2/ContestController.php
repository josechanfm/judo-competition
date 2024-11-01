<?php

namespace App\Http\Controllers\Api\Contest\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContestController extends Controller
{
    public function get(Request $request)
    {
        return response()->json([
            'contest' => $request->user(),
        ]);
    }
}
