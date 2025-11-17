<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Services\DebugLogger;
use App\Models\OauthSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DebugLogger::class, function ($app) {
            return new DebugLogger();
        });
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

        // OAuth 設定をDBから読み込んでサービス設定を上書き
        $oauthSettings = OauthSetting::all()->keyBy('provider');

        if ($github = $oauthSettings->get('github')) {
            config(['services.github.client_id' => $github->client_id]);
            config(['services.github.client_secret' => $github->client_secret]);
            if (!empty($github->redirect)) {
                config(['services.github.redirect' => $github->redirect]);
            }
        }

        if ($google = $oauthSettings->get('google')) {
            config(['services.google.client_id' => $google->client_id]);
            config(['services.google.client_secret' => $google->client_secret]);
            if (!empty($google->redirect)) {
                config(['services.google.redirect' => $google->redirect]);
            }
        }

        if ($twitter = $oauthSettings->get('twitter')) {
            config(['services.twitter.client_id' => $twitter->client_id]);
            config(['services.twitter.client_secret' => $twitter->client_secret]);
            if (!empty($twitter->redirect)) {
                config(['services.twitter.redirect' => $twitter->redirect]);
            }
        }

        // LivewireのURL設定をサブディレクトリ対応に修正
        if (str_contains(config('app.url'), 'PersonalManager')) {
            config(['livewire.base_url' => config('app.url')]);
        }

        // Livewireコンポーネントを登録
        Livewire::component('dashboard', \App\Http\Livewire\Dashboard::class);
        Livewire::component('settings', \App\Http\Livewire\Settings::class);
    }
}
