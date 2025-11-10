<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Edit Section — {{ $section->form->title }}
            </h2>
            <a href="{{ route('admin.forms.sections.index', $section->form) }}"
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

            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3 text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

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

            {{-- ===== FORM UPDATE (PUT) ===== --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="{
                loading: false,
                errs: {},
                titleCount: ({{ json_encode(old('title', $section->title ?? '')) }}).length,
                descCount: ({{ json_encode(old('description', $section->description ?? '')) }}).length,
                redirectUrl: '{{ route('admin.forms.sections.index', $section->form) }}',
                async submit() {
                    this.loading = true;
                    this.errs = {};
                    const form = this.$refs.form;
                    const fd = new FormData(form);
                    // method spoofing sudah ada di input _method, tapi aman kalau ditambah
                    fd.set('_method', 'PUT');
                    try {
                        const res = await fetch(form.action, {
                            method: 'POST',
                            body: fd,
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (res.status === 422) {
                            const data = await res.json();
                            this.errs = data.errors || {};
                            this.loading = false;
                            // fokus ke field pertama yang error
                            const firstKey = Object.keys(this.errs)[0];
                            if (firstKey) this.$refs[firstKey]?.focus();
                        } else {
                            window.location.href = this.redirectUrl; // redirect sukses -> Sections index
                        }
                    } catch (e) {
                        // fallback: submit normal kalau fetch gagal (misal adblock, jaringan, dsb)
                        form.submit();
                    }
                }
            }">
                <form x-ref="form" id="update-section-form" method="POST"
                    action="{{ route('admin.sections.update', $section) }}" @submit.prevent="submit()">
                    @csrf
                    @method('PUT')

                    <div class="p-6">
                        {{-- JUDUL (opsional) --}}
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-emerald-900">
                                Judul <span class="text-emerald-700/60 font-normal">(opsional)</span>
                            </label>
                            <input id="title" x-ref="title" name="title" type="text"
                                value="{{ old('title', $section->title) }}" maxlength="120"
                                @input="titleCount = $event.target.value.length"
                                class="mt-1 w-full rounded-xl border bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40
                                       focus:outline-none focus-visible:ring-2 transition"
                                :class="errs.title ? 'border-red-300 ring-red-200 focus-visible:ring-red-300' :
                                    'border-emerald-200 focus-visible:ring-emerald-300'"
                                placeholder="Contoh: Informasi Umum Responden" autocomplete="off" />
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-emerald-700/70">Nama bagian yang akan tampil ke responden.</span>
                                <span class="text-emerald-700/70"><span x-text="titleCount"></span>/120</span>
                            </div>
                            <template x-if="errs.title">
                                <p class="mt-1 text-sm text-red-600" x-text="errs.title?.[0]"></p>
                            </template>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- DESKRIPSI (opsional) --}}
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-emerald-900">
                                Deskripsi <span class="text-emerald-700/60 font-normal">(opsional)</span>
                            </label>
                            <textarea id="description" x-ref="description" name="description" rows="6" maxlength="500"
                                @input="descCount = $event.target.value.length"
                                class="mt-1 w-full rounded-xl border bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40
                                       focus:outline-none focus-visible:ring-2 transition"
                                :class="errs.description ? 'border-red-300 ring-red-200 focus-visible:ring-red-300' :
                                    'border-emerald-200 focus-visible:ring-emerald-300'"
                                placeholder="Tambahkan konteks/instruksi singkat untuk responden.">{{ old('description', $section->description) }}</textarea>
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-emerald-700/70">Bisa dikosongkan kalau tidak diperlukan.</span>
                                <span class="text-emerald-700/70"><span x-text="descCount"></span>/500</span>
                            </div>
                            <template x-if="errs.description">
                                <p class="mt-1 text-sm text-red-600" x-text="errs.description?.[0]"></p>
                            </template>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- POSISI --}}
                        <div class="mb-2">
                            <label for="position" class="block text-sm font-medium text-emerald-900">Posisi</label>
                            <input id="position" x-ref="position" name="position" type="number" min="1"
                                value="{{ old('position', $section->position) }}"
                                class="mt-1 w-40 rounded-xl border bg-white px-3 py-2 text-emerald-900 focus:outline-none focus-visible:ring-2 transition"
                                :class="errs.position ? 'border-red-300 ring-red-200 focus-visible:ring-red-300' :
                                    'border-emerald-200 focus-visible:ring-emerald-300'" />
                            <div class="mt-1 text-xs text-emerald-700/70">Urutan tampil section dalam form.</div>
                            <template x-if="errs.position">
                                <p class="mt-1 text-sm text-red-600" x-text="errs.position?.[0]"></p>
                            </template>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Sticky action bar --}}
                    <div
                        class="sticky bottom-0 -mx-6 rounded-b-2xl border-t border-emerald-100 bg-white/90 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-white/70">
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('admin.forms.sections.index', $section->form) }}"
                                class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                                Kembali
                            </a>
                            <div class="ml-auto flex items-center gap-2">
                                {{-- Tombol Hapus memicu form DELETE terpisah --}}
                                <button type="submit" form="delete-section-form"
                                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-white hover:bg-red-700 focus-visible:ring-2 focus-visible:ring-red-500 transition"
                                    onclick="return confirm('Hapus section ini? Pertanyaan di dalamnya juga akan terhapus.');">
                                    Hapus
                                </button>
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
                    {{-- /Sticky action bar --}}
                </form>
            </div>

            {{-- ===== FORM DELETE (DELETE) - terpisah & tersembunyi ===== --}}
            <form id="delete-section-form" method="POST" action="{{ route('admin.sections.destroy', $section) }}"
                class="hidden">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>
</x-app-layout>
