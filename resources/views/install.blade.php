<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('PersonalManager Installer') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-2xl mt-10 p-8 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">{{ __('PersonalManager Installer') }}</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/install') }}" method="POST">
            @csrf

            <!-- Server Requirements -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">{{ __('Server Requirements') }}</h2>
                <ul class="list-disc list-inside">
                    @foreach ($requirements as $key => $value)
                        <li class="{{ $value ? 'text-green-600' : 'text-red-600' }}">
                            {{ $key }}
                            @if ($value)
                                <span class="float-right">✔</span>
                            @else
                                <span class="float-right">✘</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Directory Permissions -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">{{ __('Directory Permissions') }}</h2>
                <ul class="list-disc list-inside">
                     @foreach ($permissions as $key => $value)
                        <li class="{{ $value ? 'text-green-600' : 'text-red-600' }}">
                            {{ $key }}
                            @if ($value)
                                <span class="float-right">✔</span>
                            @else
                                <span class="float-right">✘</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Database Settings -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">{{ __('Database Settings') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="db_host" class="block text-sm font-medium text-gray-700">{{ __('Host') }}</label>
                        <input type="text" name="db_host" id="db_host" value="127.0.0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="db_port" class="block text-sm font-medium text-gray-700">{{ __('Port') }}</label>
                        <input type="text" name="db_port" id="db_port" value="3306" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="db_database" class="block text-sm font-medium text-gray-700">{{ __('Database Name') }}</label>
                        <input type="text" name="db_database" id="db_database" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="db_username" class="block text-sm font-medium text-gray-700">{{ __('Username') }}</label>
                        <input type="text" name="db_username" id="db_username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="db_password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                        <input type="password" name="db_password" id="db_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                </div>
            </div>

            <!-- Admin User -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">{{ __('Admin User') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                        <input type="text" name="admin_name" id="admin_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <input type="email" name="admin_email" id="admin_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="admin_password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                        <input type="password" name="admin_password" id="admin_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                        <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline" @if(!$all_checks_passed) disabled @endif>
                {{ __('Install') }}
            </button>
        </form>
    </div>
</body>
</html>
