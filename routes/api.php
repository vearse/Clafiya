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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['cors', 'json.response']], function () {
    
    Route::prefix('auth')->name('api.auth')->group(function(){
        Route::post('/login', 'Auth\AuthApiController@login')->name('login');
        Route::post('/register','Auth\AuthApiController@register')->name('register');
        Route::middleware('auth:api')->post('/logout', 'Auth\AuthApiController@logout')->name('logout');
    });
    
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/profile', 'Auth\AuthApiController@profile')->name('profile');
 
    });
});