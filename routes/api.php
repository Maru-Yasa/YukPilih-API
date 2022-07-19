<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => "auth"], function() {
    Route::post('/login', 'App\Http\Controllers\Api\Auth@login');
    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::post('/me', 'App\Http\Controllers\Api\Auth@me');
        Route::post('/logout', 'App\Http\Controllers\Api\Auth@logout');
        Route::post('/resetpassword', 'App\Http\Controllers\Api\Auth@resetPassword');
    });
});

Route::group(['prefix' => 'poll', 'middleware' => 'auth:sanctum'], function(){
    Route::group(['middleware' => 'admin'], function(){
        Route::delete('/{poll_id}', 'App\Http\Controllers\Api\PollController@delete');
        Route::post('/', 'App\Http\Controllers\Api\PollController@create');
    });
    Route::group(['middleware' => 'user'], function(){
        Route::post('/{poll_id}/vote/{choice_id}', 'App\Http\Controllers\Api\PollController@vote');
    });
    Route::get('/', 'App\Http\Controllers\Api\PollController@getAll');
    Route::get('/{poll_id}', 'App\Http\Controllers\Api\PollController@getById');
});

Route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function(){
    Route::group(['middleware' => 'admin'], function(){
        Route::post('/', 'App\Http\Controllers\Api\UserController@create');
        Route::get('/', 'App\Http\Controllers\Api\UserController@getAll');
        Route::get('/{user_id}', 'App\Http\Controllers\Api\UserController@getById');
        Route::put('/{user_id}', 'App\Http\Controllers\Api\UserController@update');
        Route::delete('/{user_id}', 'App\Http\Controllers\Api\UserController@delete');
    });
});

Route::group(['prefix' => 'devision', 'middleware' => 'auth:sanctum'], function(){
    Route::get('/{devision_id}', 'App\Http\Controllers\Api\DevisionController@getById');
    Route::group(['middleware' => 'admin'], function(){
        Route::post('/', 'App\Http\Controllers\Api\DevisionController@create');
        Route::get('/', 'App\Http\Controllers\Api\DevisionController@getAll');
        Route::put('/{devision_id}', 'App\Http\Controllers\Api\DevisionController@update');
        Route::delete('/', 'App\Http\Controllers\Api\DevisionController@delete');
    });
});