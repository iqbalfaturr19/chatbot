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

Route::get('/', function () {
    return view('chat');
});

Route::post('/chat/start', 'ChatController@startChat');
Route::post('/chat', 'ChatController@chat');
Route::get('/chat/history/{sessionId}', 'ChatController@history');
Route::get('/chat/sessions', 'ChatController@getSessions');
Route::post('/delete-session', 'ChatController@deleteSession');