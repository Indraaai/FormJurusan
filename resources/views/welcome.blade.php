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

<body class="antialiased bg-gradient-soft font-sans">

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 bg-tech-pattern opacity-5"></div>

        {{-- Animated Tech Elements --}}
        <div class="absolute top-20 left-10 w-32 h-32 bg-primary-300 rounded-full blur-3xl opacity-30 animate-pulse">
        </div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-accent-300 rounded-full blur-3xl opacity-30 animate-pulse"
            style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-tech-purple rounded-full blur-2xl opacity-20 animate-pulse"
            style="animation-delay: 2s;"></div>

        {{-- Navigation --}}
        <nav class="absolute top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-primary-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-primary-500 via-tech-purple to-accent-500 rounded-xl flex items-center justify-center shadow-soft-lg animate-scale-in">
                            <i class="bi bi-code-slash text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1
                                class="font-bold text-xl bg-gradient-to-r from-primary-600 via-tech-purple to-accent-600 bg-clip-text text-transparent">
                                Teknik Informatika
                            </h1>
                            <p class="text-xs text-secondary-600">Universitas Malikussaleh</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-lg shadow-soft hover:shadow-soft-lg transition-all duration-200">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-lg shadow-soft hover:shadow-soft-lg transition-all duration-200">
                                Daftar Sekarang
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- Hero Content --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left Content --}}
                <div class="text-center lg:text-left space-y-8 animate-fade-in">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary-50 to-accent-50 border border-primary-200 rounded-full text-primary-700 text-sm font-medium">
                        <i class="bi bi-cpu mr-1"></i>
                        Platform Form Digital Jurusan
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-secondary-900 leading-tight">
                        Sistem Formulir <br />
                        <span
                            class="bg-gradient-to-r from-primary-600 via-tech-purple to-accent-600 bg-clip-text text-transparent">
                            Teknik Informatika
                        </span>
                    </h1>

                    <p class="text-lg text-secondary-600 max-w-2xl leading-relaxed">
                        Platform resmi untuk pengelolaan formulir digital mahasiswa Teknik Informatika Universitas
                        Malikussaleh.
                        Mudah, cepat, dan terorganisir.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200 animate-scale-in">
                                <i class="bi bi-person-plus-fill"></i>
                                Daftar Sebagai Mahasiswa
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200">
                                <i class="bi bi-speedometer2"></i>
                                Ke Dashboard
                            </a>
                        @endguest

                        <a href="#features"
                            class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-primary-700 bg-white hover:bg-primary-50 border-2 border-primary-200 rounded-xl shadow-soft hover:shadow-soft-lg transition-all duration-200">
                            <i class="bi bi-info-circle"></i>
                            Tentang Sistem
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-primary-100">
                        <div class="text-center lg:text-left">
                            <div
                                class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
                                500+</div>
                            <div class="text-sm text-secondary-600">Mahasiswa</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div
                                class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
                                50+</div>
                            <div class="text-sm text-secondary-600">Form Aktif</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div
                                class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
                                24/7</div>
                            <div class="text-sm text-secondary-600">Online</div>
                        </div>
                    </div>
                </div>

                {{-- Right Content - Illustration --}}
                <div class="relative animate-slide-up">
                    <div
                        class="relative z-10 bg-white/95 backdrop-blur-sm rounded-2xl shadow-soft-xl p-8 border border-primary-100">
                        {{-- Mock Form Preview --}}
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-6 border-b border-primary-100">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-primary-500 via-tech-purple to-accent-500 rounded-lg flex items-center justify-center shadow-soft">
                                    <i class="bi bi-file-earmark-text text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-secondary-900">Form Pendaftaran</h3>
                                    <p class="text-sm text-secondary-500">Contoh formulir mahasiswa</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                                        <i class="bi bi-person text-primary-600 mr-1"></i>
                                        Nama Lengkap
                                    </label>
                                    <div
                                        class="h-11 bg-gradient-to-r from-secondary-50 to-primary-50 rounded-lg border-2 border-secondary-200">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                                        <i class="bi bi-envelope text-primary-600 mr-1"></i>
                                        Email Mahasiswa
                                    </label>
                                    <div
                                        class="h-11 bg-gradient-to-r from-secondary-50 to-primary-50 rounded-lg border-2 border-secondary-200">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                                        <i class="bi bi-code-square text-primary-600 mr-1"></i>
                                        Minat Konsentrasi
                                    </label>
                                    <div
                                        class="h-11 bg-gradient-to-r from-secondary-50 to-primary-50 rounded-lg border-2 border-secondary-200">
                                    </div>
                                </div>
                                <button
                                    class="w-full py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white font-medium rounded-lg shadow-soft hover:shadow-soft-lg transition-all duration-200 flex items-center justify-center gap-2">
                                    <i class="bi bi-send-fill"></i>
                                    Submit Form
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Floating Code Elements --}}
                    <div
                        class="absolute -top-4 -right-4 w-16 h-16 bg-primary-200 rounded-lg blur-xl opacity-60 animate-pulse">
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-accent-200 rounded-lg blur-xl opacity-60 animate-pulse"
                        style="animation-delay: 1s;"></div>

                    {{-- Decorative Code Snippet --}}
                    <div
                        class="absolute -right-8 top-1/4 bg-secondary-900 text-accent-400 px-3 py-2 rounded-lg shadow-soft-lg text-xs font-mono hidden xl:block">
                        <div>&lt;form&gt;</div>
                        <div class="pl-4 text-primary-400">type="submit"</div>
                        <div>&lt;/form&gt;</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="relative py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section Header --}}
            <div class="text-center mb-16 animate-fade-in">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary-50 to-accent-50 border border-primary-200 rounded-full text-primary-700 text-sm font-medium mb-4">
                    <i class="bi bi-gear-fill"></i>
                    Fitur Sistem
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 mb-4">
                    Kenapa Menggunakan <span
                        class="bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">Sistem
                        Kami</span>?
                </h2>
                <p class="text-lg text-secondary-600 max-w-2xl mx-auto">
                    Platform yang dirancang khusus untuk kebutuhan administrasi mahasiswa Teknik Informatika
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div
                    class="group p-8 bg-gradient-to-br from-white to-primary-50 rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in hover:scale-105">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-soft">
                        <i class="bi bi-lightning-charge-fill text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Cepat & Efisien</h3>
                    <p class="text-secondary-600">
                        Isi formulir dalam hitungan menit tanpa perlu antre. Proses digital yang cepat dan mudah.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="group p-8 bg-gradient-to-br from-white to-primary-50 rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in hover:scale-105"
                    style="animation-delay: 0.1s;">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-soft">
                        <i class="bi bi-shield-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Data Aman</h3>
                    <p class="text-secondary-600">
                        Data mahasiswa tersimpan dengan aman dan hanya dapat diakses oleh pihak yang berwenang.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="group p-8 bg-gradient-to-br from-white to-primary-50 rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in hover:scale-105"
                    style="animation-delay: 0.2s;">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-soft">
                        <i class="bi bi-clock-history text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Akses 24/7</h3>
                    <p class="text-secondary-600">
                        Akses formulir kapan saja dan di mana saja melalui perangkat komputer atau smartphone Anda.
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div class="group p-8 bg-gradient-to-br from-white to-primary-50 rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in hover:scale-105"
                    style="animation-delay: 0.3s;">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-soft">
                        <i class="bi bi-check2-circle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Validasi Otomatis</h3>
                    <p class="text-secondary-600">
                        Sistem akan memvalidasi data secara otomatis untuk memastikan kelengkapan informasi.
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div class="group p-8 bg-gradient-to-br from-white to-primary-50 rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in hover:scale-105"
                    style="animation-delay: 0.4s;">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-soft">
                        <i class="bi bi-bell-fill text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Notifikasi Real-time</h3>
                    <p class="text-secondary-600">
                        Dapatkan notifikasi langsung setelah mengirim formulir atau ada update status.
                    </p>
                </div>

                {{-- Feature 6 --}}
                <div class="group p-8 bg-gradient-to-br from-white to-primary-50 rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in hover:scale-105"
                    style="animation-delay: 0.5s;">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-soft">
                        <i class="bi bi-graph-up-arrow text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Dashboard Mahasiswa</h3>
                    <p class="text-secondary-600">
                        Pantau semua formulir yang telah Anda isi dan status pengajuan dalam satu tempat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section class="relative py-20 bg-gradient-soft overflow-hidden">
        {{-- Background Tech Pattern --}}
        <div class="absolute inset-0 bg-tech-pattern opacity-5"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm border border-primary-200 rounded-full text-primary-700 text-sm font-medium mb-4">
                    <i class="bi bi-list-ol"></i>
                    Cara Menggunakan
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 mb-4">
                    Mudah Dalam 3 Langkah
                </h2>
                <p class="text-lg text-secondary-600 max-w-2xl mx-auto">
                    Ikuti langkah sederhana untuk mengakses dan mengisi formulir jurusan
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                {{-- Connection Lines (Desktop) --}}
                <div class="hidden md:block absolute top-24 left-1/4 right-1/4 h-0.5">
                    <div class="h-full bg-gradient-to-r from-primary-400 via-tech-purple to-accent-400"></div>
                </div>

                {{-- Step 1 --}}
                <div class="relative text-center animate-slide-up">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-500 to-accent-600 rounded-2xl text-white text-3xl font-bold shadow-soft-lg mb-6 relative z-10">
                        <i class="bi bi-1-circle-fill"></i>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 border border-primary-100 shadow-soft">
                        <h3 class="text-xl font-semibold text-secondary-900 mb-3">Daftar Akun</h3>
                        <p class="text-secondary-600">
                            Buat akun menggunakan email mahasiswa <strong
                                class="text-primary-700">@mhs.unimal.ac.id</strong>
                        </p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="relative text-center animate-slide-up" style="animation-delay: 0.2s;">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-500 to-accent-600 rounded-2xl text-white text-3xl font-bold shadow-soft-lg mb-6 relative z-10">
                        <i class="bi bi-2-circle-fill"></i>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 border border-primary-100 shadow-soft">
                        <h3 class="text-xl font-semibold text-secondary-900 mb-3">Pilih Formulir</h3>
                        <p class="text-secondary-600">
                            Lihat daftar formulir yang tersedia dan pilih sesuai kebutuhan Anda
                        </p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="relative text-center animate-slide-up" style="animation-delay: 0.4s;">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-500 to-accent-600 rounded-2xl text-white text-3xl font-bold shadow-soft-lg mb-6 relative z-10">
                        <i class="bi bi-3-circle-fill"></i>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 border border-primary-100 shadow-soft">
                        <h3 class="text-xl font-semibold text-secondary-900 mb-3">Isi & Kirim</h3>
                        <p class="text-secondary-600">
                            Lengkapi formulir dengan data yang benar dan kirimkan untuk diproses
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div
                class="relative bg-gradient-to-br from-primary-600 via-tech-purple to-accent-600 rounded-3xl p-12 shadow-soft-xl overflow-hidden">
                {{-- Background Pattern --}}
                <div class="absolute inset-0 bg-tech-pattern opacity-10"></div>

                {{-- Animated Particles --}}
                <div class="absolute top-10 left-10 w-2 h-2 bg-white rounded-full animate-ping"></div>
                <div class="absolute bottom-10 right-10 w-3 h-3 bg-white rounded-full animate-ping"
                    style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 right-20 w-2 h-2 bg-white rounded-full animate-ping"
                    style="animation-delay: 2s;"></div>

                <div class="relative z-10 space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-2">
                        <i class="bi bi-mortarboard-fill"></i>
                        Untuk Mahasiswa Teknik Informatika
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-white">
                        Siap Menggunakan Sistem Formulir?
                    </h2>
                    <p class="text-lg text-white/95 max-w-2xl mx-auto leading-relaxed">
                        Bergabunglah dengan ratusan mahasiswa Teknik Informatika yang sudah menggunakan sistem ini untuk
                        mengisi berbagai formulir jurusan dengan lebih mudah dan cepat.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                        @guest
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-primary-700 bg-white hover:bg-secondary-50 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200">
                                <i class="bi bi-person-plus-fill"></i>
                                Daftar Sebagai Mahasiswa
                            </a>
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm border-2 border-white/50 rounded-xl transition-all duration-200">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Sudah Punya Akun? Masuk
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-primary-700 bg-white hover:bg-secondary-50 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200">
                                <i class="bi bi-speedometer2"></i>
                                Buka Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gradient-to-br from-secondary-900 to-secondary-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-primary-500 via-tech-purple to-accent-500 rounded-xl flex items-center justify-center shadow-soft">
                            <i class="bi bi-code-slash text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Teknik Informatika</h3>
                            <p class="text-xs text-secondary-400">Universitas Malikussaleh</p>
                        </div>
                    </div>
                    <p class="text-secondary-400 mb-4 leading-relaxed">
                        Platform resmi untuk pengelolaan formulir digital mahasiswa Teknik Informatika.
                        Dikembangkan untuk memudahkan administrasi dan meningkatkan efisiensi layanan jurusan.
                    </p>
                    <div class="flex gap-3">
                        <a href="#"
                            class="w-10 h-10 bg-secondary-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-secondary-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-secondary-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-secondary-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="bi bi-globe"></i>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                        <i class="bi bi-link-45deg text-primary-400"></i>
                        Tautan Cepat
                    </h3>
                    <ul class="space-y-2">
                        <li><a href="#features"
                                class="text-secondary-400 hover:text-primary-400 transition-colors flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs"></i> Fitur Sistem
                            </a></li>
                        <li><a href="{{ route('login') }}"
                                class="text-secondary-400 hover:text-primary-400 transition-colors flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs"></i> Login Mahasiswa
                            </a></li>
                        <li><a href="{{ route('register') }}"
                                class="text-secondary-400 hover:text-primary-400 transition-colors flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs"></i> Registrasi
                            </a></li>
                        <li><a href="#"
                                class="text-secondary-400 hover:text-primary-400 transition-colors flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs"></i> Panduan Penggunaan
                            </a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                        <i class="bi bi-envelope text-primary-400"></i>
                        Kontak
                    </h3>
                    <ul class="space-y-3 text-secondary-400">
                        <li class="flex items-start gap-2">
                            <i class="bi bi-geo-alt mt-1 text-primary-400"></i>
                            <span class="text-sm">Jl. Cot Tengku Nie, Reuleut, Kec. Muara Batu, Aceh Utara</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="bi bi-envelope text-primary-400"></i>
                            <span class="text-sm">teknikinformatika@unimal.ac.id</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="bi bi-phone text-primary-400"></i>
                            <span class="text-sm">+62 xxx xxxx xxxx</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-t border-secondary-700 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-secondary-400 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} Teknik Informatika - Universitas Malikussaleh. All rights reserved.
                </p>
                <div class="flex gap-6 text-sm">
                    <a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Kebijakan
                        Privasi</a>
                    <a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Syarat &
                        Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Background Tech Pattern Style --}}
    <style>
        .bg-tech-pattern {
            background-image:
                linear-gradient(to right, rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>
</body>

</html>
