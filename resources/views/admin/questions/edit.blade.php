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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-secondary-900">Edit Pertanyaan</h2>
                <p class="mt-1 text-sm text-secondary-600">
                    Form: <span class="font-semibold text-primary-600">{{ $form->title }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.questions.validations.index', $question) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-secondary-200 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                    <i class="bi bi-shield-check"></i>
                    Validations
                </a>
                <a href="{{ route('admin.forms.questions.index', $form) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-primary-200 bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div
                    class="rounded-xl border border-primary-200 bg-primary-50/70 p-3 text-sm font-medium text-primary-900">
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

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <div class="rounded-2xl border border-primary-100 bg-white shadow-soft" x-data="editQuestion({
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
                    <div class="border-b border-primary-100 p-6">
                        <h3 class="text-lg font-semibold text-secondary-900">Detail Pertanyaan</h3>
                        <p class="mt-1 text-sm text-secondary-500">Selaraskan struktur, tipe, serta opsi agar sesuai
                            dengan gaya form lainnya.</p>
                    </div>

                    {{-- ===== FORM UPDATE (PUT) ===== --}}
                    <form id="update-question-form" method="POST"
                        action="{{ route('admin.questions.update', $question) }}" class="flex flex-col"
                        @submit="submitting=true">
                        @csrf
                        @method('PUT')

                        <div class="space-y-8 p-8">
                            {{-- Section, Type, Position --}}
                            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                                <div>
                                    <label for="section_id"
                                        class="mb-2 block text-sm font-semibold text-secondary-900">Section</label>
                                    <select id="section_id" name="section_id" required
                                        class="w-full rounded-xl border border-secondary-300 bg-white px-4 py-3 text-secondary-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                                        @foreach ($sections as $sec)
                                            <option value="{{ $sec->id }}" @selected(old('section_id', $question->section_id) == $sec->id)>
                                                #{{ $sec->position }} — {{ $sec->title ?? 'Tanpa judul' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('section_id')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="q-type"
                                        class="mb-2 block text-sm font-semibold text-secondary-900">Tipe
                                        Pertanyaan</label>
                                    <select id="q-type" name="type" x-model="type" required
                                        class="w-full rounded-xl border border-secondary-300 bg-white px-4 py-3 text-secondary-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                                        @foreach ($types as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position"
                                        class="mb-2 block text-sm font-semibold text-secondary-900">Posisi</label>
                                    <input id="position" type="number" name="position" min="1"
                                        value="{{ old('position', $question->position) }}"
                                        class="w-full rounded-xl border border-secondary-300 bg-white px-4 py-3 text-secondary-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                                    @error('position')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Title & Description --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="title"
                                        class="mb-2 block text-sm font-semibold text-secondary-900">Judul
                                        Pertanyaan</label>
                                    <textarea id="title" name="title" rows="2" maxlength="200" x-model="title" x-ref="title"
                                        @input="autoresize($event)" required
                                        class="w-full resize-none rounded-xl border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                        placeholder="Tulis pertanyaan di sini..."></textarea>
                                    <div class="mt-2 flex items-center justify-between text-xs text-secondary-500">
                                        <span>Pertanyaan yang akan dilihat responden.</span>
                                        <span class="font-medium"><span x-text="title.length"></span>/200</span>
                                    </div>
                                    @error('title')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description"
                                        class="mb-2 block text-sm font-semibold text-secondary-900">Deskripsi
                                        (opsional)</label>
                                    <textarea id="description" name="description" rows="3" maxlength="300" x-model="description" x-ref="description"
                                        @input="autoresize($event)"
                                        class="w-full resize-none rounded-xl border border-secondary-300 bg-white px-4 py-3 text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                        placeholder="Tambahkan konteks atau instruksi singkat..."></textarea>
                                    <div class="mt-2 flex items-center justify-between text-xs text-secondary-500">
                                        <span>Biarkan kosong jika tidak diperlukan.</span>
                                        <span class="font-medium"><span x-text="description.length"></span>/300</span>
                                    </div>
                                    @error('description')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Hidden fallbacks for toggles --}}
                            <input type="hidden" name="shuffle_options" value="0"
                                :value="isOptionsType() && shuffleChecked ? 1 : 0">
                            <input type="hidden" name="other_option_enabled" value="0"
                                :value="isOptionsType() && otherEnabled ? 1 : 0">

                            {{-- Toggles --}}
                            <div class="rounded-2xl border border-secondary-100 bg-secondary-50/60 p-5 space-y-4">
                                <p class="text-sm font-semibold text-secondary-900">Pengaturan Wajib & Opsi</p>
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label class="flex items-start gap-3 text-secondary-800">
                                        <input type="hidden" name="required" value="0">
                                        <input type="checkbox" name="required" value="1"
                                            @checked(old('required', $question->required))
                                            class="mt-1 rounded border-secondary-300 text-primary-600 focus:ring-primary-300">
                                        <span><span class="font-semibold">Wajib diisi</span><span
                                                class="block text-xs text-secondary-500">Responden tidak bisa
                                                lanjut tanpa menjawab.</span></span>
                                    </label>

                                    <label class="flex items-start gap-3 text-secondary-800" x-show="isOptionsType()"
                                        x-cloak>
                                        <input type="checkbox"
                                            class="mt-1 rounded border-secondary-300 text-primary-600 focus:ring-primary-300"
                                            x-model="shuffleChecked">
                                        <span><span class="font-semibold">Acak urutan opsi</span><span
                                                class="block text-xs text-secondary-500">Setiap responden melihat
                                                urutan berbeda.</span></span>
                                    </label>

                                    <label class="flex items-start gap-3 text-secondary-800" x-show="isOptionsType()"
                                        x-cloak>
                                        <input type="checkbox"
                                            class="mt-1 rounded border-secondary-300 text-primary-600 focus:ring-primary-300"
                                            x-model="otherEnabled">
                                        <span><span class="font-semibold">Tambahkan opsi “Lainnya”</span><span
                                                class="block text-xs text-secondary-500">Biarkan responden mengisi
                                                jawaban sendiri.</span></span>
                                    </label>
                                </div>

                                <div x-show="otherEnabled && isOptionsType()" x-cloak
                                    class="rounded-xl border border-primary-100 bg-white/80 p-4 space-y-3">
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold text-secondary-700">Label
                                                “Lainnya”</label>
                                            <input type="text" x-model="otherLabel"
                                                class="w-full rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                                placeholder='Default: "Lainnya"'>
                                            <input type="hidden" name="other_option_label" value=""
                                                :value="otherEnabled ? (otherLabel || 'Lainnya') : ''">
                                        </div>
                                        <div>
                                            <label
                                                class="mb-1 block text-xs font-semibold text-secondary-700">Placeholder</label>
                                            <input type="text" x-model="otherPlaceholder"
                                                class="w-full rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                                placeholder="contoh: Ketik jawaban lain...">
                                            <input type="hidden" name="other_option_placeholder" value=""
                                                :value="otherEnabled ? otherPlaceholder : ''">
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <input type="hidden" name="other_option_text_required" value="0">
                                            <input type="checkbox"
                                                class="mt-1 rounded border-secondary-300 text-primary-600 focus:ring-primary-300"
                                                x-model="otherTextRequired">
                                            <span class="text-sm text-secondary-800">Teks wajib saat memilih
                                                “Lainnya”</span>
                                            <input type="hidden" name="other_option_text_required" value="0"
                                                :value="otherEnabled && otherTextRequired ? 1 : 0">
                                        </div>
                                    </div>
                                    <p class="text-xs text-secondary-500">Jika label dikosongkan, sistem otomatis
                                        memakai “Lainnya”.</p>
                                </div>
                            </div>

                            {{-- Settings --}}
                            <div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-secondary-900">Pengaturan Tambahan</p>
                                    <span class="text-xs text-secondary-500" x-show="type==='linear_scale'"
                                        x-cloak>Direkomendasikan 1–5 atau 1–10</span>
                                </div>
                                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-5" x-show="type==='linear_scale'"
                                    x-cloak>
                                    <input type="number" step="1" name="settings[min]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Min" value="{{ $set['min'] ?? 1 }}">
                                    <input type="number" step="1" name="settings[max]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Max" value="{{ $set['max'] ?? 5 }}">
                                    <input type="text" name="settings[label_left]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Label kiri" value="{{ $set['label_left'] ?? '' }}">
                                    <input type="text" name="settings[label_right]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Label kanan" value="{{ $set['label_right'] ?? '' }}">
                                    <input type="number" step="1" name="settings[step]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Step" value="{{ $set['step'] ?? 1 }}">
                                </div>

                                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3"
                                    x-show="type!=='linear_scale'">
                                    <input type="text" name="settings[placeholder]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Placeholder (opsional)" value="{{ $set['placeholder'] ?? '' }}">
                                    <input type="number" name="settings[min]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Min (opsional)" value="{{ $set['min'] ?? '' }}">
                                    <input type="number" name="settings[max]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Max (opsional)" value="{{ $set['max'] ?? '' }}">
                                </div>
                                <p class="mt-2 text-xs text-secondary-500">Sesuaikan dengan kebutuhan tipe pertanyaan.
                                </p>
                            </div>

                            {{-- Options / Grid Items --}}
                            <div class="space-y-4" x-show="supportsOptionEditor()" x-cloak>
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <p class="text-sm font-semibold text-secondary-900">Opsi / Grid Items</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button"
                                            class="rounded-xl border border-secondary-300 bg-white px-3 py-1.5 text-xs font-medium text-secondary-700 hover:bg-secondary-50"
                                            @click="addOption()">
                                            + Tambah Baris
                                        </button>
                                        <p class="text-xs text-secondary-500" x-show="isGridType()" x-cloak>
                                            Pilih role <span class="font-semibold">row/column</span> untuk grid.
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(opt, i) in options" :key="i">
                                        <div
                                            class="rounded-2xl border border-secondary-100 bg-secondary-50/60 p-4 space-y-3">
                                            <template x-if="opt.id">
                                                <input type="hidden" :name="`options[${i}][id]`" x-model="opt.id">
                                            </template>

                                            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                                                <input type="text"
                                                    class="flex-1 rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                                    placeholder="Label" :name="`options[${i}][label]`"
                                                    x-model="opt.label">

                                                <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center">
                                                    <input type="text"
                                                        class="w-full rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                                        placeholder="Value (opsional)" :name="`options[${i}][value]`"
                                                        x-model="opt.value">

                                                    <div class="flex items-center gap-1 sm:ml-auto">
                                                        <button type="button"
                                                            class="rounded-lg border border-secondary-200 px-2 py-1 text-xs text-secondary-700 hover:bg-secondary-50"
                                                            @click="move(i, -1)" :disabled="i === 0">↑</button>
                                                        <button type="button"
                                                            class="rounded-lg border border-secondary-200 px-2 py-1 text-xs text-secondary-700 hover:bg-secondary-50"
                                                            @click="move(i, 1)"
                                                            :disabled="i === options.length - 1">↓</button>
                                                        <button type="button"
                                                            class="rounded-lg bg-danger-50 px-2 py-1 text-xs font-semibold text-danger-600 hover:bg-danger-100"
                                                            @click="removeOption(i)">Hapus</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                                                <select
                                                    class="w-full rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200 lg:w-48"
                                                    :name="`options[${i}][role]`" x-model="opt.role"
                                                    x-show="isGridType()" x-cloak>
                                                    <option value="option">option</option>
                                                    <option value="row">row</option>
                                                    <option value="column">column</option>
                                                </select>
                                                <label
                                                    class="inline-flex items-center gap-2 text-sm text-secondary-800">
                                                    <input type="hidden" :name="`options[${i}][is_other]`"
                                                        value="0">
                                                    <input type="checkbox" :name="`options[${i}][is_other]`"
                                                        value="1"
                                                        class="rounded border-secondary-300 text-primary-600 focus:ring-primary-300"
                                                        x-bind:checked="opt.is_other"
                                                        @change="opt.is_other = $event.target.checked">
                                                    <span>Jadikan opsi “Lainnya”</span>
                                                </label>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                @error('options')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="border-t border-secondary-200 bg-secondary-50/60 px-8 py-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <a href="{{ route('admin.forms.questions.index', $form) }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-primary-200 transition">
                                    Batal
                                </a>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="submit" form="delete-question-form"
                                        class="inline-flex items-center justify-center rounded-xl bg-danger-600 px-4 py-2 text-sm font-semibold text-white hover:bg-danger-700 focus:outline-none focus:ring-2 focus:ring-danger-200 transition"
                                        onclick="return confirm('Hapus pertanyaan ini?');">
                                        Hapus
                                    </button>
                                    <button type="submit" :disabled="submitting"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-200 disabled:cursor-not-allowed disabled:opacity-60 transition">
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

                <aside class="space-y-4">
                    <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-soft">
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                                <i class="bi bi-brush"></i>
                            </span>
                            <div>
                                <h4 class="text-sm font-semibold text-secondary-900">Panduan Konsistensi</h4>
                                <p class="mt-1 text-xs text-secondary-500">Samakan ritme dengan halaman Create agar
                                    pengalaman admin tidak terputus.</p>
                            </div>
                        </div>
                        <ul class="mt-4 space-y-2 text-xs text-secondary-600">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Gunakan bahasa ringkas dan hindari duplikasi pertanyaan.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Susun opsi dari yang paling umum ke khusus.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Tandai pertanyaan penting dengan deskripsi singkat.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-secondary-100 bg-secondary-50/80 p-5 space-y-3">
                        <p class="text-xs font-semibold text-secondary-700">Ringkasan Pertanyaan</p>
                        <dl class="space-y-2 text-sm text-secondary-700">
                            <div class="flex items-center justify-between">
                                <dt>Tipe</dt>
                                <dd class="font-semibold text-secondary-900">
                                    {{ $types[$question->type] ?? ucwords(str_replace('_', ' ', $question->type)) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>Posisi</dt>
                                <dd class="font-semibold text-secondary-900">{{ $question->position }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>Terakhir diubah</dt>
                                <dd class="font-semibold text-secondary-900">
                                    {{ optional($question->updated_at)->diffForHumans() ?? '—' }}</dd>
                            </div>
                        </dl>
                        <p class="text-xs text-secondary-600">Form ini memiliki {{ $form->sections()->count() }}
                            section &
                            {{ $form->questions()->count() }} pertanyaan aktif.</p>
                    </div>
                </aside>
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
                    // jika tipe opsi/grid & belum ada opsi -> tambah satu baris
                    if (this.supportsOptionEditor() && this.options.length === 0) {
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
                        }

                        if (this.supportsOptionEditor() && this.options.length === 0) {
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
                isGridType() {
                    return ['mc_grid', 'checkbox_grid'].includes(this.type)
                },
                supportsOptionEditor() {
                    return this.isOptionsType() || this.isGridType();
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
                    if (this.supportsOptionEditor() && this.options.length === 0) {
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
