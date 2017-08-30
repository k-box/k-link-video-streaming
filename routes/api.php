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

Route::middleware('guest')->post('video.add', 'Api\VideoController@store');

Route::middleware('guest')->post('video.get', 'Api\VideoController@show');

Route::middleware('guest')->post('video.delete', 'Api\VideoController@destroy');
