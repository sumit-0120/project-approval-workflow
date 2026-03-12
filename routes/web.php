<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;

Route::get('/register',[AuthController::class,'registerPage'])->name('register-page');
Route::post('/custome-register',[AuthController::class,'customeRegister'])->name('custome-register');
Route::get('/',[AuthController::class,'loginPage'])->name('login-page');
Route::post('/custome-login',[AuthController::class,'customeLogin'])->name('custome-login');

Route::middleware('auth')->group(function(){

    Route::get('/dashboard',[AuthController::class,'dashboard'])->name('dashboard');
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');

    Route::prefix('projects/')->group(function () {
        Route::get('list',[ProjectController::class,'projectList'])->name('projects.list');
        Route::get('create',[ProjectController::class,'createProject'])->name('projects.create');
        Route::post('store',[ProjectController::class,'storeProject'])->name('projects.store');
    });

    // Admin only routes
    Route::middleware('admin')->prefix('projects/')->group(function () {
        Route::post('approve/{id}',[ProjectController::class,'approve'])->name('projects.approve');
        Route::post('reject/{id}',[ProjectController::class,'reject'])->name('projects.reject');
        Route::get('history/{id}', [ProjectController::class,'history'])->name('projects.history');
        Route::post('bulk-action',[ProjectController::class,'bulkAction'])->name('projects.bulk.action');
    });

});