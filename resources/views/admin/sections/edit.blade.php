<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-secondary-900 leading-tight">Edit Section</h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Form: <span class="font-semibold text-primary-600">{{ $section->form->title }}</span>
                </p>
            </div>
            <a href="{{ route('admin.forms.sections.index', $section->form) }}"
                class="inline-flex items-center gap-2 rounded-xl border border-secondary-200 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                <i class="bi bi-arrow-left"></i>
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
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-success-200 bg-success-50 p-3 text-success-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-danger-200 bg-danger-50 p-3 text-danger-800">
                    <div class="mb-1 font-semibold">Periksa kembali input kamu:</div>
                    <ul class="list-inside list-disc text-sm">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ===== FORM UPDATE (PUT) ===== --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <div class="rounded-2xl border border-primary-100 bg-white shadow-soft" x-data="{
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
                    <div class="border-b border-primary-100 p-6">
                        <h3 class="text-lg font-semibold text-secondary-900">Detail Section</h3>
                        <p class="mt-1 text-sm text-secondary-500">Edit informasi section untuk form Anda.</p>
                    </div>
                    <form x-ref="form" id="update-section-form" method="POST"
                        action="{{ route('admin.sections.update', $section) }}" @submit.prevent="submit()">
                        @csrf
                        @method('PUT')

                        <div class="p-8">
                            {{-- JUDUL (opsional) --}}
                            <div class="mb-8">
                                <label for="title" class="block text-sm font-semibold text-secondary-900 mb-2">
                                    Judul Section <span class="font-normal text-secondary-500">(opsional)</span>
                                </label>
                                <input id="title" x-ref="title" name="title" type="text"
                                    value="{{ old('title', $section->title) }}" maxlength="120"
                                    @input="titleCount = $event.target.value.length"
                                    class="w-full rounded-xl border bg-white px-4 py-3 text-base text-secondary-900 placeholder:text-secondary-400
                                       focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 transition"
                                    :class="errs.title ? 'border-danger-300 focus:border-danger-500 focus:ring-danger-200' :
                                        'border-secondary-300'"
                                    placeholder="Contoh: Informasi Pribadi" autocomplete="off" />
                                <div class="mt-2 flex items-center justify-between text-xs text-secondary-500">
                                    <span>Nama bagian yang akan ditampilkan kepada responden</span>
                                    <span class="font-medium"><span x-text="titleCount">0</span>/120</span>
                                </div>
                                <template x-if="errs.title">
                                    <p class="mt-1 text-sm text-danger-600" x-text="errs.title?.[0]"></p>
                                </template>
                                @error('title')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- DESKRIPSI (opsional) --}}
                            <div class="mb-8">
                                <label for="description" class="block text-sm font-semibold text-secondary-900 mb-2">
                                    Deskripsi <span class="font-normal text-secondary-500">(opsional)</span>
                                </label>
                                <textarea id="description" x-ref="description" name="description" rows="5" maxlength="500"
                                    @input="descCount = $event.target.value.length"
                                    class="w-full rounded-xl border bg-white px-4 py-3 text-base text-secondary-900 placeholder:text-secondary-400 resize-y
                                       focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 transition"
                                    :class="errs.description ?
                                        'border-danger-300 focus:border-danger-500 focus:ring-danger-200' :
                                        'border-secondary-300'"
                                    placeholder="Berikan penjelasan singkat tentang bagian ini...">{{ old('description', $section->description) }}</textarea>
                                <div class="mt-2 flex items-center justify-between text-xs text-secondary-500">
                                    <span>Deskripsi tambahan untuk responden (opsional)</span>
                                    <span class="font-medium"><span x-text="descCount">0</span>/500</span>
                                </div>
                                <template x-if="errs.description">
                                    <p class="mt-1 text-sm text-danger-600" x-text="errs.description?.[0]"></p>
                                </template>
                                @error('description')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- POSISI --}}
                            <div>
                                <label for="position"
                                    class="block text-sm font-semibold text-secondary-900 mb-2">Posisi</label>
                                <input id="position" x-ref="position" name="position" type="number" min="1"
                                    value="{{ old('position', $section->position) }}"
                                    class="w-32 rounded-xl border bg-white px-4 py-3 text-base text-secondary-900
                                       focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 transition"
                                    :class="errs.position ? 'border-danger-300 focus:border-danger-500 focus:ring-danger-200' :
                                        'border-secondary-300'" />
                                <div class="mt-2 text-xs text-secondary-500">Urutan tampil section dalam form</div>
                                <template x-if="errs.position">
                                    <p class="mt-1 text-sm text-danger-600" x-text="errs.position?.[0]"></p>
                                </template>
                                @error('position')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="border-t border-secondary-200 bg-secondary-50/50 px-8 py-4">
                            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <button type="submit" form="delete-section-form"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-danger-300 bg-white px-5 py-2.5 text-sm font-medium text-danger-700 hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-danger-500 transition"
                                    onclick="return confirm('Hapus section ini? Pertanyaan di dalamnya juga akan terhapus.');">
                                    <i class="bi bi-trash"></i>
                                    Hapus Section
                                </button>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.forms.sections.index', $section->form) }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-secondary-300 bg-white px-5 py-2.5 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                        Batal
                                    </a>
                                    <button type="submit" :disabled="loading"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm
                                           hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:cursor-not-allowed disabled:opacity-60 transition">
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
                    </form>
                </div>

                <aside class="space-y-4">
                    <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-soft">
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                                <i class="bi bi-info-circle text-lg"></i>
                            </span>
                            <div>
                                <h4 class="text-sm font-semibold text-secondary-900">Panduan Edit</h4>
                                <p class="mt-1 text-xs text-secondary-500">Perubahan akan langsung tersimpan setelah
                                    Anda klik Simpan.</p>
                            </div>
                        </div>
                        <ul class="mt-4 space-y-2 text-xs text-secondary-600">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Ubah posisi untuk mengatur urutan section.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Judul dan deskripsi bisa dikosongkan.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Pertanyaan dalam section tidak ikut terhapus jika section dihapus.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-secondary-100 bg-secondary-50 p-5">
                        <p class="text-xs font-semibold text-secondary-700">Info Section:</p>
                        <div class="mt-3 space-y-1.5 text-xs text-secondary-600">
                            <div class="flex justify-between">
                                <span>Posisi saat ini:</span>
                                <span class="font-semibold">{{ $section->position }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total pertanyaan:</span>
                                <span class="font-semibold">{{ $section->questions_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </aside>
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
