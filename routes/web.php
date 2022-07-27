<?php

use Illuminate\Support\Facades\Route;
use Jiannius\Atom\Controllers\FileController;
use Jiannius\Atom\Controllers\TeamController;
use Jiannius\Atom\Controllers\RoleController;
use Jiannius\Atom\Controllers\UserController;
use Jiannius\Atom\Controllers\LalamoveController;

Route::prefix('lalamove')->group(function() {
    Route::post('services', [LalamoveController::class, 'getServices']);
    Route::post('quotation', [LalamoveController::class, 'getQuotation']);
    Route::post('order', [LalamoveController::class, 'createOrder']);
    Route::post('order-details', [LalamoveController::class, 'getOrderDetails']);
});

Route::prefix('app')->middleware('auth')->group(function() {
    Route::inertia('/', 'dashboard')->name('app.home');

    /**
     * Team
     */
    Route::prefix('team')->group(function () {
        Route::post('list', [TeamController::class, 'list'])->name('team.list');
        
        Route::middleware('can:team.manage')->group(function () {
            Route::get('list', [TeamController::class, 'list'])->name('team.list');
            Route::get('create', [TeamController::class, 'create'])->name('team.create');
            Route::get('update/{id}', [TeamController::class, 'update'])->name('team.update');
            Route::post('store/{id?}', [TeamController::class, 'store'])->name('team.store');
            Route::delete('/', [TeamController::class, 'delete'])->name('team.delete');
        });
    });
        
    /**
     * Role
     */
    Route::prefix('role')->middleware('can:role.manage')->group(function () {
        Route::get('list', [RoleController::class, 'list'])->name('role.list');
        Route::get('create', [RoleController::class, 'create'])->name('role.create');
        Route::get('update/{id}/{tab?}', [RoleController::class, 'update'])->name('role.update');
        Route::post('store/{id?}', [RoleController::class, 'store'])->name('role.store');
        Route::delete('/', [RoleController::class, 'delete'])->name('role.delete');
    });

    /**
     * User
     */
    Route::prefix('user')->group(function () {
        Route::match(['get', 'post'], 'account', [UserController::class, 'account'])->name('user.account');
        Route::post('list', [UserController::class, 'list'])->name('user.list');

        Route::middleware('can:user.manage')->group(function () {
            Route::get('list', [UserController::class, 'list'])->name('user.list');
            Route::post('store/{id?}', [UserController::class, 'store'])->name('user.store');
            Route::post('invite/{id}', [UserController::class, 'invite'])->name('user.invite');
            Route::delete('/', [UserController::class, 'delete'])->name('user.delete');
        });
    });

    /**
     * File
     */
    Route::prefix('file')->group(function () {
        Route::match(['get', 'post'], 'list', [FileController::class, 'list'])->name('file.list');
        Route::post('upload', [FileController::class, 'upload'])->name('file.upload');
        Route::post('store/{id}', [FileController::class, 'store'])->name('file.store');
        Route::delete('/', [FileController::class, 'delete'])->name('file.delete');
    });
});