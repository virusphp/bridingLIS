<?php

// use Illuminate\Http\Request;

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

//  Route::middleware('auth:api')->get('/user', function (Request $request) {
//      return $request->user();
//  });

    Route::any('/{token}', 'Bot\TelegramBotController@index')->name('webhook');
    
 Route::group(['namespace' => 'Api', 'middleware' => ['cors']], function() {
    Route::get('lis/show/{noRm}', 'LisController@getLis');
    Route::post('lis/create', 'LisController@create');
    Route::put('lis/update/{noReg}/{noLab}', 'LisController@update');
    Route::delete('lis/delete/{noReg}/{noLab}', 'LisController@delete');

 });
