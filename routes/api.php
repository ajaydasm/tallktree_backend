<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LoginController ;
use  App\Http\Controllers\Admin\PlanController;




Route::post('/login', [LoginController::class, 'login']);


Route::middleware('admin.check')->group(function () {

    Route::get('/ping', function () {
        return response()->json(['message' => 'API is working!'], 200);
    });

    Route::get('plans', [PlanController::class, 'index']); 
    Route::post('add-plan', [PlanController::class, 'store']);
    Route::get('plan/{id}', [PlanController::class, 'show']);
    Route::post('update-plan/{id}', [PlanController::class, 'update']);
    Route::post('delete-plan', [PlanController::class, 'destroy']);
    Route::post('/update-status', [PlanController::class, 'updateStatus']);
    
    Route::post('/logout', [LoginController::class, 'logout']); 
});