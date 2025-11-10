<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FormJurusan') }} - Platform Form Digital</title>
    
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
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        
        {{-- Navigation --}}
        <nav class="absolute top-0 left-0 right-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center shadow-soft">
                            <i class="bi bi-ui-checks-grid text-white text-2xl"></i>
                        </div>
                        <span class="font-bold text-2xl bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
                            FormJurusan
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-lg shadow-soft hover:shadow-soft-lg transition-all duration-200">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-lg shadow-soft hover:shadow-soft-lg transition-all duration-200">
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
                    <div class="inline-block px-4 py-2 bg-primary-50 border border-primary-200 rounded-full text-primary-700 text-sm font-medium">
                        <i class="bi bi-stars mr-2"></i>
                        Platform Form Digital Terpercaya
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-secondary-900 leading-tight">
                        Buat & Kelola <br/>
                        <span class="bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
                            Form Digital
                        </span> <br/>
                        Dengan Mudah
                    </h1>
                    
                    <p class="text-lg text-secondary-600 max-w-2xl">
                        Platform lengkap untuk membuat formulir digital, mengumpulkan data, dan mengelola respons dengan antarmuka yang intuitif dan mudah digunakan.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200 animate-scale-in">
                                <i class="bi bi-rocket-takeoff"></i>
                                Mulai Gratis
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200">
                                <i class="bi bi-speedometer2"></i>
                                Ke Dashboard
                            </a>
                        @endguest
                        
                        <a href="#features" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-primary-700 bg-white hover:bg-primary-50 border-2 border-primary-200 rounded-xl shadow-soft hover:shadow-soft-lg transition-all duration-200">
                            <i class="bi bi-play-circle"></i>
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                    
                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-primary-100">
                        <div class="text-center lg:text-left">
                            <div class="text-3xl font-bold text-primary-600">100+</div>
                            <div class="text-sm text-secondary-600">Pengguna Aktif</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-3xl font-bold text-primary-600">500+</div>
                            <div class="text-sm text-secondary-600">Form Dibuat</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-3xl font-bold text-primary-600">5K+</div>
                            <div class="text-sm text-secondary-600">Respons Terkumpul</div>
                        </div>
                    </div>
                </div>
                
                {{-- Right Content - Illustration --}}
                <div class="relative animate-slide-up">
                    <div class="relative z-10 bg-white rounded-2xl shadow-soft-xl p-8 border border-primary-100">
                        {{-- Mock Form Preview --}}
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-6 border-b border-primary-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-600 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-file-earmark-text text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-secondary-900">Form Pendaftaran</h3>
                                    <p class="text-sm text-secondary-500">Contoh formulir digital</p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 mb-2">Nama Lengkap</label>
                                    <div class="h-10 bg-secondary-50 rounded-lg border border-secondary-200"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 mb-2">Email</label>
                                    <div class="h-10 bg-secondary-50 rounded-lg border border-secondary-200"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 mb-2">Pilih Jurusan</label>
                                    <div class="h-10 bg-secondary-50 rounded-lg border border-secondary-200"></div>
                                </div>
                                <button class="w-full py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white font-medium rounded-lg shadow-soft">
                                    Kirim Form
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Floating Elements --}}
                    <div class="absolute -top-6 -right-6 w-20 h-20 bg-accent-200 rounded-full blur-2xl opacity-60"></div>
                    <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-primary-200 rounded-full blur-2xl opacity-60"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="relative py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section Header --}}
            <div class="text-center mb-16 animate-fade-in">
                <div class="inline-block px-4 py-2 bg-primary-50 border border-primary-200 rounded-full text-primary-700 text-sm font-medium mb-4">
                    <i class="bi bi-sparkles mr-2"></i>
                    Fitur Unggulan
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 mb-4">
                    Kenapa Memilih <span class="text-primary-600">FormJurusan</span>?
                </h2>
                <p class="text-lg text-secondary-600 max-w-2xl mx-auto">
                    Platform yang dirancang khusus untuk memudahkan pembuatan dan pengelolaan formulir digital
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="group p-8 bg-gradient-soft rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-lightning-charge text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Mudah & Cepat</h3>
                    <p class="text-secondary-600">
                        Buat formulir dalam hitungan menit dengan antarmuka drag-and-drop yang intuitif
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="group p-8 bg-gradient-soft rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in" style="animation-delay: 0.1s;">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-shield-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Aman & Terpercaya</h3>
                    <p class="text-secondary-600">
                        Data responden tersimpan dengan aman menggunakan enkripsi standar industri
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="group p-8 bg-gradient-soft rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in" style="animation-delay: 0.2s;">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-graph-up-arrow text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Analisis Real-time</h3>
                    <p class="text-secondary-600">
                        Pantau dan analisis respons formulir secara real-time dengan dashboard interaktif
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div class="group p-8 bg-gradient-soft rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in" style="animation-delay: 0.3s;">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-palette text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Kustomisasi Penuh</h3>
                    <p class="text-secondary-600">
                        Sesuaikan tampilan form dengan brand Anda menggunakan berbagai tema dan warna
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div class="group p-8 bg-gradient-soft rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in" style="animation-delay: 0.4s;">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-phone text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Responsif Mobile</h3>
                    <p class="text-secondary-600">
                        Akses dan isi formulir dari perangkat apa pun, kapan saja dan di mana saja
                    </p>
                </div>

                {{-- Feature 6 --}}
                <div class="group p-8 bg-gradient-soft rounded-2xl border border-primary-100 hover:shadow-soft-lg transition-all duration-300 animate-scale-in" style="animation-delay: 0.5s;">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-people text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Kolaborasi Tim</h3>
                    <p class="text-secondary-600">
                        Kelola formulir bersama tim dengan sistem role dan permission yang fleksibel
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section class="relative py-20 bg-gradient-soft">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block px-4 py-2 bg-primary-50 border border-primary-200 rounded-full text-primary-700 text-sm font-medium mb-4">
                    <i class="bi bi-question-circle mr-2"></i>
                    Cara Kerja
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 mb-4">
                    Mulai Dalam 3 Langkah Mudah
                </h2>
                <p class="text-lg text-secondary-600 max-w-2xl mx-auto">
                    Proses yang sederhana dan jelas untuk membuat formulir digital Anda
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                {{-- Connection Lines (Desktop) --}}
                <div class="hidden md:block absolute top-20 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-primary-300 via-accent-400 to-primary-300"></div>

                {{-- Step 1 --}}
                <div class="relative text-center animate-slide-up">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-600 rounded-full text-white text-2xl font-bold shadow-soft-lg mb-6 relative z-10">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Daftar Akun</h3>
                    <p class="text-secondary-600">
                        Buat akun gratis Anda dalam hitungan detik menggunakan email aktif
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="relative text-center animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-600 rounded-full text-white text-2xl font-bold shadow-soft-lg mb-6 relative z-10">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Buat Formulir</h3>
                    <p class="text-secondary-600">
                        Pilih template atau buat dari awal dengan berbagai jenis pertanyaan
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="relative text-center animate-slide-up" style="animation-delay: 0.4s;">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-600 rounded-full text-white text-2xl font-bold shadow-soft-lg mb-6 relative z-10">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-900 mb-3">Bagikan & Analisis</h3>
                    <p class="text-secondary-600">
                        Bagikan link formulir dan pantau respons secara real-time
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-gradient-to-br from-primary-600 to-accent-600 rounded-3xl p-12 shadow-soft-xl relative overflow-hidden">
                {{-- Background Pattern --}}
                <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
                
                <div class="relative z-10 space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white">
                        Siap Membuat Formulir Digital?
                    </h2>
                    <p class="text-lg text-white/90 max-w-2xl mx-auto">
                        Bergabunglah dengan ratusan pengguna yang sudah menggunakan FormJurusan untuk mengumpulkan data dengan lebih efisien
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-primary-700 bg-white hover:bg-secondary-50 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200">
                                <i class="bi bi-rocket-takeoff"></i>
                                Mulai Gratis Sekarang
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-primary-700 bg-white hover:bg-secondary-50 rounded-xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-200">
                                <i class="bi bi-speedometer2"></i>
                                Ke Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-secondary-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-ui-checks-grid text-white text-xl"></i>
                        </div>
                        <span class="font-bold text-xl">FormJurusan</span>
                    </div>
                    <p class="text-secondary-400 mb-4">
                        Platform formulir digital yang memudahkan pembuatan, pengelolaan, dan analisis data responden.
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-secondary-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-secondary-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-secondary-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="font-semibold text-lg mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-secondary-400 hover:text-primary-400 transition-colors">Fitur</a></li>
                        <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Harga</a></li>
                        <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Tutorial</a></li>
                        <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">FAQ</a></li>
                    </ul>
                </div>

                {{-- Support --}}
                <div>
                    <h3 class="font-semibold text-lg mb-4">Dukungan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Bantuan</a></li>
                        <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Kontak</a></li>
                        <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-secondary-400 hover:text-primary-400 transition-colors">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-secondary-800 pt-8 text-center text-secondary-400">
                <p>&copy; {{ date('Y') }} FormJurusan. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Background Grid Pattern Style --}}
    <style>
        .bg-grid-pattern {
            background-image: 
                linear-gradient(to right, rgba(34, 197, 94, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(34, 197, 94, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</body>
</html>
