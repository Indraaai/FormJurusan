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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-secondary-900 leading-tight">Tambah Pertanyaan</h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Form: <span class="font-semibold text-primary-600">{{ $form->title }}</span>
                </p>
            </div>
            <a href="{{ route('admin.forms.questions.index', $form) }}"
                class="inline-flex items-center gap-2 rounded-xl border border-secondary-200 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">

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
                <div class="rounded-2xl border border-primary-100 bg-white shadow-soft" x-data="questionForm({
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
                    <div class="border-b border-primary-100 p-6">
                        <h3 class="text-lg font-semibold text-secondary-900">Detail Pertanyaan</h3>
                        <p class="mt-1 text-sm text-secondary-500">Susun teks, tipe pertanyaan, dan opsi dengan gaya
                            yang konsisten.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.forms.questions.store', $form) }}"
                        class="flex flex-col" @submit="submitting=true">
                        @csrf

                        <div class="space-y-8 p-8">
                            {{-- Section & Type --}}
                            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                                <div>
                                    <label for="section_id"
                                        class="block text-sm font-semibold text-secondary-900 mb-2">Section</label>
                                    <select id="section_id" name="section_id" required
                                        class="w-full rounded-xl border border-secondary-300 bg-white px-4 py-3 text-secondary-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                                        <option value="">— Pilih Section —</option>
                                        @foreach ($sections as $sec)
                                            <option value="{{ $sec->id }}" @selected(old('section_id') == $sec->id)>
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
                                        class="block text-sm font-semibold text-secondary-900 mb-2">Tipe
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
                                    <p class="mt-2 text-xs text-secondary-500"
                                        x-show="type === 'mc_grid' || type === 'checkbox_grid'" x-cloak>
                                        Untuk tipe Grid, buat pertanyaan dulu lalu tambah Row/Column di halaman Edit.
                                    </p>
                                    <p class="mt-2 text-xs text-secondary-500" x-show="type === 'linear_scale'" x-cloak>
                                        Gunakan <span class="font-medium">Settings</span> untuk mengatur min/max/step
                                        dan label.
                                    </p>
                                </div>
                            </div>

                            {{-- Title & Description --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="title"
                                        class="block text-sm font-semibold text-secondary-900 mb-2">Judul
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
                                        class="block text-sm font-semibold text-secondary-900 mb-2">Deskripsi
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
                                            @checked(old('required', 0))
                                            class="mt-1 rounded border-secondary-300 text-primary-600 focus:ring-primary-300">
                                        <span><span class="font-semibold">Wajib diisi</span><span
                                                class="block text-xs text-secondary-500">Responden tidak bisa lanjut
                                                tanpa menjawab.</span></span>
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
                                        placeholder="Min" value="{{ old('settings.min', 1) }}">
                                    <input type="number" step="1" name="settings[max]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Max" value="{{ old('settings.max', 5) }}">
                                    <input type="text" name="settings[label_left]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Label kiri" value="{{ old('settings.label_left') }}">
                                    <input type="text" name="settings[label_right]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Label kanan" value="{{ old('settings.label_right') }}">
                                    <input type="number" step="1" name="settings[step]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Step" value="{{ old('settings.step', 1) }}">
                                </div>

                                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3"
                                    x-show="type!=='linear_scale'">
                                    <input type="text" name="settings[placeholder]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Placeholder (opsional)"
                                        value="{{ old('settings.placeholder') }}">
                                    <input type="number" name="settings[min]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Min (opsional)" value="{{ old('settings.min') }}">
                                    <input type="number" name="settings[max]"
                                        class="rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                        placeholder="Max (opsional)" value="{{ old('settings.max') }}">
                                </div>
                                <p class="mt-2 text-xs text-secondary-500">Sesuaikan dengan kebutuhan tipe pertanyaan.
                                </p>
                            </div>

                            {{-- Options --}}
                            <template x-if="isOptionsType()">
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                        <p class="text-sm font-semibold text-secondary-900">Opsi Jawaban</p>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <button type="button"
                                                class="rounded-xl border border-secondary-300 bg-white px-3 py-1.5 text-xs font-medium text-secondary-700 hover:bg-secondary-50"
                                                @click="addOption()">
                                                + Tambah Opsi
                                            </button>
                                            <button type="button"
                                                class="rounded-xl border border-secondary-300 bg-white px-3 py-1.5 text-xs font-medium text-secondary-700 hover:bg-secondary-50"
                                                @click="quickAdd('1-5')">
                                                1–5
                                            </button>
                                            <button type="button"
                                                class="rounded-xl border border-secondary-300 bg-white px-3 py-1.5 text-xs font-medium text-secondary-700 hover:bg-secondary-50"
                                                @click="quickAdd('yn')">
                                                Ya/Tidak
                                            </button>
                                            <button type="button"
                                                class="rounded-xl border border-secondary-300 bg-white px-3 py-1.5 text-xs font-medium text-secondary-700 hover:bg-secondary-50"
                                                @click="showBulk = !showBulk">
                                                Tempel massal
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <template x-for="(opt, i) in options" :key="i">
                                            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                                                <input type="text"
                                                    class="flex-1 rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200"
                                                    placeholder="Label" :name="`options[${i}][label]`"
                                                    x-model="opt.label">
                                                <div class="flex w-full items-center gap-2 md:w-auto">
                                                    <input type="text"
                                                        class="w-full rounded-xl border border-secondary-300 px-3 py-2 text-secondary-900 focus:border-primary-500 focus:ring-primary-200 md:w-56"
                                                        placeholder="Value (opsional)" :name="`options[${i}][value]`"
                                                        x-model="opt.value">
                                                    <div class="flex items-center gap-1">
                                                        <button type="button"
                                                            class="rounded-lg border border-secondary-200 px-2 py-1 text-xs text-secondary-700 hover:bg-secondary-50"
                                                            @click="move(i, -1)" :disabled="i === 0">↑</button>
                                                        <button type="button"
                                                            class="rounded-lg border border-secondary-200 px-2 py-1 text-xs text-secondary-700 hover:bg-secondary-50"
                                                            @click="move(i, +1)"
                                                            :disabled="i === options.length - 1">↓</button>
                                                        <button type="button"
                                                            class="rounded-lg bg-danger-50 px-2 py-1 text-xs text-danger-600 hover:bg-danger-100"
                                                            @click="removeOption(i)">Hapus</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="rounded-xl border border-primary-200 bg-primary-50/60 p-5 space-y-3"
                                        x-show="showBulk" x-cloak
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0">

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-secondary-900">Tempel Massal</p>
                                                <p class="text-xs text-secondary-500">Tulis atau paste daftar opsi, 1
                                                    baris = 1 opsi.</p>
                                            </div>
                                            <button type="button" @click="showBulk = false"
                                                class="rounded-lg p-1 text-secondary-400 hover:text-secondary-700 hover:bg-secondary-100 transition">
                                                <i class="bi bi-x-lg text-sm"></i>
                                            </button>
                                        </div>

                                        <textarea rows="6" x-model="bulkText" x-ref="bulkTextarea"
                                            class="w-full rounded-xl border border-secondary-300 bg-white px-4 py-3 text-sm text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 font-mono leading-relaxed"
                                            placeholder="Sangat setuju&#10;Setuju&#10;Netral&#10;Tidak setuju&#10;Sangat tidak setuju"></textarea>

                                        <div
                                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex items-center gap-4">
                                                <p class="text-xs text-secondary-500">
                                                    <span class="font-semibold"
                                                        x-text="(bulkText || '').split('\n').filter(l => l.trim()).length">0</span>
                                                    opsi terdeteksi
                                                </p>
                                                <label
                                                    class="flex items-center gap-2 text-xs text-secondary-600 cursor-pointer">
                                                    <input type="checkbox" x-model="bulkReplace"
                                                        class="rounded border-secondary-300 text-primary-600 focus:ring-primary-300">
                                                    <span>Ganti semua opsi yang ada</span>
                                                </label>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                    class="rounded-xl border border-secondary-300 bg-white px-4 py-2 text-xs font-medium text-secondary-700 hover:bg-secondary-50 transition"
                                                    @click="bulkText = ''">
                                                    Bersihkan
                                                </button>
                                                <button type="button"
                                                    class="rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                                    :disabled="!(bulkText || '').split('\n').filter(l => l.trim()).length"
                                                    @click="applyBulk()">
                                                    <span x-text="bulkReplace ? 'Ganti Opsi' : 'Tambahkan'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @error('options')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </template>
                        </div>

                        <div class="border-t border-secondary-200 bg-secondary-50/50 px-8 py-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                                <a href="{{ route('admin.forms.questions.index', $form) }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-secondary-300 bg-white px-5 py-2.5 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                    Batal
                                </a>
                                <button type="submit" :disabled="submitting"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-60 disabled:cursor-not-allowed transition">
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
                    </form>
                </div>

                <aside class="space-y-4">
                    <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-soft">
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                                <i class="bi bi-lightbulb"></i>
                            </span>
                            <div>
                                <h4 class="text-sm font-semibold text-secondary-900">Tips Penyusunan Pertanyaan</h4>
                                <p class="mt-1 text-xs text-secondary-500">Gunakan bahasa singkat dan langsung ke inti.
                                </p>
                            </div>
                        </div>
                        <ul class="mt-4 space-y-2 text-xs text-secondary-600">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Tambahkan deskripsi jika pertanyaan butuh konteks.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Gunakan opsi “Lainnya” untuk jawaban terbuka.
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success-500 mt-0.5"></i>
                                Aktifkan acak opsi untuk menghindari bias.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-secondary-100 bg-secondary-50/80 p-5">
                        <p class="text-xs font-semibold text-secondary-700">Statistik Cepat</p>
                        <p class="mt-2 text-xs text-secondary-600">Form ini memiliki {{ $form->sections()->count() }}
                            section & {{ $form->questions()->count() }} pertanyaan. Pastikan pertanyaan baru mengikuti
                            alur.</p>
                    </div>
                </aside>
            </div>
        </div>
    </div>

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
                type: initialType || 'short_text',
                title: initialTitle || '',
                description: initialDescription || '',
                submitting: false,
                options: Array.isArray(initialOptions) && initialOptions.length ? initialOptions : [],
                showBulk: false,
                bulkText: '',
                bulkReplace: true,
                shuffleChecked: !!initialShuffle,
                otherEnabled: !!initialOtherEnabled,
                otherLabel: initialOtherLabel || 'Lainnya',
                otherPlaceholder: initialOtherPlaceholder || '',
                otherTextRequired: !!initialOtherTextRequired,

                init() {
                    if (this.isOptionsType() && this.options.length === 0) {
                        this.options.push({
                            label: '',
                            value: ''
                        });
                    }
                    this.$watch('type', (value) => {
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
                    return ['multiple_choice', 'checkboxes', 'dropdown'].includes(this.type);
                },

                autoresize(event) {
                    const el = event.target;
                    el.style.height = 'auto';
                    el.style.height = `${el.scrollHeight}px`;
                },

                addOption() {
                    this.options.push({
                        label: '',
                        value: ''
                    });
                },
                removeOption(index) {
                    this.options.splice(index, 1);
                    if (this.options.length === 0) {
                        this.options.push({
                            label: '',
                            value: ''
                        });
                    }
                },
                move(index, delta) {
                    const next = index + delta;
                    if (next < 0 || next >= this.options.length) return;
                    const temp = this.options[index];
                    this.options[index] = this.options[next];
                    this.options[next] = temp;
                },
                quickAdd(kind) {
                    if (kind === '1-5') {
                        this.options = [1, 2, 3, 4, 5].map((n) => ({
                            label: String(n),
                            value: String(n)
                        }));
                    }
                    if (kind === 'yn') {
                        this.options = [{
                                label: 'Ya',
                                value: 'ya'
                            },
                            {
                                label: 'Tidak',
                                value: 'tidak'
                            }
                        ];
                    }
                },
                applyBulk() {
                    const lines = (this.bulkText || '').split('\n').map((line) => line.trim()).filter(Boolean);
                    if (!lines.length) return;
                    const newOpts = lines.map((line) => ({
                        label: line,
                        value: ''
                    }));
                    if (this.bulkReplace) {
                        this.options = newOpts;
                    } else {
                        // Remove empty trailing options before appending
                        const cleaned = this.options.filter((o) => o.label.trim() || o.value.trim());
                        this.options = [...cleaned, ...newOpts];
                    }
                    this.bulkText = '';
                    this.showBulk = false;
                }
            }
        }
    </script>
</x-app-layout>
