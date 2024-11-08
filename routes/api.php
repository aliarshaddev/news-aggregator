<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\UserPreferenceController;
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
    Route::controller(ArticleController::class)->group(function() {
        Route::get('articles', 'index');
        Route::get('articles/{id}', 'show');
    });
    Route::controller(CategoryController::class)->group(function() {
        Route::get('categories', 'index');
    });
    Route::controller(SourceController::class)->group(function() {
        Route::get('sources', 'index');
        Route::post('sources', 'store');
        Route::get('sources/{id}', 'show');
    });
    Route::controller(SourceController::class)->group(function() {
        Route::get('authors', 'index');
    });
    Route::controller(UserPreferenceController::class)->group(function() {
        Route::post('/user/preferences','setPreferences');
        Route::get('/user/preferences', 'getPreferences');
        Route::get('/user/personalized-feed', 'personalizedFeed');
    });
});