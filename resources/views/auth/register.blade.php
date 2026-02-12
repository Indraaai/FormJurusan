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
                                <div
                                    class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                                    <i class="bi bi-person-plus text-white text-3xl"></i>
                                </div>
                                <h2 class="text-3xl font-bold text-white mb-4">
                                    Bergabung Bersama Kami! 🚀
                                </h2>
                                <p class="text-primary-100 text-lg leading-relaxed">
                                    Daftar sekarang dan mulai buat form profesional dengan mudah.
                                </p>
                            </div>

                            <div class="space-y-4 mt-8">
                                <div class="flex items-start gap-3 text-white/90">
                                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                                    <span>Gratis dan mudah digunakan</span>
                                </div>
                                <div class="flex items-start gap-3 text-white/90">
                                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                                    <span>Template form yang beragam</span>
                                </div>
                                <div class="flex items-start gap-3 text-white/90">
                                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                                    <span>Analisis data otomatis</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT SIDE - Form --}}
                    <div class="lg:col-span-3 p-8 sm:p-12">
                        <div class="max-w-md mx-auto">
                            {{-- Mobile Logo --}}
                            <div class="lg:hidden text-center mb-8">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="bi bi-clipboard-data text-white text-2xl"></i>
                                </div>
                            </div>

                            {{-- Header --}}
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-secondary-900 mb-2">Buat Akun Baru</h2>
                                <p class="text-secondary-600">Lengkapi data di bawah untuk mendaftar</p>
                            </div>

                            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                                @csrf

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" value="Nama Lengkap"
                                        class="text-sm font-semibold text-secondary-700 mb-2" />
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="bi bi-person text-secondary-400 text-lg"></i>
                                        </div>
                                        <x-text-input id="name"
                                            class="block w-full pl-12 pr-4 py-3 rounded-xl border-secondary-200 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all"
                                            type="text" name="name" :value="old('name')" required autofocus
                                            autocomplete="name" placeholder="Contoh: Ahmad Fauzi" />
                                    </div>
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Email Address -->
                                <div>
                                    <x-input-label for="email" value="Email"
                                        class="text-sm font-semibold text-secondary-700 mb-2" />
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="bi bi-envelope text-secondary-400 text-lg"></i>
                                        </div>
                                        <x-text-input id="email"
                                            class="block w-full pl-12 pr-4 py-3 rounded-xl border-secondary-200 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all"
                                            type="email" name="email" :value="old('email')" required
                                            autocomplete="username" placeholder="nama@mhs.unimal.ac.id" />
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    <div
                                        class="mt-2 flex items-start gap-2 rounded-lg bg-primary-50/50 border border-primary-100 px-3 py-2 text-xs text-secondary-600">
                                        <i class="bi bi-info-circle text-primary-600 mt-0.5"></i>
                                        <span>
                                            <strong>Untuk Responden:</strong> Gunakan email kampus <strong
                                                class="text-primary-700">@mhs.unimal.ac.id</strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div x-data="{ show: false }">
                                    <x-input-label for="password" value="Password"
                                        class="text-sm font-semibold text-secondary-700 mb-2" />
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="bi bi-lock text-secondary-400 text-lg"></i>
                                        </div>
                                        <x-text-input id="password" x-ref="input" type="password" name="password"
                                            required autocomplete="new-password" placeholder="Minimal 8 karakter"
                                            class="block w-full pl-12 pr-12 py-3 rounded-xl border-secondary-200 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all" />
                                        <button type="button"
                                            @click="show = !show; $refs.input.type = show ? 'text' : 'password'"
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-secondary-400 hover:text-primary-600 transition-colors">
                                            <i class="bi text-lg" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>


                                <!-- Confirm Password -->
                                <div x-data="{ show: false }">
                                    <x-input-label for="password_confirmation" value="Konfirmasi Password"
                                        class="text-sm font-semibold text-secondary-700 mb-2" />
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="bi bi-shield-check text-secondary-400 text-lg"></i>
                                        </div>
                                        <x-text-input id="password_confirmation" x-ref="input" type="password"
                                            name="password_confirmation" required autocomplete="new-password"
                                            placeholder="Ulangi password Anda"
                                            class="block w-full pl-12 pr-12 py-3 rounded-xl border-secondary-200 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all" />
                                        <button type="button"
                                            @click="show = !show; $refs.input.type = show ? 'text' : 'password'"
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-secondary-400 hover:text-primary-600 transition-colors">
                                            <i class="bi text-lg" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>



                                {{-- Submit Button --}}
                                <div class="space-y-4 pt-2">
                                    <x-primary-button
                                        class="w-full justify-center py-3.5 bg-primary-600 hover:bg-primary-700 font-semibold text-base shadow-soft hover:shadow-soft-md transition-all duration-200">
                                        <i class="bi bi-person-plus mr-2"></i>
                                        Daftar Sekarang
                                    </x-primary-button>

                                    <div class="relative">
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="w-full border-t border-secondary-200"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-4 bg-white text-secondary-500">Atau</span>
                                        </div>
                                    </div>

                                    {{-- Login Link --}}
                                    <p class="text-center text-sm text-secondary-600">
                                        Sudah punya akun?
                                        <a href="{{ route('login') }}"
                                            class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                            Masuk di sini
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
