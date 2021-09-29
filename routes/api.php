<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// without auth apis

Route::post('/register', 'App\Http\Controllers\RegisterController@register');
Route::post('/login', 'App\Http\Controllers\LoginController@login');

// with auth apis

Route::group(['middleware' => ['auth:api']], function () {

    Route::post('/logout', 'App\Http\Controllers\LogoutController@logout');

    /** student routes*/

    Route::group(['prefix' => 'students'], function () {
        Route::get('/', 'App\Http\Controllers\StudentController@get');        
        Route::post('/add', 'App\Http\Controllers\StudentController@add');        
    });
});

