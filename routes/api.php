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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('update', 'Api\ProfileController@update');
    Route::get('search', 'Api\TechnicalAnswerController@search');
    Route::get('check', 'Api\TechnicalAnswerController@check');
    Route::get('csr/{technical_answer}', 'Api\TechnicalAnswerController@get');
});

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');

