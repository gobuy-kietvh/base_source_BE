<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum', 'token_expiration'])->group(function () {
    Route::get('/test', function () {
        return "welcome to laravel";
    });
});
