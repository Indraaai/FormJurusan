<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Teknik Informatika - Universitas Malikussaleh</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-slate-50 font-sans">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-white border-b border-primary-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="bi bi-code-slash text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg text-secondary-900">
                        Teknik Informatika
                    </h1>
                    <p class="text-xs text-secondary-500">
                        Universitas Malikussaleh
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition">
                        Daftar
                    </a>
                @endauth
            </div>

        </div>
    </nav>

    {{-- HERO --}}
    <section class="pt-32 pb-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            <span
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 border border-primary-200 rounded-full text-primary-700 text-sm font-medium mb-6">
                <i class="bi bi-cpu"></i>
                Platform Form Digital
            </span>

            <h2 class="text-4xl sm:text-5xl font-bold text-secondary-900 mb-6">
                Sistem Formulir<br>
                <span class="text-primary-700">Teknik Informatika</span>
            </h2>

            <p class="text-lg text-secondary-600 max-w-2xl mx-auto mb-10">
                Platform resmi untuk pengelolaan formulir digital mahasiswa Teknik Informatika
                Universitas Malikussaleh.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition">
                        <i class="bi bi-person-plus-fill"></i>
                        Daftar Mahasiswa
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                @endguest

                <a href="#features"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-white border border-primary-300 text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition">
                    <i class="bi bi-info-circle"></i>
                    Tentang Sistem
                </a>
            </div>

        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold text-secondary-900 mb-4">
                    Fitur Sistem
                </h3>
                <p class="text-secondary-600 max-w-2xl mx-auto">
                    Dirancang untuk kebutuhan administrasi mahasiswa Teknik Informatika
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                @php
                    $features = [
                        [
                            'icon' => 'bi-lightning-charge-fill',
                            'title' => 'Cepat & Efisien',
                            'desc' => 'Isi formulir dalam hitungan menit tanpa antre.',
                        ],
                        [
                            'icon' => 'bi-shield-check',
                            'title' => 'Data Aman',
                            'desc' => 'Data hanya dapat diakses oleh pihak berwenang.',
                        ],
                        [
                            'icon' => 'bi-clock-history',
                            'title' => 'Akses 24/7',
                            'desc' => 'Isi formulir kapan saja di mana saja.',
                        ],
                        [
                            'icon' => 'bi-check2-circle',
                            'title' => 'Validasi Otomatis',
                            'desc' => 'Sistem memvalidasi data secara otomatis.',
                        ],
                        [
                            'icon' => 'bi-bell-fill',
                            'title' => 'Notifikasi',
                            'desc' => 'Update status langsung realtime.',
                        ],
                        [
                            'icon' => 'bi-graph-up-arrow',
                            'title' => 'Dashboard',
                            'desc' => 'Pantau semua pengajuan dalam satu tempat.',
                        ],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div
                        class="bg-white p-8 rounded-2xl border border-primary-100 shadow-sm hover:shadow-md transition">
                        <div class="w-14 h-14 bg-primary-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="bi {{ $feature['icon'] }} text-white text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-secondary-900 mb-3">
                            {{ $feature['title'] }}
                        </h4>
                        <p class="text-secondary-600">
                            {{ $feature['desc'] }}
                        </p>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24 bg-primary-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            <h3 class="text-3xl sm:text-4xl font-bold mb-6">
                Siap Menggunakan Sistem Formulir?
            </h3>

            <p class="text-lg opacity-90 mb-10">
                Bergabunglah dengan mahasiswa Teknik Informatika dalam mengelola administrasi
                jurusan secara digital.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition">
                        <i class="bi bi-person-plus-fill"></i>
                        Daftar
                    </a>

                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 border border-white/60 text-white rounded-xl hover:bg-white/10 transition">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Masuk
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                @endguest
            </div>

        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-primary-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid md:grid-cols-3 gap-8 mb-8">

                <div>
                    <h4 class="font-semibold text-secondary-900 mb-3">
                        Teknik Informatika
                    </h4>
                    <p class="text-secondary-600 text-sm">
                        Universitas Malikussaleh
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-secondary-900 mb-3">
                        Tautan
                    </h4>
                    <ul class="space-y-2 text-sm text-secondary-600">
                        <li><a href="#features" class="hover:text-primary-600">Fitur</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-primary-600">Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-primary-600">Register</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-secondary-900 mb-3">
                        Kontak
                    </h4>
                    <p class="text-sm text-secondary-600">
                        teknikinformatika@unimal.ac.id
                    </p>
                </div>

            </div>

            <div class="pt-6 border-t border-primary-100 text-center text-sm text-secondary-500">
                © {{ date('Y') }} Teknik Informatika - Universitas Malikussaleh
            </div>

        </div>
    </footer>

</body>

</html>
