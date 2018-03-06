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

Route::group(['prefix' => 'v1'], function () {
    
    Route::post('signin','Auth\LoginController@signin');
    Route::post('signup','Auth\RegisterController@signup');
    
    Route::post('moodReport','MoodController@reportMood');
    Route::get('moodSuggest','MoodController@getMoodsSuggest');
    Route::get('moodUserCount','MoodController@getMoodUserCount');
    Route::post('moodSkip','MoodController@indexSkip');
    Route::resource('mood', 'MoodController', [
        'except' => ['edit','create'] 
    ]);
    
    Route::post('user/destroyWithPassword/{id}', 'UserController@destroyWithPassword');
    Route::post('user/resetPassword','UserController@resetPassword');
    Route::resource('user', 'UserController');

    Route::resource('like', 'LikeController', [
        'only' => ['store','destroy'] 
    ]);

    Route::resource('country', 'CountryController', [
        'only' => ['index'] 
    ]);

    Route::resource('city', 'CityController', [
        'only' => ['show'] 
    ]);

    Route::resource('category', 'CategoryController',[
        'only' => ['index'] 
    ]);
});