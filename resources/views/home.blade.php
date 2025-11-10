<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Home') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-2xl mt-10 p-8 bg-white rounded-lg shadow-md text-center">
        <h1 class="text-2xl font-bold mb-6">{{ __('Welcome to PersonalManager!') }}</h1>
        <p class="mb-6">{{ __('You are logged in.') }}</p>
        <div class="space-x-4">
            <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline inline-block">
                {{ __('Dashboard') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline">
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </div>
</body>
</html>
