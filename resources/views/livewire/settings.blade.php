<div>
    <!-- タブナビゲーション -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <button
                wire:click="setActiveTab('profile')"
                class="{{ $activeTab === 'profile' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            >
                {{ __('settings.tabs.profile') }}
            </button>
            <button
                wire:click="setActiveTab('accounts')"
                class="{{ $activeTab === 'accounts' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            >
                {{ __('settings.tabs.accounts') }}
            </button>
            <button
                wire:click="setActiveTab('sharing')"
                class="{{ $activeTab === 'sharing' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            >
                {{ __('settings.tabs.sharing') }}
            </button>
            <button
                wire:click="setActiveTab('servers')"
                class="{{ $activeTab === 'servers' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            >
                {{ __('settings.tabs.servers') }}
            </button>
        </nav>
    </div>

    <!-- 成功メッセージ -->
    @if ($showSuccessMessage)
        <div class="m-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-green-800">{{ $successMessage }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button wire:click="closeSuccessMessage" class="-mx-1.5 -my-1.5 bg-green-50 rounded-md p-1.5 inline-flex h-8 w-8 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                        <span class="sr-only">{{ __('common.close') }}</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- タブコンテンツ -->
    <div class="p-6">
        <!-- プロフィール設定 -->
        @if ($activeTab === 'profile')
            <form wire:submit.prevent="updateProfile">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">{{ __('settings.profile.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('settings.profile.description') }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('settings.profile.name') }}
                            </label>
                            <input
                                type="text"
                                id="name"
                                wire:model="name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                {{ __('settings.profile.email') }}
                            </label>
                            <input
                                type="email"
                                id="email"
                                wire:model="email"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror"
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="locale" class="block text-sm font-medium text-gray-700">
                                {{ __('settings.profile.language') }}
                            </label>
                            <select
                                id="locale"
                                wire:model="locale"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('locale') border-red-300 @enderror"
                            >
                                <option value="ja">{{ __('languages.ja') }}</option>
                                <option value="en">{{ __('languages.en') }}</option>
                            </select>
                            @error('locale')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <h3 class="text-md font-medium text-gray-900">{{ __('settings.profile.change_password') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('settings.profile.password_description') }}</p>

                        <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">
                                    {{ __('settings.profile.current_password') }}
                                </label>
                                <input
                                    type="password"
                                    id="current_password"
                                    wire:model="current_password"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('current_password') border-red-300 @enderror"
                                >
                                @error('current_password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    {{ __('settings.profile.new_password') }}
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    wire:model="password"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-300 @enderror"
                                >
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    {{ __('settings.profile.confirm_password') }}
                                </label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    wire:model="password_confirmation"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password_confirmation') border-red-300 @enderror"
                                >
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                {{ __('settings.profile.save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        <!-- アカウント連携 -->
        @if ($activeTab === 'accounts')
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ __('settings.accounts.title') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('settings.accounts.description') }}</p>
                </div>

                <div class="space-y-4">
                    @foreach ($available_services as $service)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-md font-medium text-gray-900">{{ $service['name'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $service['description'] }}</p>
                                </div>
                                <div class="ml-4">
                                    @if ($service['connected'])
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            {{ __('settings.accounts.connected') }}
                                        </span>
                                        <button
                                            wire:click="disconnectAccount('{{ $service['id'] }}')"
                                            class="ml-3 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            {{ __('settings.accounts.disconnect') }}
                                        </button>
                                    @else
                                        <a
                                            href="{{ $service['auth_url'] }}"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            {{ __('settings.accounts.connect') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- データ公開設定 -->
        @if ($activeTab === 'sharing')
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ __('settings.sharing.title') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('settings.sharing.description') }}</p>
                </div>

                <div class="space-y-4">
                    @forelse ($widgets as $widget)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-md font-medium text-gray-900">{{ $widget['type'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ __('settings.sharing.current_setting') }}: 
                                        <span class="font-medium">{{ __('settings.sharing.types.' . ($widget['sharing_setting']['sharing_type'] ?? 'private')) }}</span>
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <select
                                        wire:change="updateSharingSettings({{ $widget['id'] }}, $event.target.value)"
                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                    >
                                        <option value="private" {{ ($widget['sharing_setting']['sharing_type'] ?? 'private') === 'private' ? 'selected' : '' }}>
                                            {{ __('settings.sharing.types.private') }}
                                        </option>
                                        <option value="specific_users" {{ ($widget['sharing_setting']['sharing_type'] ?? 'private') === 'specific_users' ? 'selected' : '' }}>
                                            {{ __('settings.sharing.types.specific_users') }}
                                        </option>
                                        <option value="specific_groups" {{ ($widget['sharing_setting']['sharing_type'] ?? 'private') === 'specific_groups' ? 'selected' : '' }}>
                                            {{ __('settings.sharing.types.specific_groups') }}
                                        </option>
                                        <option value="specific_servers" {{ ($widget['sharing_setting']['sharing_type'] ?? 'private') === 'specific_servers' ? 'selected' : '' }}>
                                            {{ __('settings.sharing.types.specific_servers') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('settings.sharing.no_widgets') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('settings.sharing.no_widgets_description') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('settings.sharing.go_to_dashboard') }}
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- サーバー間連携 -->
        @if ($activeTab === 'servers')
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ __('settings.servers.title') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('settings.servers.description') }}</p>
                </div>

                <!-- サーバー追加フォーム -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('settings.servers.add_server') }}</h3>
                    <form wire:submit.prevent="addServer" class="space-y-4">
                        <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-3">
                            <div>
                                <label for="new_server_name" class="block text-sm font-medium text-gray-700">
                                    {{ __('settings.servers.name') }}
                                </label>
                                <input
                                    type="text"
                                    id="new_server_name"
                                    wire:model="new_server_name"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('new_server_name') border-red-300 @enderror"
                                >
                                @error('new_server_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="new_server_url" class="block text-sm font-medium text-gray-700">
                                    {{ __('settings.servers.url') }}
                                </label>
                                <input
                                    type="url"
                                    id="new_server_url"
                                    wire:model="new_server_url"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('new_server_url') border-red-300 @enderror"
                                >
                                @error('new_server_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="new_server_identifier" class="block text-sm font-medium text-gray-700">
                                    {{ __('settings.servers.identifier') }}
                                </label>
                                <input
                                    type="text"
                                    id="new_server_identifier"
                                    wire:model="new_server_identifier"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('new_server_identifier') border-red-300 @enderror"
                                >
                                @error('new_server_identifier')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                {{ __('settings.servers.add') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- サーバー一覧 -->
                <div class="space-y-4">
                    @forelse ($server_connections as $server)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-md font-medium text-gray-900">{{ $server['name'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $server['url'] }}</p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('settings.servers.identifier') }}: {{ $server['server_identifier'] }}
                                    </p>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if ($server['status'] === 'approved') bg-green-100 text-green-800
                                            @elseif ($server['status'] === 'pending_sent' || $server['status'] === 'pending_received') bg-yellow-100 text-yellow-800
                                            @elseif ($server['status'] === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif
                                        ">
                                            {{ __('settings.servers.status.' . $server['status']) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="ml-4 flex space-x-2">
                                    @if ($server['status'] === 'pending_received')
                                        <button
                                            wire:click="approveServer({{ $server['id'] }})"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            {{ __('settings.servers.approve') }}
                                        </button>
                                        <button
                                            wire:click="rejectServer({{ $server['id'] }})"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            {{ __('settings.servers.reject') }}
                                        </button>
                                    @endif
                                    <button
                                        wire:click="removeServer({{ $server['id'] }})"
                                        class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        {{ __('settings.servers.remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('settings.servers.no_servers') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('settings.servers.no_servers_description') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
