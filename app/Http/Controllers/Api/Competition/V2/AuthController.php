<?php

namespace App\Http\Controllers\Api\Competition\V2;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Contest;
use Illuminate\Http\Request;
use Spatie\Activitylog\ActivityLogger;

class AuthController extends Controller
{
    public function token(Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'token' => 'required|exists:competitions,token',
            'device_uuid' => 'required',
        ]);

        $competition = Competition::where('token', $validated['token'])->first();

        // revoke all previous token
        $competition->tokens()->where('name', $validated['device_uuid'])->delete();
        // dd('aaaa');
        //        activity()->performedOn($contest)
        //                  ->causedBy($validated['device_uuid'])
        //                  ->log('logged in');

        return response()->json([
            'token' => $competition->createToken($validated['device_uuid'])->plainTextToken,
        ]);
    }

    public function revokeToken(Request $request)
    {
        if ($request->user('sanctum')) {
            $request->user('sanctum')->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Token revoked',
            ]);
        }

        return response()->json([
            'message' => 'Invalid Token',
        ]);
    }
}
