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

    // SETTINGS awal
    $set = old('settings', $question->settings ?? []);

    // OPTIONS awal (old() jika ada, else dari DB) -> normalisasi jadi array asosiatif
    $initialOptions = is_array(old('options'))
        ? collect(old('options'))
            ->map(function ($opt) {
                return [
                    'id' => is_array($opt) ? $opt['id'] ?? null : null,
                    'label' => is_array($opt) ? $opt['label'] ?? '' : '',
                    'value' => is_array($opt) ? $opt['value'] ?? '' : '',
                    'role' => is_array($opt) ? $opt['role'] ?? 'option' : 'option',
                    'is_other' => (bool) (is_array($opt) ? $opt['is_other'] ?? 0 : 0),
                ];
            })
            ->values()
        : $question->options
            ->map(
                fn($o) => [
                    'id' => $o->id,
                    'label' => $o->label,
                    'value' => $o->value,
                    'role' => $o->role ?? 'option',
                    'is_other' => (bool) $o->is_other,
                ],
            )
            ->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Edit Pertanyaan — {{ $form->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.questions.validations.index', $question) }}"
                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Validations
                </a>
                <a href="{{ route('admin.forms.questions.index', $form) }}"
                    class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Kembali
                </a>
            </div>
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

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="editQuestion({
                initialType: @js(old('type', $question->type)),
                initialSection: @js(old('section_id', $question->section_id)),
                initialPosition: @js(old('position', $question->position)),
                initialTitle: @js(old('title', $question->title)),
                initialDescription: @js(old('description', $question->description)),
                initialShuffle: @js((bool) old('shuffle_options', $question->shuffle_options)),
                initialOtherEnabled: @js((bool) old('other_option_enabled', $question->other_option_enabled)),
                initialOtherLabel: @js(old('other_option_label', 'Lainnya')),
                initialOtherPlaceholder: @js(old('other_option_placeholder', '')),
                initialOtherTextRequired: @js((bool) old('other_option_text_required', 0)),
                initialOptions: @js($initialOptions),
            })"
                x-init="init()">
                {{-- ===== FORM UPDATE (PUT) ===== --}}
                <form id="update-question-form" method="POST" action="{{ route('admin.questions.update', $question) }}"
                    @submit="submitting=true">
                    @csrf
                    @method('PUT')

                    <div class="p-6">
                        {{-- Section --}}
                        <div class="mb-6">
                            <label for="section_id" class="block text-sm font-medium text-emerald-900">Section</label>
                            <select id="section_id" name="section_id" required
                                class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300">
                                @foreach ($sections as $sec)
                                    <option value="{{ $sec->id }}" @selected(old('section_id', $question->section_id) == $sec->id)>
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
                        </div>

                        {{-- Position --}}
                        <div class="mb-6">
                            <label for="position" class="block text-sm font-medium text-emerald-900">Posisi</label>
                            <input id="position" type="number" name="position" min="1"
                                value="{{ old('position', $question->position) }}"
                                class="mt-1 w-40 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300">
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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

                        {{-- Toggles (SELALU kirim ke backend) --}}
                        <!-- Hidden fallbacks -->
                        <input type="hidden" name="shuffle_options" value="0"
                            :value="isOptionsType() && shuffleChecked ? 1 : 0">
                        <input type="hidden" name="other_option_enabled" value="0"
                            :value="isOptionsType() && otherEnabled ? 1 : 0">

                        <div class="mb-6 space-y-3">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <label class="flex items-start gap-3">
                                    <input type="hidden" name="required" value="0">
                                    <input type="checkbox" name="required" value="1" @checked(old('required', $question->required))
                                        class="mt-1">
                                    <span class="text-emerald-900"><span class="font-medium">Wajib diisi</span></span>
                                </label>

                                <label class="flex items-start gap-3" x-show="isOptionsType()" x-cloak>
                                    <input type="checkbox" class="mt-1" x-model="shuffleChecked">
                                    <span class="text-emerald-900"><span class="font-medium">Acak opsi</span></span>
                                </label>

                                <label class="flex items-start gap-3" x-show="isOptionsType()" x-cloak>
                                    <input type="checkbox" class="mt-1" x-model="otherEnabled">
                                    <span class="text-emerald-900"><span class="font-medium">Tambahkan opsi
                                            “Lainnya”</span></span>
                                </label>
                            </div>

                            {{-- Pengaturan "Lainnya" --}}
                            <div x-show="otherEnabled && isOptionsType()" x-cloak
                                class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-emerald-900">Label “Lainnya”
                                            (opsional)</label>
                                        <input type="text" x-model="otherLabel"
                                            class="w-full rounded-xl border border-emerald-200 px-3 py-2"
                                            placeholder='Default: "Lainnya"'>
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
                                    value="{{ $set['min'] ?? 1 }}">
                                <input type="number" step="1" name="settings[max]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="max"
                                    value="{{ $set['max'] ?? 5 }}">
                                <input type="text" name="settings[label_left]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="label kiri"
                                    value="{{ $set['label_left'] ?? '' }}">
                                <input type="text" name="settings[label_right]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="label kanan"
                                    value="{{ $set['label_right'] ?? '' }}">
                                <input type="number" step="1" name="settings[step]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2" placeholder="step"
                                    value="{{ $set['step'] ?? 1 }}">
                            </div>

                            {{-- Generic --}}
                            <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-3" x-show="type!=='linear_scale'">
                                <input type="text" name="settings[placeholder]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2"
                                    placeholder="placeholder (opsional)" value="{{ $set['placeholder'] ?? '' }}">
                                <input type="number" name="settings[min]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2"
                                    placeholder="min (opsional)" value="{{ $set['min'] ?? '' }}">
                                <input type="number" name="settings[max]"
                                    class="rounded-xl border border-emerald-200 px-3 py-2"
                                    placeholder="max (opsional)" value="{{ $set['max'] ?? '' }}">
                            </div>

                            <p class="mt-1 text-xs text-emerald-700/70">Gunakan sesuai kebutuhan tipe pertanyaan.</p>
                        </div>

                        {{-- Options / Grid Items --}}
                        <div class="mb-6" x-show="isOptionsType()" x-cloak>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="block text-sm font-medium text-emerald-900">Opsi / Grid Items</label>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button"
                                        class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs hover:bg-emerald-50"
                                        @click="addOption()">+ Tambah Baris</button>
                                    <span class="text-xs text-emerald-700/70">Untuk Grid: pilih role <code>row</code> /
                                        <code>column</code>.</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(opt, i) in options" :key="i">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <template x-if="opt.id">
                                            <input type="hidden" :name="`options[${i}][id]`" x-model="opt.id">
                                        </template>

                                        <input type="text"
                                            class="flex-1 rounded-xl border border-emerald-200 px-3 py-2"
                                            placeholder="Label" :name="`options[${i}][label]`" x-model="opt.label">

                                        <input type="text"
                                            class="w-56 rounded-xl border border-emerald-200 px-3 py-2"
                                            placeholder="Value (opsional)" :name="`options[${i}][value]`"
                                            x-model="opt.value">

                                        <select class="w-40 rounded-xl border border-emerald-200 px-3 py-2"
                                            :name="`options[${i}][role]`" x-model="opt.role">
                                            <option value="option">option</option>
                                            <option value="row">row</option>
                                            <option value="column">column</option>
                                        </select>

                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="hidden" :name="`options[${i}][is_other]`" value="0">
                                            <input type="checkbox" :name="`options[${i}][is_other]`" value="1"
                                                x-bind:checked="opt.is_other"
                                                @change="opt.is_other = $event.target.checked">
                                            <span>is_other</span>
                                        </label>

                                        <div class="flex items-center gap-1">
                                            <button type="button"
                                                class="rounded-lg border border-emerald-200 px-2 py-1 text-xs hover:bg-emerald-50"
                                                @click="move(i,-1)" :disabled="i === 0">↑</button>
                                            <button type="button"
                                                class="rounded-lg border border-emerald-200 px-2 py-1 text-xs hover:bg-emerald-50"
                                                @click="move(i, 1)" :disabled="i === options.length - 1">↓</button>
                                            <button type="button"
                                                class="rounded-lg bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100"
                                                @click="removeOption(i)">Hapus</button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            @error('options')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Sticky action bar --}}
                    <div
                        class="sticky bottom-0 -mx-6 rounded-b-2xl border-t border-emerald-100 bg-white/90 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-white/70">
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('admin.forms.questions.index', $form) }}"
                                class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                                Kembali
                            </a>
                            <div class="ml-auto flex items-center gap-2">
                                {{-- Tombol Hapus (form DELETE terpisah) --}}
                                <button type="submit" form="delete-question-form"
                                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-white hover:bg-red-700 focus-visible:ring-2 focus-visible:ring-red-500 transition"
                                    onclick="return confirm('Hapus pertanyaan ini?');">
                                    Hapus
                                </button>

                                {{-- Simpan --}}
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

            {{-- FORM DELETE (terpisah & tersembunyi) --}}
            <form id="delete-question-form" method="POST"
                action="{{ route('admin.questions.destroy', $question) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>

    {{-- Alpine helpers --}}
    <script>
        function editQuestion(cfg) {
            return {
                // state utama
                type: cfg.initialType || 'short_text',
                title: cfg.initialTitle || '',
                description: cfg.initialDescription || '',
                submitting: false,

                // toggles wajib backend
                shuffleChecked: !!cfg.initialShuffle,
                otherEnabled: !!cfg.initialOtherEnabled,
                otherLabel: cfg.initialOtherLabel || 'Lainnya',
                otherPlaceholder: cfg.initialOtherPlaceholder || '',
                otherTextRequired: !!cfg.initialOtherTextRequired,

                // opsi
                options: Array.isArray(cfg.initialOptions) ? cfg.initialOptions : [],
                init() {
                    // jika tipe opsi & belum ada opsi -> tambah satu baris
                    if (this.isOptionsType() && this.options.length === 0) {
                        this.options.push({
                            id: null,
                            label: '',
                            value: '',
                            role: 'option',
                            is_other: false
                        });
                    }
                    // reset toggle saat tipe non-opsi
                    this.$watch('type', () => {
                        if (!this.isOptionsType()) {
                            this.shuffleChecked = false;
                            this.otherEnabled = false;
                        } else if (this.options.length === 0) {
                            this.options.push({
                                id: null,
                                label: '',
                                value: '',
                                role: 'option',
                                is_other: false
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
                        id: null,
                        label: '',
                        value: '',
                        role: 'option',
                        is_other: false
                    });
                },
                removeOption(i) {
                    this.options.splice(i, 1);
                    if (this.isOptionsType() && this.options.length === 0) {
                        this.options.push({
                            id: null,
                            label: '',
                            value: '',
                            role: 'option',
                            is_other: false
                        });
                    }
                },
                move(i, d) {
                    const j = i + d;
                    if (j < 0 || j >= this.options.length) return;
                    const t = this.options[i];
                    this.options[i] = this.options[j];
                    this.options[j] = t;
                },
            }
        }
    </script>
</x-app-layout>
