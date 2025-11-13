<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GitHubController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\DebugTestController;

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

// Debug test routes
Route::get('/debug/test', [DebugTestController::class, 'test']);
Route::post('/debug/clean', [DebugTestController::class, 'clean']);

// Installer routes
Route::group(['prefix' => 'install', 'middleware' => ['installed']], function () {
    Route::get('/', [InstallController::class, 'show']);
    Route::post('/', [InstallController::class, 'store']);
});
Route::get('/install/complete', [InstallController::class, 'complete']);

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/accounts', [SettingsController::class, 'updateAccounts'])->name('settings.accounts.update');
    Route::post('/settings/sharing', [SettingsController::class, 'updateSharing'])->name('settings.sharing.update');
    Route::post('/settings/servers', [SettingsController::class, 'updateServers'])->name('settings.servers.update');
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

// OAuth Authentication Routes
Route::get('/auth/github', [GitHubController::class, 'redirect'])
    ->middleware('auth')
    ->name('auth.github');

Route::get('/auth/github/callback', [GitHubController::class, 'callback'])
    ->middleware('auth')
    ->name('auth.github.callback');

Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->middleware('auth')
    ->name('auth.google');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->middleware('auth')
    ->name('auth.google.callback');
