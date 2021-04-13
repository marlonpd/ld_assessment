<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\InviteUserController;

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

Route::get('loginfailed', function () {
    return response()->json(['error' => 'unauthenticated']);
})->name('loginfailed');

Route::middleware('api')->namespace('App\Http\Controllers')->group(function () {
  
    Route::post('/authenticate', [AuthController::class, 'authenticate', 'as' => 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register/confirm-pin', [AuthController::class, 'confirmPin'])->name('confirmPin');
    // Route::group([
    //     'middleware' => 'auth:api',
    // ], function ($router) {
    //     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    //     Route::post('/refresh/token', [AuthController::class, 'refresh'])->name('refresh');
        
    //     Route::post('/send/invite', [InviteUserController::class, 'sendInvite'])->name('sendInvite');        
    // });



    Route::group([
        'middleware' => 'auth:api',
    ], function ($router) {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh/token', [AuthController::class, 'refresh'])->name('refresh');
        Route::any('/update/user', [UserController::class, 'updateUser'])->name('updateUser');
        Route::post('/send/invite', [InviteUserController::class, 'sendInvite'])->name('sendInvite');   
   });
});