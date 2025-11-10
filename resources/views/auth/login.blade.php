<x-guest-layout>
    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-secondary-900 mb-2">Selamat Datang Kembali!</h2>
        <p class="text-secondary-600">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" class="text-secondary-700 font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-envelope text-secondary-400"></i>
                </div>
                <x-text-input id="email"
                    class="block w-full pl-10 pr-3 py-3 border-secondary-200 rounded-lg focus:border-primary-500 focus:ring-primary-500 transition-colors"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                    placeholder="nama@mhs.unimal.ac.id" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div
                class="mt-2 flex items-start gap-2 text-sm text-secondary-600 bg-primary-50 border border-primary-200 rounded-lg p-3">
                <i class="bi bi-info-circle text-primary-600 mt-0.5"></i>
                <span>
                    <strong>Untuk Responden:</strong> Gunakan email dengan domain <strong
                        class="text-primary-700">@mhs.unimal.ac.id</strong>
                </span>
            </div>
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" class="text-secondary-700 font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-lock text-secondary-400"></i>
                </div>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-3 py-3 border-secondary-200 rounded-lg focus:border-primary-500 focus:ring-primary-500 transition-colors"
                    type="password" name="password" required autocomplete="current-password"
                    placeholder="Masukkan password Anda" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox"
                    class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 cursor-pointer transition-colors"
                    name="remember">
                <span class="ms-2 text-sm text-secondary-600 group-hover:text-secondary-900 transition-colors">
                    Ingat saya
                </span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors"
                    href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit Button --}}
        <div class="space-y-4">
            <x-primary-button
                class="w-full justify-center py-3 bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 font-semibold shadow-soft hover:shadow-soft-lg transition-all duration-200">
                <i class="bi bi-box-arrow-in-right mr-2"></i>
                Masuk
            </x-primary-button>

            {{-- Register Link --}}
            <p class="text-center text-sm text-secondary-600">
                Belum punya akun?
                <a href="{{ route('register') }}"
                    class="font-medium text-primary-600 hover:text-primary-700 transition-colors">
                    Daftar sekarang
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
