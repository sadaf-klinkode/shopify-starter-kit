<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\appUserController;

Route::middleware(['verify.shopify'])->group(function () {
    Route::prefix('/rules')->group(function () {
        Route::post('/', [RulesController::class, 'create_rule']);
        // Route::get('/{id}', [RulesController::class, 'show_rule']);
        Route::get('/get-rules', [RulesController::class, 'get_rules']);
        Route::delete('/delete-rule/{id}', [RulesController::class, 'delete_rule']);
        Route::get('/get-rule/{id}', [RulesController::class, 'get_rule']);

        Route::put('/update-rule/{id}', [RulesController::class, 'update_rule']);
    });

    Route::post('/store-user-data', [appUserController::class, 'getUserData']);
    Route::get('/get-user-analytics', [appUserController::class, 'getUserAnalytics']);
});





