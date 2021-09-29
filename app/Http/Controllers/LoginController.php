<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\JsonApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    function login(Request $request)
    {
        $rules = [
            'username' => 'required|string|min:3|max:25|exists:users,username',
            'password' => 'required|string|min:5|max:20'
        ];

        $customMessages = [
            'required' => 'The :attribute field is required.',
        ];

        $credentials =  $this->validate($request, $rules, $customMessages);

        if (!Auth::attempt($credentials)) {
            return JsonApiResponse::error('Invalid credentials.', 422);
        }
        
        $user_details = array();
        $user_details['email'] = Auth::user()->email;
        $user_details['username'] = Auth::user()->username;
        
        return JsonApiResponse::success('Successfully logged in.', [[
            'access_token' => Auth::user()->createToken(Str::random(50))->accessToken,
            'user_details' => $user_details
        ]]);
    }
}
