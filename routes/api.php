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

    Route::group(['middleware' => 'auth:api' ], function () {
        
        Route::get('logout', 'APILoginController@logout')->name('logout');

        // ToDo
        Route::resource('todos',  'ToDoController');
    });
