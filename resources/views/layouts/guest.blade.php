<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Form Teknik Informatika - Universitas Malikussaleh</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gradient-soft">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 bg-tech-pattern opacity-5"></div>

        {{-- Decorative Elements with Tech Theme --}}
        <div class="absolute top-20 left-10 w-32 h-32 bg-primary-300 rounded-full blur-3xl opacity-30 animate-pulse">
        </div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-accent-300 rounded-full blur-3xl opacity-30 animate-pulse"
            style="animation-delay: 1s;"></div>
        <div class="absolute top-1/3 right-1/4 w-24 h-24 bg-tech-purple rounded-full blur-2xl opacity-20 animate-pulse"
            style="animation-delay: 2s;"></div>

        <div class="w-full max-w-md relative z-10">
            {{-- Logo & Back Button --}}
            <div class="text-center mb-8 animate-fade-in">
                <a href="/" class="inline-flex flex-col items-center gap-2 mb-2 group">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-primary-500 via-tech-purple to-accent-600 rounded-2xl flex items-center justify-center shadow-soft-lg group-hover:scale-110 transition-transform">
                        <i class="bi bi-code-slash text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1
                            class="font-bold text-2xl bg-gradient-to-r from-primary-600 via-tech-purple to-accent-600 bg-clip-text text-transparent">
                            Teknik Informatika
                        </h1>
                        <p class="text-sm text-secondary-600">Universitas Malikussaleh</p>
                    </div>
                </a>
            </div>

            {{-- Card --}}
            <div
                class="bg-white/95 backdrop-blur-md shadow-soft-xl rounded-2xl p-8 border border-primary-100 animate-scale-in">
                {{ $slot }}
            </div>

            {{-- Footer Link --}}
            <p class="mt-6 text-center text-sm text-secondary-600 animate-fade-in" style="animation-delay: 0.2s;">
                <a href="/"
                    class="font-medium text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </p>
        </div>
    </div>

    {{-- Background Tech Pattern Style --}}
    <style>
        .bg-tech-pattern {
            background-image:
                linear-gradient(to right, rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
        }
    </style>
</body>

</html>
