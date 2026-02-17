{{-- resources/views/layouts/navigation.blade.php --}}
<nav x-data="{ open: false }"
    class="sticky top-0 z-50 bg-white/95 backdrop-blur-lg border-b border-primary-100/50 shadow-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side - Logo & Navigation -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <img src="{{ asset('images.jpg') }}" alt="Logo Unimal"
                            class="w-10 h-10 object-contain rounded-lg shadow-soft group-hover:shadow-soft-md transition-all duration-300 group-hover:scale-105">
                        <div class="hidden sm:block">
                            <span class="font-bold text-lg text-secondary-900">
                                FormApp
                            </span>
                            <p class="text-xs text-secondary-500">Admin Panel</p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden sm:ml-10 sm:flex sm:space-x-1">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.home')" :active="request()->routeIs('admin.home')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200">
                                <i class="bi bi-house-door"></i>
                                <span>Dashboard</span>
                            </x-nav-link>
                            <x-nav-link :href="route('admin.forms.index')" :active="request()->routeIs('admin.forms.*')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>Forms</span>
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('respondent.forms.index')" :active="request()->routeIs('respondent.forms.*')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200">
                                <i class="bi bi-list-check"></i>
                                <span>Daftar Form</span>
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side - User Menu -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                @auth
                    <!-- Notifications (Optional) -->
                    <button
                        class="relative p-2 text-secondary-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200">
                        <i class="bi bi-bell text-lg"></i>
                        <span
                            class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger-500 border-2 border-white rounded-full"></span>
                    </button>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ openDropdown: false }">
                        <button @click="openDropdown = !openDropdown"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-primary-50 transition-all duration-200 group border border-transparent hover:border-primary-200">
                            <div
                                class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-soft">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <div class="text-sm font-semibold text-secondary-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-secondary-500 capitalize">{{ auth()->user()->role }}</div>
                            </div>
                            <i
                                class="bi bi-chevron-down text-xs text-secondary-400 group-hover:text-primary-600 transition-all duration-200"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="openDropdown" @click.away="openDropdown = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-64 origin-top-right rounded-xl bg-white shadow-soft-lg border border-primary-100 py-2 z-50"
                            style="display: none;">

                            <div class="px-4 py-3 border-b border-primary-100">
                                <p class="text-sm font-semibold text-secondary-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-secondary-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                <span
                                    class="inline-block mt-2 px-2.5 py-1 text-xs font-medium rounded-full bg-primary-100 text-primary-700 capitalize">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <i class="bi bi-person-circle text-base"></i>
                                    <span>Profil Saya</span>
                                </a>
                                <a href="#"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <i class="bi bi-gear text-base"></i>
                                    <span>Pengaturan</span>
                                </a>
                            </div>

                            <form method="POST" action="{{ route('logout') }}"
                                class="border-t border-primary-100 mt-1 pt-1">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-left text-danger-600 hover:bg-danger-50 transition-colors">
                                    <i class="bi bi-box-arrow-right text-base"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-medium text-secondary-700 hover:text-primary-700 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 rounded-lg shadow-soft hover:shadow-soft-md transition-all duration-200">
                        Daftar
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-secondary-600 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200">
                    <i class="bi text-xl" :class="open ? 'bi-x-lg' : 'bi-list'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95" class="sm:hidden border-t border-primary-100 bg-white"
        style="display: none;">

        @auth
            <!-- Mobile User Info -->
            <div class="pt-4 pb-3 border-b border-primary-100 px-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center text-white font-semibold shadow-soft">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-secondary-900">{{ auth()->user()->name }}</div>
                        <div class="text-sm text-secondary-500">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Links -->
            <div class="py-2 space-y-1 px-2">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.home') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors {{ request()->routeIs('admin.home') ? 'bg-primary-100 text-primary-700 font-semibold' : '' }}">
                        <i class="bi bi-house-door text-lg"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.forms.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors {{ request()->routeIs('admin.forms.*') ? 'bg-primary-100 text-primary-700 font-semibold' : '' }}">
                        <i class="bi bi-file-earmark-text text-lg"></i>
                        <span>Forms</span>
                    </a>
                @else
                    <a href="{{ route('respondent.forms.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors {{ request()->routeIs('respondent.forms.*') ? 'bg-primary-100 text-primary-700 font-semibold' : '' }}">
                        <i class="bi bi-list-check text-lg"></i>
                        <span>Daftar Form</span>
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                    <i class="bi bi-person-circle text-lg"></i>
                    <span>Profil Saya</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t border-primary-100">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-left text-danger-600 hover:bg-danger-50 transition-colors">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        @else
            <div class="py-4 space-y-2 px-4">
                <a href="{{ route('login') }}"
                    class="block px-4 py-3 rounded-lg text-center font-medium text-secondary-700 hover:bg-primary-50 transition-colors">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                    class="block px-4 py-3 rounded-lg text-center font-medium text-white bg-gradient-to-r from-primary-600 to-accent-600 shadow-soft">
                    Daftar
                </a>
            </div>
        @endauth
    </div>
</nav>
