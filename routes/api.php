<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\StoreController;
use App\Http\Controllers\Group\VkGroupController;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Models\Group;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::post('/signup', fn (Request $request) => response()->json(['incoming_data' => $request->all(), 'message' => 'Registered successfully!']));
//Route::post('/api/main/getStatistics', fn (Request $request) => response()->json(['incoming_data' => $request->all(), 'message' => 'Registered successfully!']));


//Route::post('/api/main/saveProfile', [StoreController::class, 'saveProfile']);
Route::group(['middleware' => 'api', 'prefix' => 'main'] , function () {
    Route::post('/mainInfoAboutGroups', [VkGroupController::class, 'mainInfoAboutGroups']);

});


Route::middleware('api')->get('/main/profile', function (Request $request) {
   // dd($request);
   // dd($request->query('id'));
   // dd(User::find($request));
    //dd(DB::table('users')->where('id', ));
    return User::find($request->query('id'));
});


Route::group(['middleware' => 'api', 'prefix' => 'main'] , function (){
    Route::post("/saveProfile",  [StoreController::class, 'saveProfile']);
});

Route::group(['middleware' => 'api'] , function () {
    Route::post('/signup', [StoreController::class, 'signup']);
    Route::post('refresh', [StoreController::class,'refresh']);
    Route::post('me', [StoreController::class, 'me']);
    Route::post('login', [StoreController::class, 'login']);
    Route::post('logout', [StoreController::class, 'logout']);
});


/*
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});
*/


