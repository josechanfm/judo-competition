<?php

namespace App\Http\Controllers\Tracker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginController extends Controller
{
 
    public function scanQrcode(){
        return Inertia::render('Tracker/ScanQrcode2',[

        ]);
    }
}
