<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;

// Livewire routes
Route::middleware('web')->group(function () {
    \Livewire\Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle);
    });
    
    \Livewire\Livewire::setScriptRoute(function ($handle) {
        return Route::get('/livewire/livewire.js', $handle);
    });
});

// Debug route
Route::post('/debug/check-password', [DebugController::class, 'checkPassword']);

// Installer routes
Route::group(['prefix' => 'install', 'middleware' => ['installed']], function () {
    Route::get('/', [InstallController::class, 'show']);
    Route::post('/', [InstallController::class, 'store']);
});
Route::get('/install/complete', [InstallController::class, 'complete']);

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Authentication Routes
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
