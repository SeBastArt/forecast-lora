<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Web\CompanyController;
use App\Http\Controllers\Web\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */


// Page Route
Route::get('login', 'Auth\Logincontroller@login');
Route::get('logout', 'Auth\Logincontroller@logout');

// locale route
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

Auth::routes(['verify' => true]);

Route::get('/', [CompanyController::class, 'dashboard']);
//User
Route::resource('users', 'Web\UserController')->except([
    'create', 'store', 
]);
Route::get('/profile', 'Web\UserController@profile');
Route::post('/user/{user}/alertaddresses', 'Web\UserController@addAlertAddress');
Route::delete('/user/{user}/alertaddresses/{alertAddress}', 'Web\UserController@destroyAlertAddress');


//Companies
Route::resource('companies', 'Web\CompanyController')->only([
    'index', 'store', 'update', 'edit', 'destroy'
]);
Route::get('dashboard', 'Web\CompanyController@dashboard');

//Facilities
Route::resource('facilities', 'Web\FacilityController')->only([
    'update', 'edit', 'destroy'
]);
Route::get('facilities/{facility}/dashboard', 'Web\FacilityController@dashboard');
Route::get('companies/{company}/facilities', 'Web\FacilityController@index');
Route::post('companies/{company}/facilities', 'Web\FacilityController@store');


//Nodes
Route::resource('nodes', 'Web\NodeController')->only([
    'update', 'show', 'destroy'
]);

Route::get('facilities/{facility}/nodes', 'Web\NodeController@index');
Route::post('facilities/{facility}/nodes', 'Web\NodeController@store')->name('nodes.store');
Route::delete('nodes/{node}/presetdelete', 'Web\NodeController@deletepreset');
Route::get('nodes/{node}/alert/reset', 'Web\NodeController@alert_reset');
Route::post('facilities/{facility}/fileupload', 'Web\NodeController@fileUpload')->name('nodes.fileUpload');
Route::get('facilities/{facility}/filedownload', 'Web\NodeController@fileDownload')->name('nodes.fileDownload');
Route::delete('facilities/{facility}/fileremove', 'Web\NodeController@fileRemove')->name('nodes.fileRemove');

//Fields
Route::resource('fields', 'Web\FieldController')->except([
    'show', 'index', 'store'
]);
Route::get('nodes/{node}/fields', 'Web\FieldController@index');
Route::post('nodes/{node}/fields', 'Web\FieldController@storeNode');
Route::post('presets/{preset}/fields', 'Web\FieldController@storePreset');

//Token
//Route::resource('token', 'Web\TokenController')->only([
//   'destroy'
//]);
Route::post('users/{user}/generatetoken', 'Web\TokenController@store');
Route::delete('users/{user}/token/{token}', 'Web\TokenController@destroy');

//Presets
Route::resource('presets', 'Web\PresetController')->except([
    'create', 'show'
]);
Route::post('presets/{preset}/spread', 'Web\PresetController@spread');

//Dock
Route::resource('dock', 'Web\DockController')->only([
     'index', 'store'
]);


