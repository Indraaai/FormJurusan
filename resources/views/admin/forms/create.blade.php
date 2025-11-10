<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">Buat Form Baru</h2>
                <p class="mt-1 text-sm text-secondary-600">Isi informasi dasar form sebelum menambahkan pertanyaan</p>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-soft border border-primary-100" x-data="{ titleCount: 0, descCount: 0, loading: false }"
                x-init="titleCount = ($refs.title?.value?.length || 0);
                descCount = ($refs.desc?.value?.length || 0);">
                <form method="POST" action="{{ route('admin.forms.store') }}" @submit="loading=true">
                    @csrf

                    <!-- Card body -->
                    <div class="p-6 space-y-6">
                        {{-- JUDUL --}}
                        <div>
                            <label for="title" class="block text-sm font-semibold text-secondary-900 mb-2">
                                Judul Form <span class="text-danger-600">*</span>
                            </label>
                            <input id="title" x-ref="title" @input="titleCount = $event.target.value.length"
                                type="text" name="title" value="{{ old('title') }}" maxlength="120" required
                                autocomplete="off"
                                class="w-full rounded-lg border bg-white px-4 py-3 text-secondary-900 placeholder:text-secondary-400
                                       focus:outline-none focus:ring-2 transition-all
                                       {{ $errors->has('title') ? 'border-danger-300 focus:ring-danger-500 focus:border-danger-500' : 'border-primary-200 focus:ring-primary-500 focus:border-primary-500' }}"
                                placeholder="Contoh: Survei Kepuasan Mahasiswa 2025" />
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span class="text-secondary-500">
                                    <i class="bi bi-info-circle"></i>
                                    Nama form yang terlihat oleh responden
                                </span>
                                <span class="text-secondary-500 font-medium">
                                    <span x-text="titleCount"></span>/120
                                </span>
                            </div>
                            @error('title')
                                <p class="mt-2 text-sm text-danger-600 flex items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- DESKRIPSI --}}
                        <div>
                            <label for="description" class="block text-sm font-semibold text-secondary-900 mb-2">
                                Deskripsi <span class="text-secondary-500 font-normal">(opsional)</span>
                            </label>
                            <textarea id="description" x-ref="desc" @input="descCount = $event.target.value.length" name="description"
                                rows="6" maxlength="500"
                                class="w-full rounded-lg border bg-white px-4 py-3 text-secondary-900 placeholder:text-secondary-400
                                       focus:outline-none focus:ring-2 transition-all resize-none
                                       {{ $errors->has('description') ? 'border-danger-300 focus:ring-danger-500 focus:border-danger-500' : 'border-primary-200 focus:ring-primary-500 focus:border-primary-500' }}"
                                placeholder="Jelaskan tujuan form, instruksi singkat, atau informasi penting lainnya yang perlu diketahui responden.">{{ old('description') }}</textarea>
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span class="text-secondary-500">
                                    <i class="bi bi-info-circle"></i>
                                    Bantu responden memahami konteks form
                                </span>
                                <span class="text-secondary-500 font-medium">
                                    <span x-text="descCount"></span>/500
                                </span>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-danger-600 flex items-center gap-1">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action bar -->
                    <div class="border-t border-primary-100 bg-secondary-50/50 px-6 py-4 rounded-b-xl">
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ url()->previous() }}"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-secondary-300 bg-white px-5 py-2.5 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                                <i class="bi bi-arrow-left"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit" :disabled="loading"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-2.5 text-sm font-semibold text-white shadow-soft
                                           hover:shadow-soft-md hover:from-primary-700 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200">
                                <svg x-show="loading" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin"
                                    viewBox="0 0 24 24" fill="none" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                                <i x-show="!loading" class="bi bi-check-lg"></i>
                                <span>Simpan & Lanjutkan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tips Card --}}
            <div
                class="mt-6 rounded-xl border border-primary-200 bg-gradient-to-r from-primary-50 to-primary-100/50 p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="bi bi-lightbulb text-white text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-secondary-900 mb-1">Tips</h4>
                        <p class="text-sm text-secondary-700">
                            Setelah form disimpan, Anda bisa menambahkan <strong>Sections</strong> dan
                            <strong>Questions</strong> untuk membuat form yang lebih terstruktur.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
