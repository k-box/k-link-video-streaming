<?php

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

Route::get('/', 'WelcomeController@index');

// playback route with full page
Route::get('/play/{id}', 'VideoPlaybackController@show')->name('video.show');

// Playback in embed context, only player
Route::get('/embed/{id}', 'VideoEmbedController@show')->name('video.embed');

// OEmbed Endpoint route. See https://oembed.com/
Route::get('/oembed', 'OembedController@show')->name('oembed');
