@extends('layouts.app')

@section('title', __('settings.oauth.title'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('settings.oauth.title') }}</h1>
            <p class="mt-2 text-gray-600">{{ __('settings.oauth.description') }}</p>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form method="POST" action="{{ route('admin.settings.oauth.update') }}" class="p-6 space-y-8">
                @csrf

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">GitHub</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="github_client_id">Client ID</label>
                            <input id="github_client_id" type="text" name="github[client_id]" value="{{ old('github.client_id', $github->client_id) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="github_client_secret">Client Secret</label>
                            <input id="github_client_secret" type="text" name="github[client_secret]" value="{{ old('github.client_secret', $github->client_secret) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="github_redirect">Redirect URL</label>
                            <input id="github_redirect" type="text" name="github[redirect]" value="{{ old('github.redirect', $github->redirect ?? (config('services.github.redirect') ?? '')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                    </div>
                </div>

                <hr />

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Twitter</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="twitter_client_id">Client ID</label>
                            <input id="twitter_client_id" type="text" name="twitter[client_id]" value="{{ old('twitter.client_id', $twitter->client_id) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="twitter_client_secret">Client Secret</label>
                            <input id="twitter_client_secret" type="text" name="twitter[client_secret]" value="{{ old('twitter.client_secret', $twitter->client_secret) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="twitter_redirect">Redirect URL</label>
                            <input id="twitter_redirect" type="text" name="twitter[redirect]" value="{{ old('twitter.redirect', $twitter->redirect ?? (config('services.twitter.redirect') ?? '')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Google</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="google_client_id">Client ID</label>
                            <input id="google_client_id" type="text" name="google[client_id]" value="{{ old('google.client_id', $google->client_id) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="google_client_secret">Client Secret</label>
                            <input id="google_client_secret" type="text" name="google[client_secret]" value="{{ old('google.client_secret', $google->client_secret) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="google_redirect">Redirect URL</label>
                            <input id="google_redirect" type="text" name="google[redirect]" value="{{ old('google.redirect', $google->redirect ?? (config('services.google.redirect') ?? '')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('settings.oauth.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
