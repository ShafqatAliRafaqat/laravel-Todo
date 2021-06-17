<?php

use App\Models\User;
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
    
    Route::get('all_users', 'APILoginController@allUsers');
    Route::get('/user', function (Request $request) {
        return User::all();
    });
    Route::POST('login', 'APILoginController@login');
    Route::post('register', 'APILoginController@register');
    Route::post('verification', 'APILoginController@codeVerification');
    Route::post('re-send', 'APILoginController@reSendCode');

    Route::group(['middleware' => 'auth:api' ], function () {
        
        Route::post('logout', 'APILoginController@logout')->name('logout');

        // ToDo
        Route::resource('todos',  'ToDoController');
        Route::post('todo/{id}',  'ToDoController@update');
    });
