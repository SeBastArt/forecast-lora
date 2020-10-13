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
Route::get('public', 'HomeController@index');

//Nodes
Route::resource('nodes', 'Web\NodeController')->except([
     'create', 'edit'
]);
Route::post('nodes/{node}/fieldposition', 'Web\NodeController@position');


Route::get('nodes/{node}/dataset', 'Web\NodeDataController@dataset');
Route::get('nodes/{node}/fields/{field}/data', 'Web\NodeDataController@fielddata');
Route::get('meta/node', 'Web\NodeDataController@metaData');
Route::get('data/node', 'Web\NodeDataController@nodedata');
Route::get('test', 'Web\NodeDataController@JobTest');

Route::get('/fetchforecast' , function(){
     Artisan::call('fetch:forecast');
     return 'OK';
 });

//Dock
Route::resource('dock', 'Web\DockController')->only([
     'index', 'store'
]);

Route::resource('nodes/{node}/fields', 'Web\FieldController');

// locale route
Route::get('lang/{locale}',[LanguageController::class, 'swap']);

Auth::routes();

