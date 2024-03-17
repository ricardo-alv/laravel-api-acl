<?php

use App\Http\Controllers\Api\{
    Auth\AuthApiController,
    PermissionController,
    PermissionUserController,
    UserController
};
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::withoutMiddleware('auth:sanctum')->group(function () {
            Route::post('/login', [AuthApiController::class, 'auth'])->name('auth.login');
        });

        Route::get('/me', [AuthApiController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthApiController::class, 'logout'])->name('auth.logout');
    });

Route::middleware(['auth:sanctum', 'acl'])->group(function () {
    Route::apiResource('/permissions', PermissionController::class);

    Route::get('/users/{user}/permissions', [PermissionUserController::class, 'getPermissionOfUser'])->name('users.permissions');
    Route::post('/users/{user}/permissions-sync', [PermissionUserController::class, 'syncPermissionOfUser'])->name('users.permissions.sync');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

Route::get('/', fn () => response()->json(['message' => 'ok']));
