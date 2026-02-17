<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-5xl">
            {{-- Card Container --}}
            <div class="bg-white rounded-2xl shadow-soft-xl border border-primary-100 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-5">

                    {{-- LEFT SIDE - Illustration --}}
                    <div
                        class="hidden lg:block lg:col-span-2 bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 p-12 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>

                        <div class="relative z-10 h-full flex flex-col justify-center">
                            <div class="mb-8">
                                <div class="mb-6 ">
                                    <img src="{{ asset('images.jpg') }}" alt="Logo Unimal"
                                        class="w-20 h-20 rounded-full p-2 shadow-md">
                                </div>
                                <h2 class="text-3xl font-bold text-white mb-4">
                                    Selamat Datang! 👋
                                </h2>
                                <p class="text-primary-100 text-lg leading-relaxed">
                                    Masuk untuk mengakses dashboard dan mengisi form Anda dengan mudah.
                                </p>
                            </div>

                            <div class="space-y-4 mt-8">
                                <div class="flex items-start gap-3 text-white/90">
                                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                                    <span>Lihat form yang tersedia</span>
                                </div>
                                <div class="flex items-start gap-3 text-white/90">
                                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                                    <span>Isi form dengan cepat dan mudah</span>
                                </div>
                                <div class="flex items-start gap-3 text-white/90">
                                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                                    <span>Tampilan yang user-friendly</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT SIDE - Form --}}
                    <div class="lg:col-span-3 p-8 sm:p-12">
                        <div class="max-w-md mx-auto">
                            {{-- Mobile Logo --}}
                            <div class="lg:hidden text-center mb-8">
                                <img src="{{ asset('images.jpg') }}" alt="Logo Unimal"
                                    class="w-20 h-20 object-cover rounded-full mx-auto mb-4 shadow-md">
                            </div>

                            {{-- Header --}}
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-secondary-900 mb-2">
                                    Masuk ke Akun
                                </h2>
                                <p class="text-secondary-600">
                                    Silakan masukkan kredensial Anda
                                </p>
                            </div>

                            <x-auth-session-status class="mb-6" :status="session('status')" />

                            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                                @csrf

                                {{-- Email --}}
                                <div>
                                    <x-input-label for="email" value="Email"
                                        class="text-sm font-semibold text-secondary-700 mb-2" />
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="bi bi-envelope text-secondary-400 text-lg"></i>
                                        </div>
                                        <x-text-input id="email" type="email" name="email" :value="old('email')"
                                            required autofocus autocomplete="username"
                                            placeholder="nama@mhs.unimal.ac.id"
                                            class="block w-full pl-12 pr-4 py-3 rounded-xl border-secondary-200 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all" />
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    <div
                                        class="mt-2 flex items-start gap-2 rounded-lg bg-primary-50/50 border border-primary-100 px-3 py-2 text-xs text-secondary-600">
                                        <i class="bi bi-info-circle text-primary-600 mt-0.5"></i>
                                        <span>
                                            Gunakan email domain <strong
                                                class="text-primary-700">@mhs.unimal.ac.id</strong>
                                        </span>
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div x-data="{ show: false }">
                                    <x-input-label for="password" value="Password"
                                        class="text-sm font-semibold text-secondary-700 mb-2" />
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="bi bi-lock text-secondary-400 text-lg"></i>
                                        </div>
                                        <x-text-input id="password" x-ref="input" type="password" name="password"
                                            required autocomplete="current-password" placeholder="Masukkan password"
                                            class="block w-full pl-12 pr-12 py-3 rounded-xl border-secondary-200 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all" />
                                        <button type="button"
                                            @click="show = !show; $refs.input.type = show ? 'text' : 'password'"
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-secondary-400 hover:text-primary-600 transition-colors">
                                            <i class="bi text-lg" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                {{-- Remember & Forgot --}}
                                <div class="flex items-center justify-between">
                                    <label for="remember_me"
                                        class="inline-flex items-center gap-2 cursor-pointer group">
                                        <input id="remember_me" type="checkbox" name="remember"
                                            class="rounded border-secondary-300 text-primary-600 focus:ring-2 focus:ring-primary-200 transition-all" />
                                        <span
                                            class="text-sm text-secondary-600 group-hover:text-secondary-900 transition-colors">
                                            Ingat saya
                                        </span>
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                            class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                            Lupa password?
                                        </a>
                                    @endif
                                </div>

                                {{-- Submit --}}
                                <div class="space-y-4 pt-2">
                                    <x-primary-button
                                        class="w-full justify-center py-3.5 bg-primary-600 hover:bg-primary-700 font-semibold text-base shadow-soft hover:shadow-soft-md transition-all duration-200">
                                        <i class="bi bi-box-arrow-in-right mr-2"></i>
                                        Masuk ke Dashboard
                                    </x-primary-button>

                                    <div class="relative">
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="w-full border-t border-secondary-200"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-4 bg-white text-secondary-500">Atau</span>
                                        </div>
                                    </div>

                                    <p class="text-center text-sm text-secondary-600">
                                        Belum punya akun?
                                        <a href="{{ route('register') }}"
                                            class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                            Daftar sekarang
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Back to Home Link --}}
            <div class="text-center mt-6">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-secondary-600 hover:text-primary-600 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>

</x-guest-layout>
