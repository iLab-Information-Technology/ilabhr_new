<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ApiDriverController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $driver = Driver::where('email', $request->email)->first();

        if ($driver && Hash::check($request->password, $driver->password)) {
            $token = $driver->createToken('DriverToken')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function test(Request $request) {

    }
}
