<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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

Route::post('token', 'Api\TokenApiController@make');

Route::middleware('auth:sanctum')->get('node/data', 'Api\NodeDataApiController@data');
Route::middleware('auth:sanctum')->get('node/meta', 'Api\NodeDataApiController@meta');
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//  return $request->user();
//});