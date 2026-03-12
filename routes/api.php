<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\AuthController;



Route::post('/login',[AuthController::class,'customeLogin']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/projects',[ProjectApiController::class,'storeProject']);
    Route::patch('/projects/{id}/approve',[ProjectApiController::class,'approveProject']);
});




