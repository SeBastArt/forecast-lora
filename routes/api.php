<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

Route::group([
    'prefix' => 'auth'
  ], function () {
    Route::post('register','AuthController@register');
    Route::post('login','AuthController@login');
    Route::post('forgetpassword','AuthController@forgetPassword');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('user','AuthController@user');
        Route::get('logout','AuthController@logout');
        
        
    });
});
Route::post('dock', 'Api\DockApiController@dock');

Route::post('/sanctum/token', function (Request $request) {
  $request->validate([
      'email' => 'required|email',
      'password' => 'required',
      'device_name' => 'required',
  ]);

  $user = User::where('email', $request->email)->first();

  if (! $user || ! Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
          'email' => ['The provided credentials are incorrect.'],
      ]);
  }
  $user->tokens()->delete();
  return $user->createToken($request->device_name, ['server:update'])->plainTextToken;
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});