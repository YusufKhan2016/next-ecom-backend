<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('/auth')->group(function () {

    // ===============================================
    //      authentication management
    // ===============================================

    Route::get('/get-user', [AuthController::class, 'getUser']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // ==============================================
    //      role management
    // ==============================================

    Route::get('/get-roles-list', [RoleController::class, 'index']);
    Route::post('/save-update-role', [RoleController::class, 'store']);
    Route::get('/get-role-by-id/{role}', [RoleController::class, 'show']);
    Route::delete('/delete-role/{id}', [RoleController::class, 'destroy']);

    // =============================================
    //      Permission management
    // =============================================

    Route::get('/get-permissions-list', [PermissionController::class, 'index']);

    // ============================================
    //      user management
    // ============================================

    Route::get('/get-users-list', [UserController::class, 'index']);
    Route::post('/save-update-user', [UserController::class, 'store']);
    Route::get('/get-user-by-id/{id}', [UserController::class, 'show']);
    Route::delete('/delete-user/{id}', [UserController::class, 'destroy']);
});
