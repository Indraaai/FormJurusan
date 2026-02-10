<x-guest-layout>
    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-secondary-900 mb-2">Buat Akun Baru</h2>
        <p class="text-secondary-600">Lengkapi data di bawah untuk mendaftar</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" class="text-secondary-700 font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-person text-secondary-400"></i>
                </div>
                <x-text-input id="name"
                    class="block w-full pl-10 pr-3 py-3 border-secondary-200 rounded-lg focus:border-primary-500 focus:ring-primary-500 transition-colors"
                    type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                    placeholder="Contoh: Ahmad Fauzi" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" class="text-secondary-700 font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-envelope text-secondary-400"></i>
                </div>
                <x-text-input id="email"
                    class="block w-full pl-10 pr-3 py-3 border-secondary-200 rounded-lg focus:border-primary-500 focus:ring-primary-500 transition-colors"
                    type="email" name="email" :value="old('email')" required autocomplete="username"
                    placeholder="nama@mhs.unimal.ac.id" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div
                class="mt-2 flex items-start gap-2 text-sm text-secondary-600 bg-primary-50 border border-primary-200 rounded-lg p-3">
                <i class="bi bi-info-circle text-primary-600 mt-0.5"></i>
                <span>
                    <strong>Untuk Responden:</strong> Gunakan email kampus dengan domain <strong
                        class="text-primary-700">@mhs.unimal.ac.id</strong>
                </span>
            </div>
        </div>

        <!-- Password -->
        <!-- Password -->
        <div x-data="{ show: false }">
            <x-input-label for="password" value="Password" class="text-secondary-700 font-medium mb-2" />

            <div class="relative">
                {{-- Lock icon --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-lock text-secondary-400"></i>
                </div>

                {{-- Input --}}
                <x-text-input id="password" x-ref="input" type="password" name="password" required
                    autocomplete="new-password" placeholder="Minimal 8 karakter"
                    class="block w-full pl-10 pr-12 py-3 rounded-lg
                   border-secondary-300
                   focus:border-primary-500 focus:ring-primary-500" />

                {{-- Toggle --}}
                <button type="button"
                    @click="
                show = !show;
                $refs.input.type = show ? 'text' : 'password'
            "
                    class="absolute inset-y-0 right-0 pr-3 flex items-center
                   text-secondary-500 hover:text-primary-600">

                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>


        <!-- Confirm Password -->
        <div x-data="{ show: false }">
            <x-input-label for="password_confirmation" value="Konfirmasi Password"
                class="text-secondary-700 font-medium mb-2" />

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-shield-check text-secondary-400"></i>
                </div>

                <x-text-input id="password_confirmation" x-ref="input" type="password" name="password_confirmation"
                    required autocomplete="new-password" placeholder="Ulangi password Anda"
                    class="block w-full pl-10 pr-12 py-3 rounded-lg
                   border-secondary-300
                   focus:border-primary-500 focus:ring-primary-500" />

                <button type="button"
                    @click="
                show = !show;
                $refs.input.type = show ? 'text' : 'password'
            "
                    class="absolute inset-y-0 right-0 pr-3 flex items-center
                   text-secondary-500 hover:text-primary-600">

                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>



        {{-- Submit Button --}}
        <div class="space-y-4">
            <x-primary-button
                class="w-full justify-center py-3
                       bg-primary-600 hover:bg-primary-700
                       font-semibold shadow-sm hover:shadow-md transition">
                <i class="bi bi-box-arrow-in-right mr-2"></i>
                Daftar Sekarang
            </x-primary-button>

            {{-- Login Link --}}
            <p class="text-center text-sm text-secondary-600">
                Sudah punya akun?
                <a href="{{ route('login') }}"
                    class="font-medium text-primary-600 hover:text-primary-700 transition-colors">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
