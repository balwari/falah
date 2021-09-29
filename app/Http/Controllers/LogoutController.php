<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\JsonApiResponse;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    function logout(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users'
        ]);

        // Logout single current session
        //Auth::user()->token()->revoke();

        // Logout all sessions and devices
        Auth::user()->tokens()->update([
            'revoked' => true
        ]);

        return JsonApiResponse::success('Successfully logged out.');
    }
}