<div class="bg-white rounded-lg shadow-md p-6" wire:key="github-notifications-{{ $widget->id }}">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            {{ __('GitHub Notifications') }}
        </h3>
        <button 
            wire:click="refresh" 
            wire:loading.attr="disabled"
            class="text-blue-600 hover:text-blue-800 disabled:opacity-50"
        >
            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <svg wire:loading class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </button>
    </div>

    @if($loading)
        <div class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">{{ __('Loading...') }}</p>
        </div>
    @elseif($error)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <p class="font-medium">{{ __('Error') }}</p>
            <p class="text-sm">{{ $error }}</p>
        </div>
    @elseif(empty($notifications))
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <p>{{ __('No new notifications') }}</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="border-l-4 border-blue-400 bg-blue-50 p-4 hover:bg-blue-100 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @switch($notification['subject']['type'])
                                @case('Issue')
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    @break
                                @case('PullRequest')
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    @break
                                @default
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                            @endswitch
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $notification['subject']['title'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('Repository') }}: {{ $notification['repository']['full_name'] }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ __('Type') }}: {{ $notification['subject']['type'] }}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ $notification['subject']['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                {{ __('View') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
