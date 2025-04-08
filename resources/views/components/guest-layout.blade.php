<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Massar') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --color-primary: #4b4f29;
            --color-primary-dark: #3a3e1e;
            --color-bg: #f6efe6;
            --color-text: #717171;
            --color-light-gray: #f3f3f3;
        }
        
        body {
            background-color: var(--color-bg);
            color: var(--color-text);
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="font-sans text-gray-600 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#f6efe6]">
        <div>
            <a href="/" class="flex items-center gap-2">
                <!-- Direct reference to the logo image -->
                <img src="{{ asset('images/massar-logo.png') }}" alt="Massar Logo" class="h-20">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden rounded-xl">
            {{ $slot }}
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-[#4b4f29] hover:text-[#3a3e1e]">← Back to Home</a>
        </div>
    </div>
</body>
</html>

