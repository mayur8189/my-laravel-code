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
Route::post('login', 'ApiController@login');
Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->post('searchEvent', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['auth:api']], function () { 
    
  Route::post('searchEvent', 'ApiController@searchEvent');
  Route::post('storeStubSells', 'ApiController@storeStubSells');
  Route::post('serachper', 'ApiController@searchPermission');
  Route::post('serachincrease', 'ApiController@SerachIncrease');
  Route::post('serachcounter', 'ApiController@getSetSerach');
  Route::post('trackcheck', 'ApiController@trackEventCheck');
  Route::post('trackevent', 'ApiController@trackCheckedEvent');
  
});
