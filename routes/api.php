<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LoginController ;




Route::post('/login', [LoginController::class, 'login']);


Route::middleware('admin.check')->group(function () {

    Route::get('/ping', function () {
        return response()->json(['message' => 'API is working!'], 200);
    });
    
    Route::post('/logout', [LoginController::class, 'logout']); 
});