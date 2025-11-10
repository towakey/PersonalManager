<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Installation Complete!') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-2xl mt-10 p-8 bg-white rounded-lg shadow-md text-center">
        <h1 class="text-2xl font-bold mb-6">{{ __('Installation Complete!') }}</h1>
        <p class="mb-6">{{ __('PersonalManager has been installed successfully.') }}</p>
        <a href="{{ url('/login') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline">
            {{ __('Go to Login') }}
        </a>
    </div>
</body>
</html>
