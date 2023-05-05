<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::namespace('Auth')->group(function () {
    Route::post('/auth', 'OAuthController@issueAccessToken');
});

Route::middleware(['auth:api'])
	->namespace('V1')
    ->group(function(){
    	Route::get('/user', 'UserController@user');
    });
