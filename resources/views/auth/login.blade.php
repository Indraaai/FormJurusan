<x-guest-layout>

    {{-- Header --}}
    <div class="mb-10 text-center">
        <h2 class="text-2xl font-bold text-secondary-900 mb-2">
            Selamat Datang Kembali
        </h2>
        <p class="text-secondary-600">
            Masuk ke akun Anda untuk melanjutkan
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-7">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Email" class="text-secondary-700 font-medium mb-2" />

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-envelope text-secondary-400"></i>
                </div>

                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                    autocomplete="username" placeholder="nama@mhs.unimal.ac.id"
                    class="block w-full pl-10 pr-3 py-3 rounded-lg
                           border-secondary-300
                           focus:border-primary-500 focus:ring-primary-500" />
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            <div
                class="mt-3 flex items-start gap-2 rounded-lg
                       bg-primary-50 border border-primary-200
                       px-3 py-2 text-sm text-secondary-600">
                <i class="bi bi-info-circle text-primary-600 mt-0.5"></i>
                <span>
                    Gunakan email domain
                    <strong class="text-primary-700">@mhs.unimal.ac.id</strong>
                </span>
            </div>
        </div>

        {{-- Password --}}
        {{-- Password --}}
        <div x-data="{ show: false }">
            <x-input-label for="password" value="Password" class="text-secondary-700 font-medium mb-2" />

            <div class="relative">

                {{-- Lock icon --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-lock text-secondary-400"></i>
                </div>

                {{-- Input --}}
                <x-text-input id="password" x-ref="input" type="password" name="password" required
                    autocomplete="current-password" placeholder="Masukkan password"
                    class="block w-full pl-10 pr-12 py-3 rounded-lg
                   border-secondary-300
                   focus:border-primary-500 focus:ring-primary-500" />

                {{-- Toggle button --}}
                <button type="button"
                    @click="
                show = !show;
                $refs.input.type = show ? 'text' : 'password'
            "
                    class="absolute inset-y-0 right-0 pr-3 flex items-center
                   text-secondary-500 hover:text-primary-600 focus:outline-none">

                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </button>

            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>


        {{-- Remember + Forgot --}}
        <div class="flex items-center justify-between text-sm">

            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-secondary-300
                           text-primary-600
                           focus:ring-primary-500" />

                <span class="text-secondary-600 hover:text-secondary-900">
                    Ingat saya
                </span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="font-medium text-primary-600 hover:text-primary-700">
                    Lupa password?
                </a>
            @endif

        </div>

        {{-- Submit --}}
        <div class="space-y-5">

            <x-primary-button
                class="w-full justify-center py-3
                       bg-primary-600 hover:bg-primary-700
                       font-semibold shadow-sm hover:shadow-md transition">
                <i class="bi bi-box-arrow-in-right mr-2"></i>
                Masuk
            </x-primary-button>

            <p class="text-center text-sm text-secondary-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-700">
                    Daftar sekarang
                </a>
            </p>

        </div>

    </form>

</x-guest-layout>
