<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\StoreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::post('/signup', fn (Request $request) => response()->json(['incoming_data' => $request->all(), 'message' => 'Registered successfully!']));

Route::post('/signup', [StoreController::class, 'signup']);



Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

/*
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/signup', [StoreController::class, 'signup']);
});
*/


