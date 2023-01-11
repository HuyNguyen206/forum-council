<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
    @livewireStyles
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 relative">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif
    <!-- Page Content -->
    <main>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <ul class="flex flex-col space-y-4 divide-amber-700 p-4 ">
                        <li><a class="p-2 {{request()->routeIs('channels.index') ? 'bg-blue-800 text-white' :''}}" href="{{route('channels.index')}}">
                                Channels
                            </a></li>
                        <li><a class="p-2 {{request()->routeIs('channels.create') ? 'bg-blue-800 text-white': ''}}" href="{{route('channels.create')}}">
                                Create channel
                            </a></li>
                    </ul>

                    <div class="relative overflow-x-auto col-span-3">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
@stack('scripts')
@livewireScripts
</body>
</html>
