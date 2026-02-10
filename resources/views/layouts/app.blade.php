<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #0284c7;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0369a1;
        }
    </style>
</head>

<body class="bg-primary-50">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white shadow-sm border-b border-primary-100">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main class="pb-20">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-primary-100">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-secondary-600">
                    © {{ date('Y') }}
                    <span class="font-semibold text-primary-600">
                        {{ config('app.name') }}
                    </span>. All rights reserved.
                </p>

                <div class="flex items-center gap-4 text-sm text-secondary-500">
                    <a href="#" class="hover:text-primary-600">Bantuan</a>
                    <span>•</span>
                    <a href="#" class="hover:text-primary-600">Dokumentasi</a>
                    <span>•</span>
                    <a href="#" class="hover:text-primary-600">Kontak</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
