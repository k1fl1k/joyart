<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joyhub - Login</title>
    @vite('resources/css/app.css')
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-b from-blue-500 to-gray-900">
<div class="relative w-full max-w-md p-8 space-y-6 bg-gray-900 rounded-lg shadow-lg">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-blue-400">joyhub</h1>
        <p class="text-gray-400">Colorize your day with pretty arts</p>
    </div>

    {{ $slot }}

    <p class="text-xs text-center text-gray-500">
        This site is protected by reCAPTCHA Enterprise and the Google Privacy Policy and Terms of Service apply.
    </p>
</div>

@php
    $currentRoute = Route::currentRouteName();
@endphp

@if ($currentRoute === 'login')
    <a href="{{ route('register') }}" class="absolute top-4 right-4 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
        Create an account
    </a>
@elseif ($currentRoute === 'register')
    <a href="{{ route('login') }}" class="absolute top-4 right-4 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
        Log in to an account
    </a>
@endif

</body>
</html>
