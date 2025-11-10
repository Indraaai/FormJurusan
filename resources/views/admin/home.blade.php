<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">Dashboard Admin</h2>
                <p class="text-sm text-secondary-600 mt-1">Selamat datang, <span
                        class="font-semibold text-primary-600">{{ auth()->user()->name }}</span></p>
            </div>
            <div class="flex items-center gap-2 text-sm text-secondary-600">
                <i class="bi bi-calendar3"></i>
                <span>{{ now()->timezone(config('app.timezone'))->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Forms -->
                <div
                    class="bg-white rounded-xl shadow-soft border border-primary-100 p-6 hover:shadow-soft-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Forms</p>
                            <h3 class="text-3xl font-bold text-secondary-900">
                                {{ \App\Models\Form::count() }}
                            </h3>
                            <p class="text-xs text-secondary-500 mt-2">
                                <span class="text-success-600 font-semibold">
                                    {{ \App\Models\Form::where('is_published', true)->count() }}
                                </span>
                                dipublikasikan
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-file-earmark-text text-2xl text-primary-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Responses -->
                <div
                    class="bg-white rounded-xl shadow-soft border border-success-100 p-6 hover:shadow-soft-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Respons</p>
                            <h3 class="text-3xl font-bold text-secondary-900">
                                {{ \App\Models\FormResponse::count() }}
                            </h3>
                            <p class="text-xs text-secondary-500 mt-2">Dari semua form</p>
                        </div>
                        <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-chat-left-text text-2xl text-success-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Questions -->
                <div
                    class="bg-white rounded-xl shadow-soft border border-warning-100 p-6 hover:shadow-soft-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Pertanyaan</p>
                            <h3 class="text-3xl font-bold text-secondary-900">
                                {{ \App\Models\Question::count() }}
                            </h3>
                            <p class="text-xs text-secondary-500 mt-2">Dalam semua form</p>
                        </div>
                        <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-question-circle text-2xl text-warning-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div
                    class="bg-white rounded-xl shadow-soft border border-accent-100 p-6 hover:shadow-soft-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Users</p>
                            <h3 class="text-3xl font-bold text-secondary-900">
                                {{ \App\Models\User::count() }}
                            </h3>
                            <p class="text-xs text-secondary-500 mt-2">
                                <span class="text-accent-600 font-semibold">
                                    {{ \App\Models\User::where('role', 'admin')->count() }}
                                </span>
                                admin
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-people text-2xl text-accent-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-soft border border-primary-100 p-6">
                    <h3 class="font-bold text-lg text-secondary-900 mb-4 flex items-center gap-2">
                        <i class="bi bi-lightning-charge text-primary-600"></i>
                        Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        @if (Route::has('admin.forms.create'))
                            <a href="{{ route('admin.forms.create') }}"
                                class="flex items-center gap-4 p-4 rounded-lg bg-primary-50 hover:bg-primary-100 border border-primary-200 transition-all duration-200 group">
                                <div
                                    class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="bi bi-plus-lg text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-secondary-900">Buat Form Baru</h4>
                                    <p class="text-sm text-secondary-600">Mulai form baru dari awal</p>
                                </div>
                                <i
                                    class="bi bi-arrow-right text-primary-600 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @endif

                        @if (Route::has('admin.forms.index'))
                            <a href="{{ route('admin.forms.index') }}"
                                class="flex items-center gap-4 p-4 rounded-lg bg-success-50 hover:bg-success-100 border border-success-200 transition-all duration-200 group">
                                <div
                                    class="w-10 h-10 bg-success-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="bi bi-list-ul text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-secondary-900">Kelola Forms</h4>
                                    <p class="text-sm text-secondary-600">Edit, hapus, atau publish form</p>
                                </div>
                                <i
                                    class="bi bi-arrow-right text-success-600 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @endif

                        <a href="#"
                            class="flex items-center gap-4 p-4 rounded-lg bg-secondary-50 hover:bg-secondary-100 border border-secondary-200 transition-all duration-200 group">
                            <div
                                class="w-10 h-10 bg-secondary-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="bi bi-bar-chart text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-secondary-900">Lihat Statistik</h4>
                                <p class="text-sm text-secondary-600">Analisa data dan respons</p>
                            </div>
                            <i
                                class="bi bi-arrow-right text-secondary-600 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Recent Forms -->
                <div class="bg-white rounded-xl shadow-soft border border-primary-100 p-6">
                    <h3 class="font-bold text-lg text-secondary-900 mb-4 flex items-center gap-2">
                        <i class="bi bi-clock-history text-primary-600"></i>
                        Form Terbaru
                    </h3>
                    @php
                        $recentForms = \App\Models\Form::latest()->take(5)->get();
                    @endphp

                    @if ($recentForms->count() > 0)
                        <div class="space-y-3">
                            @foreach ($recentForms as $form)
                                <a href="{{ route('admin.forms.edit', $form) }}"
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-primary-50 border border-transparent hover:border-primary-200 transition-all duration-200 group">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-text text-white"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4
                                            class="font-semibold text-secondary-900 truncate group-hover:text-primary-600">
                                            {{ $form->title }}
                                        </h4>
                                        <p class="text-xs text-secondary-500">
                                            {{ $form->created_at->timezone(config('app.timezone'))->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium {{ $form->is_published ? 'bg-success-100 text-success-700' : 'bg-secondary-100 text-secondary-700' }}">
                                        {{ $form->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </a>
                            @endforeach
                        </div>

                        @if (Route::has('admin.forms.index'))
                            <a href="{{ route('admin.forms.index') }}"
                                class="block mt-4 text-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                Lihat Semua Forms →
                            </a>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <i class="bi bi-inbox text-4xl text-secondary-300"></i>
                            <p class="text-sm text-secondary-500 mt-2">Belum ada form yang dibuat</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
