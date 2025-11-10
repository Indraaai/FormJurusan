<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl leading-tight text-gray-900">
                    Edit Form
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $form->title ?: 'Tanpa Judul' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Preview khusus admin (read-only) --}}
                <a href="{{ route('admin.forms.preview', $form) }}" rel="noopener"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview
                </a>

                {{-- Link publik (opsional, untuk cek cepat) --}}
                <a href="{{ route('forms.start', $form) }}" rel="noopener" target="_blank"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Link Publik
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Flash status --}}
            @if (session('status'))
                <div class="mb-6 rounded-lg border-l-4 border-emerald-500 bg-emerald-50 p-4 shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-emerald-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-emerald-800">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4 shadow-sm">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 mb-2">Periksa input berikut:</h3>
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Sidebar kiri - Meta info & Quick Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Meta Info Card --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Informasi Form
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            {{-- UID --}}
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Form
                                    UID</label>
                                <div class="flex items-center gap-2">
                                    <code id="form-uid"
                                        class="flex-1 text-xs font-mono bg-gray-50 px-3 py-2 rounded border border-gray-200 text-gray-700">{{ $form->uid }}</code>
                                    <button type="button" id="copy-uid"
                                        class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-2 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Copy
                                    </button>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Status</label>
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium
                                    {{ $form->is_published ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                                    <span
                                        class="w-2 h-2 rounded-full {{ $form->is_published ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $form->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>

                            {{-- Timestamps --}}
                            <div class="pt-4 border-t border-gray-100 space-y-2 text-xs text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Dibuat: <strong
                                            class="text-gray-700">{{ $form->created_at?->format('d M Y, H:i') ?? '-' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Diubah: <strong
                                            class="text-gray-700">{{ $form->updated_at?->format('d M Y, H:i') ?? '-' }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions Card --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Aksi Cepat
                            </h3>
                        </div>
                        <div class="p-4 space-y-2">
                            <a href="{{ route('admin.forms.sections.index', $form) }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-emerald-300 transition-all duration-200 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Kelola
                                    Sections</span>
                            </a>

                            <a href="{{ route('admin.forms.questions.index', $form) }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-emerald-300 transition-all duration-200 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Kelola
                                    Questions</span>
                            </a>

                            <a href="{{ route('admin.forms.logic.index', $form) }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-emerald-300 transition-all duration-200 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Kelola
                                    Logic</span>
                            </a>

                            <a href="{{ route('admin.forms.responses.index', $form) }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-emerald-300 transition-all duration-200 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Lihat
                                    Responses</span>
                            </a>

                            <a href="{{ route('admin.forms.settings.edit', $form) }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-emerald-300 transition-all duration-200 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Form
                                    Settings</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Main Content - Form Edit --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

                        {{-- Main Content - Form Edit --}}
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div
                                    class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Informasi Form
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">Perbarui judul dan deskripsi form Anda</p>
                                </div>

                                <form id="update-form" method="POST"
                                    action="{{ route('admin.forms.update', $form) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="p-6 space-y-6">
                                        {{-- Judul --}}
                                        <div>
                                            <label for="title"
                                                class="block text-sm font-semibold text-gray-900 mb-2">
                                                Judul Form <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="title" name="title" required
                                                value="{{ old('title', $form->title) }}"
                                                placeholder="Masukkan judul form..."
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200 px-4 py-3 text-gray-900 placeholder:text-gray-400">
                                            @error('title')
                                                <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        {{-- Deskripsi --}}
                                        <div>
                                            <label for="desc"
                                                class="block text-sm font-semibold text-gray-900 mb-2">
                                                Deskripsi Form
                                            </label>
                                            <textarea id="desc" name="description" rows="5" placeholder="Masukkan deskripsi form (opsional)..."
                                                class="w-full resize-y rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200 px-4 py-3 text-gray-900 placeholder:text-gray-400">{{ old('description', $form->description) }}</textarea>
                                            @error('description')
                                                <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                            <p class="mt-2 flex items-start gap-2 text-xs text-gray-500">
                                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Deskripsi akan ditampilkan kepada responden di halaman awal
                                                    form.</span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Action Buttons - Sticky Bottom --}}
                                    <div
                                        class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-lg">
                                        <div
                                            class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                                            <a href="{{ route('admin.forms.index') }}"
                                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                                </svg>
                                                Kembali
                                            </a>

                                            <div class="flex items-center gap-3">
                                                @if (!$form->is_published)
                                                    <button type="submit" form="publish-form"
                                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                                                        onclick="return confirm('Apakah Anda yakin ingin mempublikasikan form ini?');">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Publish Form
                                                    </button>
                                                @else
                                                    <button type="submit" form="unpublish-form"
                                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-yellow-500 text-sm font-medium text-white shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200"
                                                        onclick="return confirm('Apakah Anda yakin ingin meng-unpublish form ini?');">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                        Unpublish Form
                                                    </button>
                                                @endif

                                                <button type="submit" form="update-form"
                                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- FORM TERPISAH UNTUK PUBLISH/UNPUBLISH (agar tidak nested) --}}
                @if (!$form->is_published)
                    <form id="publish-form" method="POST" action="{{ route('admin.forms.publish', $form) }}"
                        class="hidden">
                        @csrf @method('PUT')
                    </form>
                @else
                    <form id="unpublish-form" method="POST" action="{{ route('admin.forms.unpublish', $form) }}"
                        class="hidden">
                        @csrf @method('PUT')
                    </form>
                @endif

            </div>
        </div>

        <script>
            // Copy UID functionality
            (function() {
                const btn = document.getElementById('copy-uid');
                const uid = document.getElementById('form-uid')?.textContent?.trim() || '';

                if (btn && uid) {
                    btn.addEventListener('click', async () => {
                        try {
                            await navigator.clipboard.writeText(uid);
                            const originalContent = btn.innerHTML;
                            btn.innerHTML = `
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Copied!
                        `;
                            btn.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');

                            setTimeout(() => {
                                btn.innerHTML = originalContent;
                                btn.classList.remove('bg-emerald-50', 'text-emerald-700',
                                    'border-emerald-300');
                            }, 2000);
                        } catch (e) {
                            console.error('Failed to copy:', e);
                        }
                    });
                }
            })();

            // Auto-resize textarea
            (function() {
                const ta = document.getElementById('desc');
                if (!ta) return;

                const autoResize = () => {
                    ta.style.height = 'auto';
                    ta.style.height = Math.min(ta.scrollHeight, 400) + 'px';
                };

                ta.addEventListener('input', autoResize);
                window.addEventListener('load', autoResize);
                autoResize();
            })();
        </script>
</x-app-layout>
