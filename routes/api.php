<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChildrenController;
use App\Http\Controllers\GrowthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/test', function () {
    return response()->json(['message' => 'Test route is working!']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::resource('users', UserController::class)
        ->except(['create', 'edit'])
        ->parameters(['users' => 'user:uuid']);

    Route::resource('children', ChildrenController::class)
        ->except(['create', 'edit'])
        ->parameters(['children' => 'child:uuid']);

    Route::resource('growths', GrowthController::class)
        ->except(['create', 'edit'])
        ->parameters(['growths' => 'growth:uuid']);

    

});





