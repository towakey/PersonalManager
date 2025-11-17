<div>
    <!-- 成功メッセージ -->
    @if($showSuccessMessage)
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded flex justify-between items-center" wire:transition>
            <span>{{ $successMessage }}</span>
            <button wire:click="closeSuccessMessage" class="text-green-700 hover:text-green-900">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- タブナビゲーション -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8">
            <button
                wire:click="setActiveTab('services')"
                class="{{ $activeTab === 'services' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-1 border-b-2 font-medium text-sm transition-colors"
            >
                {{ __('plugins.tabs.services') }}
            </button>
            <button
                wire:click="setActiveTab('widgets')"
                class="{{ $activeTab === 'widgets' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-1 border-b-2 font-medium text-sm transition-colors"
            >
                {{ __('plugins.tabs.widgets') }}
            </button>
        </nav>
    </div>

    <!-- サービスタブ -->
    @if($activeTab === 'services')
        <div class="space-y-6">
            @if(empty($available_services))
                <div class="text-center py-12 text-gray-500">
                    {{ __('plugins.no_services') }}
                </div>
            @else
                @foreach($available_services as $service)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $service['display_name'] }}</h3>
                                    @if($service['connected'])
                                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('plugins.status.connected') }}
                                        </span>
                                    @else
                                        <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ __('plugins.status.not_connected') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-gray-600 mb-4">{{ $service['description'] }}</p>
                                
                                @if(!empty($service['dependencies']))
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('plugins.dependencies') }}:</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($service['dependencies'] as $dependency)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $service['dependencies_met'] ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $dependency }}
                                                    @if(!$service['dependencies_met'])
                                                        <span class="ml-1">({{ __('plugins.status.unmet') }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center space-x-3">
                                    @if(!$service['configured'])
                                        <div class="text-sm text-amber-600 bg-amber-50 px-3 py-2 rounded-md">
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('plugins.status.not_configured') }}
                                        </div>
                                    @endif
                                    
                                    @if(!$service['configured'])
                                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                            <p class="text-sm text-blue-800 font-medium mb-2">{{ __('plugins.configuration.required') }}</p>
                                            <p class="text-xs text-blue-700 mb-2">{{ __('plugins.configuration.setup_instructions') }}</p>
                                            <code class="text-xs bg-blue-100 px-2 py-1 rounded">
                                                @if($service['identifier'] === 'github')
                                                    {{ __('plugins.configuration.github_credentials') }}
                                                @elseif($service['identifier'] === 'google')
                                                    {{ __('plugins.configuration.google_credentials') }}
                                                @endif
                                            </code>
                                        </div>
                                    @endif
                                    
                                    @if($service['connected'])
                                        <button
                                            wire:click="disconnectService('{{ $service['identifier'] }}')"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            {{ __('plugins.actions.disconnect') }}
                                        </button>
                                    @else
                                        @if($service['dependencies_met'] && $service['auth_url'])
                                            <a href="{{ $service['auth_url'] }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('plugins.actions.connect') }}
                                            </a>
                                        @else
                                            <button disabled class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                                                {{ __('plugins.actions.connect') }}
                                            </button>
                                        @endif
                                    @endif
                                    
                                    <button
                                        wire:click="showServiceDetails('{{ $service['identifier'] }}')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        {{ __('plugins.actions.details') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    <!-- ウィジェットタブ -->
    @if($activeTab === 'widgets')
        <div class="space-y-6">
            @if(empty($available_widgets))
                <div class="text-center py-12 text-gray-500">
                    {{ __('plugins.no_widgets') }}
                </div>
            @else
                @foreach($available_widgets as $widget)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $widget['display_name'] }}</h3>
                                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $widget['service_display_name'] }}
                                    </span>
                                    @if($widget['is_sharable'])
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ __('widgets.sharable') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('plugins.widget.identifier') }}:</h4>
                                    <code class="px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded">{{ $widget['identifier'] }}</code>
                                </div>

                                @if(!empty($widget['dependencies']))
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('plugins.dependencies') }}:</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($widget['dependencies'] as $dependency)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $dependency }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center space-x-3">
                                    <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('plugins.widgets.add_to_dashboard') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    <!-- サービス詳細モーダル -->
    @if($selectedService)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click="closeServiceDetails">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" wire:click.stop>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    {{ $selectedService['display_name'] }}
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">{{ __('plugins.service.identifier') }}</h4>
                                        <p class="mt-1 text-sm text-gray-500">{{ $selectedService['identifier'] }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">{{ __('plugins.service.description') }}</h4>
                                        <p class="mt-1 text-sm text-gray-500">{{ $selectedService['description'] }}</p>
                                    </div>
                                    
                                    @if(!empty($selectedService['dependencies']))
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">{{ __('plugins.dependencies') }}</h4>
                                            <div class="mt-1 flex flex-wrap gap-2">
                                                @foreach($selectedService['dependencies'] as $dependency)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $dependency }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">{{ __('plugins.service.status') }}</h4>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if($selectedService['connected'])
                                                <span class="text-green-600 font-medium">{{ __('plugins.status.connected') }}</span>
                                            @else
                                                <span class="text-gray-600 font-medium">{{ __('plugins.status.not_connected') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeServiceDetails" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('common.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
