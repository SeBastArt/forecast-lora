<?php
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

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
Route::get('/', 'Web\NodeController@dashboard');

//Fields
Route::resource('fields', 'Web\FieldController')->except([
     'index', 'create', 'edit'
]);

//Nodes
Route::resource('nodes', 'Web\NodeController')->except([
     'index', 'create', 'edit', 'store'
]);

//Users
Route::resource('users', 'Web\UserController')->except([
     
]);

//Companies
Route::resource('companies', 'Web\CompanyController')->except([
     
]);
Route::get('dashboard/nodes', 'Web\NodeController@dashboard');
Route::get('dashboard/companies', 'Web\CompanyController@dashboard');
//Facilities
Route::resource('facilities', 'Web\FacilityController')->except([
     'index', 'store'
]);

Route::get('dashboard/companies', 'Web\CompanyController@dashboard');
Route::get('companies/{company}/facilities', 'Web\FacilityController@index');
Route::post('companies/{company}/facilities', 'Web\FacilityController@store');

Route::get('facilities/{facility}/nodes', 'Web\NodeController@index');
Route::post('facilities/{facility}/nodes', 'Web\NodeController@store');

Route::get('nodes/{node}/fields', 'Web\FieldController@index');

//Token
Route::resource('token', 'Web\TokenController');

//Route::get('config-nodes', 'Web\NodeController@create');

Route::post('nodes/{node}/fieldposition', 'Web\NodeController@position');

//Dock
Route::resource('dock', 'Web\DockController')->only([
     'index', 'store'
]);

//Route::resource('nodes/{node}/fields', 'Web\FieldController');

Route::get('user-list', 'Web\UserController@usersList');
Route::get('user-view', 'Web\UserController@usersView');
Route::get('user-edit', 'Web\UserController@usersEdit');

// locale route
Route::get('lang/{locale}',[LanguageController::class, 'swap']);

Auth::routes();

