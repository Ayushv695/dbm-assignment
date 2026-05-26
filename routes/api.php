<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'profile']);

    Route::middleware('role:admin,manager')->group(function () {

        Route::prefix('project')->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('store', [ProjectController::class, 'store']);
            Route::get('view/{project_id}', [ProjectController::class, 'show']);
            Route::put('update/{project_id}', [ProjectController::class, 'update']);
            Route::delete('delete/{project_id}', [ProjectController::class, 'destroy']);
        });

        Route::prefix('task')->group(function () {
            Route::post('store', [TaskController::class, 'store']);
            Route::put('update/{task_id}', [TaskController::class, 'update']);
            Route::delete('delete/{task_id}', [TaskController::class, 'destroy']);
        });

        Route::get('/dashboard/analytics', [DashboardController::class, 'analytics']);

    });

    Route::prefix('task')->group(function () {
        Route::get('/', [TaskController::class,'index']);
        Route::get('view/{task_id}', [TaskController::class,'show']);
    });

});
