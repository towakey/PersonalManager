<div class="bg-white rounded-lg shadow-md p-6" wire:key="google-gmail-{{ $widget->id }}">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            {{ __('Gmail') }}
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
    @elseif(empty($messages))
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <p>{{ __('No new messages') }}</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($messages as $message)
                <div class="border-l-4 border-red-400 bg-red-50 p-4 hover:bg-red-100 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ Str::limit($message['subject'], 80) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('From') }}: {{ $message['from'] }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $message['date'] }}
                            </p>
                            <p class="text-xs text-gray-600 mt-2">
                                {{ Str::limit($message['snippet'], 120) }}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="https://mail.google.com/mail/u/0/#inbox/{{ $message['id'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                {{ __('Open') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
