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

Route::get('/inspect_guerrilla/{guerrilla}', 'GuerrillaController@show');

Route::post('/create_guerrilla', 'GuerrillaController@store');

Route::post('/guerrillas/battle_units/', 'GuerrillaController@buyBattleUnit');

Route::apiResource('/guerrillas', 'GuerrillaController');