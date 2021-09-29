<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Helper\JsonApiResponse;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation.
    |
    */

    function register(Request $request)
    {
        $rules = [
            'username' => 'required|string|min:3|max:25|unique:users,username',
            'password' => 'required|string|min:5|max:20',
            'email' => 'required|email|unique:users,email',
        ];

        $customMessages = [
            'required' => 'The :attribute field is required.',
            'unique'    => ':attribute is already used',
        ];

        $user_details =  $this->validate($request, $rules, $customMessages);
        $user_details['password'] = bcrypt($user_details['password']);
        $create = User::create($user_details);

        if(!$create){
            return JsonApiResponse::error('Something went wrong in Registration',422);
        }        
        return JsonApiResponse::success('Successfully created User');
    }
}