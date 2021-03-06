<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'AuthController@authenticate');
Route::post('register', 'AuthController@register');
Route::post('password/reset', 'AuthController@passwordReset');

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('profile', 'ProfileController@store');
    Route::get('profile/', 'ProfileController@show');

    Route::post('friends/add/{friend}', 'FriendController@addFriend');
});
