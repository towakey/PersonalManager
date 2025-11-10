<div class="bg-white rounded-lg shadow-md p-6" wire:key="google-calendar-{{ $widget->id }}">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            {{ __('Google Calendar') }}
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
    @elseif(empty($events))
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p>{{ __('No upcoming events') }}</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($events as $event)
                <div class="border-l-4 border-blue-400 bg-blue-50 p-4 hover:bg-blue-100 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $event['summary'] }}
                            </p>
                            @if($event['start_time'])
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Start') }}: {{ $event['start_time'] }}
                                </p>
                            @endif
                            @if($event['end_time'] && $event['end_time'] !== $event['start_time'])
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('End') }}: {{ $event['end_time'] }}
                                </p>
                            @endif
                            @if($event['location'])
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Location') }}: {{ $event['location'] }}
                                </p>
                            @endif
                            @if($event['description'])
                                <p class="text-xs text-gray-600 mt-2">
                                    {{ Str::limit($event['description'], 100) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            @if($event['html_link'])
                                <a href="{{ $event['html_link'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    {{ __('View') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
