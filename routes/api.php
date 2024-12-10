<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LoginController ;

Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!'], 200);
});


Route::post('/login', [LoginController::class, 'login']);