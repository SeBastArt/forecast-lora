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
Route::get('/', 'HomeController@index');

//Nodes
Route::resource('nodes', 'Web\NodeController')->except([
     'create', 'edit'
]);

//Token
Route::resource('token', 'Web\TokenController');

Route::get('nodes-create', 'Web\NodeController@create');

Route::post('nodes/{node}/fieldposition', 'Web\NodeController@position');

//Dock
Route::resource('dock', 'Web\DockController')->only([
     'index', 'store'
]);

Route::resource('nodes/{node}/fields', 'Web\FieldController');

// locale route
Route::get('lang/{locale}',[LanguageController::class, 'swap']);

Auth::routes();

