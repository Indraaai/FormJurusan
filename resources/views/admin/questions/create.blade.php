@php
    $types = [
        'short_text' => 'Short answer',
        'long_text' => 'Paragraph',
        'multiple_choice' => 'Multiple choice',
        'checkboxes' => 'Checkboxes',
        'dropdown' => 'Dropdown',
        'file_upload' => 'File upload',
        'linear_scale' => 'Linear scale',
        'mc_grid' => 'Multiple choice grid',
        'checkbox_grid' => 'Checkbox grid',
        'date' => 'Date',
        'time' => 'Time',
    ];

    // Normalisasi old('options') agar selalu bentuk [{label:'', value:''}, ...]
    $oldOptions = collect(old('options', []))
        ->map(
            fn($opt) => [
                'label' => is_array($opt) ? $opt['label'] ?? '' : '',
                'value' => is_array($opt) ? $opt['value'] ?? '' : '',
            ],
        )
        ->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Tambah Pertanyaan — {{ $form->title }}
            </h2>
            <a href="{{ route('admin.forms.questions.index', $form) }}"
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

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="questionForm({
                initialType: @js(old('type', 'short_text')),
                initialOptions: @js($oldOptions),
                initialTitle: @js(old('title', '')),
                initialDescription: @js(old('description', '')),
                initialShuffle: @js((bool) old('shuffle_options', 0)),
                initialOtherEnabled: @js((bool) old('other_option_enabled', 0)),
                initialOtherLabel: @js(old('other_option_label', 'Lainnya')),
                initialOtherPlaceholder: @js(old('other_option_placeholder', '')),
                initialOtherTextRequired: @js((bool) old('other_option_text_required', 0)),
            })"
                x-init="init()">
                <form method="POST" action="{{ route('admin.forms.questions.store', $form) }}"
                    @submit="submitting=true">
                    @csrf

                    <div class="p-6">
                        {{-- Section --}}
                        <div class="mb-6">
                            <label for="section_id" class="block text-sm font-medium text-emerald-900">Section</label>
                            <select id="section_id" name="section_id" required
                                class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300">
                                <option value="">— Pilih Section —</option>
                                @foreach ($sections as $sec)
                                    <option value="{{ $sec->id }}" @selected(old('section_id') == $sec->id)>
                                        #{{ $sec->position }} — {{ $sec->title ?? 'Tanpa judul' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div class="mb-6">
                            <label for="q-type" class="block text-sm font-medium text-emerald-900">Tipe</label>
                            <select id="q-type" name="type" x-model="type" required
                                class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300">
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <p class="mt-1 text-xs text-emerald-700/70"
                                x-show="type === 'mc_grid' || type === 'checkbox_grid'">
                                Untuk tipe Grid, buat pertanyaan dulu lalu tambah Row/Column di halaman Edit.
                            </p>
                            <p class="mt-1 text-xs text-emerald-700/70" x-show="type === 'linear_scale'">
                                Gunakan <span class="font-medium">Settings</span> di bawah untuk mengatur
                                <em>min/max/step</em> serta label kiri/kanan.
                            </p>
                        </div>

                        {{-- Title --}}
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-emerald-900">Judul
                                Pertanyaan</label>
                            <textarea id="title" name="title" rows="2" maxlength="200" x-model="title" x-ref="title"
                                @input="autoresize($event)" required
                                class="mt-1 w-full resize-none rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300"
                                placeholder="Tulis pertanyaan di sini..."></textarea>
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-emerald-700/70">Pertanyaan yang akan terlihat oleh responden.</span>
                                <span class="text-emerald-700/70"><span x-text="title.length"></span>/200</span>
                            </div>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-emerald-900">Deskripsi
                                (opsional)</label>
                            <textarea id="description" name="description" rows="2" maxlength="300" x-model="description" x-ref="description"
                                @input="autoresize($event)"
                                class="mt-1 w-full resize-none rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300"
                                placeholder="Tambahkan konteks atau instruksi singkat..."></textarea>
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-emerald-700/70">Biarkan kosong jika tidak perlu.</span>
                                <span class="text-emerald-700/70"><span x-text="description.length"></span>/300</span>
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Toggles (SELALU kirim nilai ke backend) --}}
                        <!-- Hidden fallbacks: tetap terkirim meski UI toggle tersembunyi / tipe tidak mendukung -->
                        <input type="hidden" name="shuffle_options" value="0"
                            :value="isOptionsType() && shuffleChecked ? 1 : 0">
                        <input type="hidden" name="other_option_enabled" value="0"
                            :value="isOptionsType() && otherEnabled ? 1 : 0">

                        <div class="mb-6 space-y-3">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <label class="flex items-start gap-3">
                                    <input type="hidden" name="required" value="0">
                                    <input type="checkbox" name="required" value="1" @checked(old('required', 0))
                                        class="mt-1">
                                    <span class="text-emerald-900"><span class="font-medium">Wajib diisi</span></span>
                                </label>

                                <!-- UI toggle untuk shuffle (tanpa name) -->
                                <label class="flex items-start gap-3" x-show="isOptionsType()" x-cloak>
                                    <input type="checkbox" class="mt-1" x-model="shuffleChecked">
                                    <span class="text-emerald-900"><span class="font-medium">Acak opsi</span></span>
                                </label>

                                <!-- UI toggle untuk Lainnya (tanpa name) -->
                                <label class="flex items-start gap-3" x-show="isOptionsType()" x-cloak>
                                    <input type="checkbox" class="mt-1" x-model="otherEnabled">
                                    <span class="text-emerald-900"><span class="font-medium">Tambahkan opsi
                                            “Lainnya”</span></span>
                                </label>
                            </div>

                            {{-- Pengaturan "Lainnya" (muncul hanya bila diaktifkan) --}}
                            <div x-show="otherEnabled && isOptionsType()" x-cloak
                                class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-emerald-900">Label “Lainnya”
                                            (opsional)</label>
                                        <input type="text" x-model="otherLabel"
                                            class="w-full rounded-xl border border-emerald-200 px-3 py-2"
                                            placeholder='Default: "Lainnya"'>
                                        <!-- pastikan tidak null saat dikosongkan -->
                                        <input type="hidden" name="other_option_label" value=""
                                            :value="otherEnabled ? (otherLabel || 'Lainnya') : ''">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-emerald-900">Placeholder
                                            (opsional)</label>
                                        <input type="text" x-model="otherPlaceholder"
                                            class="w-full rounded-xl border border-emerald-200 px-3 py-2"
                                            placeholder="contoh: Ketik jawaban lain...">
                                        <input type="hidden" name="other_option_placeholder" value=""
                                            :value="otherEnabled ? otherPlaceholder : ''">
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <input type="hidden" name="other_option_text_required" value="0">
                                        <input type="checkbox" class="mt-1" x-model="otherTextRequired">
                                        <span class="text-emerald-900 text-sm">Wajib isi teks saat memilih
                                            “Lainnya”</span>
                                        <input type="hidden" name="other_option_text_required" value="0"
                                            :value="otherEnabled && otherTextRequired ? 1 : 0">
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-emerald-700/70">
                                    Jika label dikosongkan, sistem otomatis memakai <span
                                        class="font-medium">“Lainnya”</span>.
                                </p>
                            </div>
                        </div>

                        {{-- Settings (kontekstual) --}}
                        <div class="mb-6">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-emerald-900">Settings (opsional)</label>
                                <div class="text-xs text-emerald-700/70" x-show="type==='linear_scale'">
                                    Direkomendasikan 1–5 atau 1–10</div>
                            </div>

                            {{-- Linear scale --}}
                            <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-5" x-show="type==='linear_scale'">
                                <input type="number" step="1" name="settings[min]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="min"
                                    value="{{ old('settings.min', 1) }}">
                                <input type="number" step="1" name="settings[max]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="max"
                                    value="{{ old('settings.max', 5) }}">
                                <input type="text" name="settings[label_left]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="label kiri"
                                    value="{{ old('settings.label_left') }}">
                                <input type="text" name="settings[label_right]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="label kanan"
                                    value="{{ old('settings.label_right') }}">
                                <input type="number" step="1" name="settings[step]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="step"
                                    value="{{ old('settings.step', 1) }}">
                            </div>

                            {{-- Generic (opsional) --}}
                            <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-3" x-show="type!=='linear_scale'">
                                <input type="text" name="settings[placeholder]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2"
                                    placeholder="placeholder (opsional)" value="{{ old('settings.placeholder') }}">
                                <input type="number" name="settings[min]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2"
                                    placeholder="min (opsional)" value="{{ old('settings.min') }}">
                                <input type="number" name="settings[max]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2"
                                    placeholder="max (opsional)" value="{{ old('settings.max') }}">
                            </div>

                            <p class="mt-1 text-xs text-emerald-700/70">Gunakan sesuai kebutuhan tipe pertanyaan.</p>
                        </div>

                        {{-- Options (untuk MC/Checkbox/Dropdown) --}}
                        <template x-if="isOptionsType()">
                            <div class="mb-6">
                                <div class="mb-2 flex items-center justify-between">
                                    <label class="block text-sm font-medium text-emerald-900">Opsi</label>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button"
                                            class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs hover:bg-emerald-50"
                                            @click="addOption()">
                                            + Tambah Opsi
                                        </button>
                                        <button type="button"
                                            class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs hover:bg-emerald-50"
                                            @click="quickAdd('1-5')">
                                            1–5
                                        </button>
                                        <button type="button"
                                            class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs hover:bg-emerald-50"
                                            @click="quickAdd('yn')">
                                            Ya/Tidak
                                        </button>
                                        <button type="button"
                                            class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs hover:bg-emerald-50"
                                            @click="showBulk = !showBulk">
                                            Tempel massal
                                        </button>
                                    </div>
                                </div>

                                {{-- daftar opsi --}}
                                <div class="space-y-2">
                                    <template x-for="(opt, i) in options" :key="i">
                                        <div class="flex items-center gap-2">
                                            <input type="text"
                                                class="flex-1 rounded-xl border border-emerald-200 px-3 py-2"
                                                placeholder="Label" :name="`options[${i}][label]`"
                                                x-model="opt.label">
                                            <input type="text"
                                                class="w-56 rounded-xl border border-emerald-200 px-3 py-2"
                                                placeholder="Value (opsional)" :name="`options[${i}][value]`"
                                                x-model="opt.value">
                                            <div class="flex items-center gap-1">
                                                <button type="button"
                                                    class="rounded-lg border border-emerald-200 px-2 py-1 text-xs hover:bg-emerald-50"
                                                    @click="move(i, -1)" :disabled="i === 0">↑</button>
                                                <button type="button"
                                                    class="rounded-lg border border-emerald-200 px-2 py-1 text-xs hover:bg-emerald-50"
                                                    @click="move(i, +1)" :disabled="i === options.length - 1">↓</button>
                                                <button type="button"
                                                    class="rounded-lg bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100"
                                                    @click="removeOption(i)">Hapus</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- bulk add --}}
                                <div class="mt-3 rounded-xl border border-emerald-100 bg-emerald-50/40 p-3"
                                    x-show="showBulk" x-cloak>
                                    <label class="mb-1 block text-xs font-medium text-emerald-900">Tempel daftar opsi
                                        (1 baris = 1 opsi)</label>
                                    <textarea rows="3" x-model="bulkText"
                                        class="w-full resize-none rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300"
                                        placeholder="Contoh:
Sangat setuju
Setuju
Netral
Tidak setuju
Sangat tidak setuju"></textarea>
                                    <div class="mt-2 flex items-center gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-emerald-600 px-3 py-1.5 text-xs text-white hover:bg-emerald-700"
                                            @click="applyBulk()">
                                            Tambahkan
                                        </button>
                                        <button type="button"
                                            class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs hover:bg-emerald-50"
                                            @click="bulkText=''">
                                            Bersihkan
                                        </button>
                                    </div>
                                </div>

                                @error('options')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </template>
                    </div>

                    {{-- Sticky actions --}}
                    <div
                        class="sticky bottom-0 -mx-6 rounded-b-2xl border-t border-emerald-100 bg-white/90 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-white/70">
                        <div class="flex items-center justify-between gap-3">
                            <p class="hidden text-xs text-emerald-700/70 sm:block">
                                Tip: gunakan <span class="font-medium">Tempel massal</span> untuk membuat banyak opsi
                                sekaligus.
                            </p>
                            <div class="ml-auto flex items-center gap-2">
                                <a href="{{ route('admin.forms.questions.index', $form) }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                                    Batal
                                </a>
                                <button type="submit" :disabled="submitting"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm
                                               hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-60 transition">
                                    <svg x-show="submitting" xmlns="http://www.w3.org/2000/svg"
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

        </div>
    </div>

    {{-- Alpine helpers --}}
    <script>
        function questionForm({
            initialType,
            initialOptions,
            initialTitle,
            initialDescription,
            initialShuffle,
            initialOtherEnabled,
            initialOtherLabel,
            initialOtherPlaceholder,
            initialOtherTextRequired
        }) {
            return {
                // state utama
                type: initialType || 'short_text',
                title: initialTitle || '',
                description: initialDescription || '',
                submitting: false,

                // opsi
                options: Array.isArray(initialOptions) && initialOptions.length ? initialOptions : [],
                showBulk: false,
                bulkText: '',

                // toggles & lainnya
                shuffleChecked: !!initialShuffle,
                otherEnabled: !!initialOtherEnabled,
                otherLabel: initialOtherLabel || 'Lainnya',
                otherPlaceholder: initialOtherPlaceholder || '',
                otherTextRequired: !!initialOtherTextRequired,

                init() {
                    // jika tipe opsi & belum ada opsi -> tambah satu baris kosong
                    if (this.isOptionsType() && this.options.length === 0) {
                        this.options.push({
                            label: '',
                            value: ''
                        });
                    }
                    // reset toggle saat ganti tipe ke non-opsi
                    this.$watch('type', (v) => {
                        if (!this.isOptionsType()) {
                            this.shuffleChecked = false;
                            this.otherEnabled = false;
                        } else if (this.options.length === 0) {
                            this.options.push({
                                label: '',
                                value: ''
                            });
                        }
                    });
                    // autoresize awal
                    this.$nextTick(() => {
                        this.$refs?.title && this.autoresize({
                            target: this.$refs.title
                        });
                        this.$refs?.description && this.autoresize({
                            target: this.$refs.description
                        });
                    });
                },

                // helpers
                isOptionsType() {
                    return ['multiple_choice', 'checkboxes', 'dropdown'].includes(this.type)
                },

                // textarea autoresize
                autoresize(e) {
                    const el = e.target;
                    el.style.height = 'auto';
                    el.style.height = (el.scrollHeight) + 'px';
                },

                // opsi handlers
                addOption() {
                    this.options.push({
                        label: '',
                        value: ''
                    });
                },
                removeOption(i) {
                    this.options.splice(i, 1);
                    if (this.options.length === 0) this.options.push({
                        label: '',
                        value: ''
                    });
                },
                move(i, delta) {
                    const j = i + delta;
                    if (j < 0 || j >= this.options.length) return;
                    const tmp = this.options[i];
                    this.options[i] = this.options[j];
                    this.options[j] = tmp;
                },
                quickAdd(kind) {
                    if (kind === '1-5') {
                        this.options = [1, 2, 3, 4, 5].map(n => ({
                            label: String(n),
                            value: String(n)
                        }));
                    }
                    if (kind === 'yn') {
                        this.options = [{
                            label: 'Ya',
                            value: 'ya'
                        }, {
                            label: 'Tidak',
                            value: 'tidak'
                        }];
                    }
                },
                applyBulk() {
                    const lines = (this.bulkText || '').split('\n').map(s => s.trim()).filter(Boolean);
                    if (!lines.length) return;
                    this.options = [...this.options, ...lines.map(l => ({
                        label: l,
                        value: ''
                    }))];
                    this.bulkText = '';
                    this.showBulk = false;
                }
            }
        }
    </script>
</x-app-layout>
