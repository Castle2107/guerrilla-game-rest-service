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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Every api route looks like: ip/api/resource
// If you want to remove the prefix you can edit 
// the RouteServiceProvider class on /app/Providers/RouteServiceProvider.php

// Deletes edit and create functions
// Route::apiResource('/items', 'ItemsController');

use App\Models\Guerrilla;
use App\Http\Resources\Guerrilla as GuerrillaResource;

Route::get('/list_guerrillas', 'GuerrillaController@index');

Route::post('/inspect_guerrilla', 'GuerrillaController@show');

Route::post('/create_guerrilla', 'GuerrillaController@store');

Route::post('/guerrillas/battle_units/', 'GuerrillaController@buyBattleUnit');

Route::apiResource('/guerrillas', 'GuerrillaController');

Route::post('/buy_guerrilla', 'GuerrillaController@buyGuerrilla');

Route::post('/attack_guerrilla', 'GuerrillaController@attackGuerrilla');

Route::get('/guerrillas/{guerrilla_id}/assault_reports', 'AssaultReportController@index');

Route::get('/guerrillas/{guerrilla_id}/assault_reports/{assault_report_id}', 'AssaultReportController@show');