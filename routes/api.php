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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('configs', 'ConfigController@show');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('reset-password', 'AuthController@resetPassword');
});

Route::group([
    'middleware' => 'api',
], function ($router) {

    Route::get('callsigns', 'CallsignsController@index');

    Route::resource('timesheet', 'TimesheetController');
    Route::get('slot/{slot}/people', 'SlotController@people');
    Route::resource('slot', 'SlotController');

    Route::resource('person/{person}/schedule', 'PersonScheduleController', [ 'only' => [ 'index', 'store', 'destroy' ]]);
    Route::resource('person/{id}/messages', 'PersonMessageController');
    Route::patch('person/{person}/password', 'PersonController@password');
    Route::get('person/{person}/yearinfo', 'PersonController@yearInfo');
    Route::resource('person', 'PersonController', [ 'only' => [ 'index','show','store','update','destroy' ]]);
});
