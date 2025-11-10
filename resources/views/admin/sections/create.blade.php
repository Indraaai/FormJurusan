<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Tambah Section — {{ $form->title }}
            </h2>
            <a href="{{ route('admin.forms.sections.index', $form) }}"
                class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-red-800">
                    <div class="mb-1 font-semibold">Periksa kembali input kamu:</div>
                    <ul class="list-inside list-disc text-sm">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="{ titleCount: ({{ json_encode(old('title', '')) }}).length, descCount: ({{ json_encode(old('description', '')) }}).length, loading: false }">
                <form method="POST" action="{{ route('admin.forms.sections.store', $form) }}" @submit="loading=true">
                    @csrf

                    <!-- Body -->
                    <div class="p-6">
                        {{-- JUDUL (opsional) --}}
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-emerald-900">
                                Judul <span class="text-emerald-700/60 font-normal">(opsional)</span>
                            </label>
                            <input id="title" name="title" type="text" value="{{ old('title') }}"
                                maxlength="120" @input="titleCount = $event.target.value.length"
                                class="mt-1 w-full rounded-xl border bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40
                                       focus:outline-none focus-visible:ring-2 transition
                                       {{ $errors->has('title') ? 'border-red-300 ring-red-200 focus-visible:ring-red-300' : 'border-emerald-200 focus-visible:ring-emerald-300' }}"
                                placeholder="Contoh: Informasi Umum Responden" autocomplete="off" />
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-emerald-700/70">Nama bagian yang akan tampil ke responden.</span>
                                <span class="text-emerald-700/70"><span x-text="titleCount"></span>/120</span>
                            </div>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- DESKRIPSI (opsional) --}}
                        <div class="mb-2">
                            <label for="description" class="block text-sm font-medium text-emerald-900">
                                Deskripsi <span class="text-emerald-700/60 font-normal">(opsional)</span>
                            </label>
                            <textarea id="description" name="description" rows="6" maxlength="500"
                                @input="descCount = $event.target.value.length"
                                class="mt-1 w-full rounded-xl border bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40
                                       focus:outline-none focus-visible:ring-2 transition
                                       {{ $errors->has('description') ? 'border-red-300 ring-red-200 focus-visible:ring-red-300' : 'border-emerald-200 focus-visible:ring-emerald-300' }}"
                                placeholder="Tambahkan konteks/instruksi singkat untuk responden.">{{ old('description') }}</textarea>
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-emerald-700/70">Bisa dikosongkan kalau tidak diperlukan.</span>
                                <span class="text-emerald-700/70"><span x-text="descCount"></span>/500</span>
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Sticky action bar -->
                    <div
                        class="sticky bottom-0 -mx-6 rounded-b-2xl border-t border-emerald-100 bg-white/90 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-white/70">
                        <div class="flex items-center justify-between gap-3">
                            <p class="hidden text-xs text-emerald-700/70 sm:block">
                                Tekan <kbd class="rounded border px-1 text-[11px]">Enter</kbd> untuk simpan.
                            </p>
                            <div class="ml-auto flex items-center gap-2">
                                <a href="{{ route('admin.forms.sections.index', $form) }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                                    Batal
                                </a>
                                <button type="submit" :disabled="loading"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm
                                               hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-60 transition">
                                    <svg x-show="loading" xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="3"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                    </svg>
                                    <span>Simpan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- /Sticky action bar -->
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
