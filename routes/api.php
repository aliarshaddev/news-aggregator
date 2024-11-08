<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
   
Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login')->name('login');
    Route::post('reset-password', 'resetPassword')->name('password.reset');
    Route::post('logout', 'logout');
});
Route::middleware('auth:sanctum')->group(function() {
    Route::controller(AuthController::class)->group(function() {
        Route::post('logout', 'logout');
    });
});