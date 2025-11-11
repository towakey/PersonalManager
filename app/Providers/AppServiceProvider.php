<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        if (request()->is('install*')) {
            config(['session.driver' => 'file']);
        }

        // Livewireコンポーネントを登録
        Livewire::component('dashboard', \App\Http\Livewire\Dashboard::class);
        Livewire::component('settings', \App\Http\Livewire\Settings::class);
    }
}
