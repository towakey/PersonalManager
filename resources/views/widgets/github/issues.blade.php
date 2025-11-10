<div class="bg-white rounded-lg shadow-md p-6" wire:key="github-issues-{{ $widget->id }}">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            {{ __('GitHub Issues') }}
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
    @elseif(empty($issues))
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p>{{ __('No issues found') }}</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($issues as $issue)
                <div class="border-l-4 @if($issue['state'] === 'open') border-green-400 @else border-gray-400 @endif bg-gray-50 p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($issue['state'] === 'open')
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $issue['title'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('Repository') }}: {{ $issue['repository_url'] }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ __('State') }}: 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($issue['state'] === 'open') bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                    {{ $issue['state'] }}
                                </span>
                            </p>
                            @if(isset($issue['labels']) && !empty($issue['labels']))
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach($issue['labels'] as $label)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #{{ $label['color'] }}20; color: #{{ $label['color'] }}">
                                            {{ $label['name'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ $issue['html_url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                {{ __('View') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
