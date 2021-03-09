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


//todo test for www.server.com/api/...
Route::post('dock', 'Api\DockApiController@dock');

Route::get('nodes/{node}/data', 'Api\NodeDataApiController@data');
Route::get('nodes/{node}', 'Api\NodeDataApiController@meta');
Route::middleware('auth:sanctum')->get('nodes/{node}/data/csv', 'Api\NodeDataApiController@csvdata');