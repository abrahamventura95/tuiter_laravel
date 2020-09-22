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

//Auth
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

//User
Route::group([
    'prefix' => 'user'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('all', 'UserController@users');
        Route::get('{id}', 'UserController@show');
        Route::put('{id}', 'UserController@edit');
        Route::delete('{id}', 'UserController@delete');
    });
});

//Tuit
Route::group([
    'prefix' => 'tuit'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
    	Route::get('timeline','TuitController@get');
        Route::post('', 'TuitController@create');
        Route::get('', 'TuitController@getMine');
        Route::get('{id}', 'TuitController@show');
        Route::delete('{id}', 'TuitController@delete');
    });
});

//Like
Route::group([
    'prefix' => 'like'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('', 'TuitController@createLike');
        Route::get('', 'TuitController@getLikes');
        Route::delete('{id}', 'TuitController@deleteLike');
    });
});

//Block
Route::group([
    'prefix' => 'block'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('', 'TuitController@createBlock');
        Route::get('', 'TuitController@getBlocks');
        Route::delete('{id}', 'TuitController@deleteBlock');
    });
});

//Follows
Route::group([
    'prefix' => 'follow'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('', 'TuitController@createFollow');
        Route::put('{id}','TuitController@acceptFollow');
        Route::get('{id}', 'TuitController@getFollows');
        Route::delete('{id}', 'TuitController@deleteFollow');
    });
});