<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLivewireBaseUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Livewireリクエストの場合にベースURLを設定
        if ($request->is('livewire/*') || str_contains($request->path(), 'livewire')) {
            config(['livewire.base_url' => config('app.url')]);
        }
        
        return $next($request);
    }
}
