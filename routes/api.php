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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'cors'], function() {
    Route::group(['prefix' => 'v1'], function() {
        Route::group(['prefix' => 'auth'], function() {
            Route::post('/register', 'AuthController@register');
            Route::post('/login', 'AuthController@login');
            Route::get('/logout', 'AuthController@logout');
        });
        Route::group(['middleware' => 'auth'], function() {
            Route::resource('board', 'BoardController', ['only' => ['store','index']]);
            Route::group(['middleware' => 'member:boardId'], function() {
                Route::resource('board', 'BoardController', ['only' => ['update', 'show', 'addMember', 'removeMember']]);
            });
            Route::delete('/board/{boardId}', 'BoardController@destroy')->middleware('creator:boardId');
        });
        Route::group(['prefix' => 'board'], function() {
            Route::group(['middleware' => 'members:boardId'], function() {
                Route::post('{boardId}/member', 'BoardController@addMember');
                Route::delete('{boardId}/member/{userId}', 'BoardController@removeMember');
                Route::resource('{boardId}/list', 'BoardListController', ['only' => ['store','update','destroy']]);
                Route::post('{boardId}/list/{listId}/right', 'BoardListController@right');
                Route::post('{boardId}/list/{listId}/left', 'BoardListController@left');
                Route::group(['prefix' => '{boardId}/list/{listId}'], function() {
                    Route::resource('card', 'CardController', ['only' => ['store', 'update','destroy']]);
                });
            });
        });
        Route::group(['prefix' => 'card/{cardId}'], function() {
            Route::group(['middleware' => 'card:cardId'], function() {
                Route::post('/up', 'CardController@up');
                Route::post('/down', 'CardController@down');
                Route::post('/move/{listId}', 'CardController@move');
            });
        });
    });
});
