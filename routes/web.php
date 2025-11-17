<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GitHubController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\TwitterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\DebugTestController;
use App\Http\Controllers\AdminOauthSettingsController;

// Livewire routes - 優先順位を高くするために最初に配置
Route::middleware('web')->group(function () {
    \Livewire\Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/PersonalManager/livewire/update', $handle)->name('livewire.app.update');
    });
    
    \Livewire\Livewire::setScriptRoute(function ($handle) {
        return Route::get('/PersonalManager/livewire/livewire.js', $handle)->name('livewire.app.script');
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
    
    // Admin OAuth settings routes
    Route::get('/admin/settings/oauth', [AdminOauthSettingsController::class, 'edit'])->name('admin.settings.oauth.edit');
    Route::post('/admin/settings/oauth', [AdminOauthSettingsController::class, 'update'])->name('admin.settings.oauth.update');
    
    // Plugin management routes
    Route::get('/plugins', [PluginController::class, 'index'])->name('plugins');
    Route::post('/plugins/disconnect', [PluginController::class, 'disconnectService'])->name('plugins.disconnect');
    Route::post('/plugins/toggle', [PluginController::class, 'togglePlugin'])->name('plugins.toggle');
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

Route::get('/auth/twitter', [TwitterController::class, 'redirect'])
    ->middleware('auth')
    ->name('auth.twitter');

Route::get('/auth/twitter/callback', [TwitterController::class, 'callback'])
    ->middleware('auth')
    ->name('auth.twitter.callback');
