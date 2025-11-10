@php
    $extras = old('extras', $validation->extras ?? []);
    $oldMimes = is_array($extras['mimes'] ?? null) ? implode(',', $extras['mimes']) : $extras['mimes'] ?? '';
    $oldMtypes = is_array($extras['mimetypes'] ?? null)
        ? implode(',', $extras['mimetypes'])
        : $extras['mimetypes'] ?? '';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Edit Validation — Q#{{ $question->position }} ({{ Str::limit($question->title, 60) }})
            </h2>
            <a href="{{ route('admin.questions.validations.index', $question) }}"
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
                    <div class="mb-1 font-semibold">Periksa input:</div>
                    <ul class="list-inside list-disc text-sm">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="valEdit({
                t: @js(old('validation_type', $validation->validation_type)),
                minValue: @js(old('min_value', $validation->min_value)),
                maxValue: @js(old('max_value', $validation->max_value)),
                pattern: @js(old('pattern', $validation->pattern)),
                minDate: @js(old('extras.min_date', $extras['min_date'] ?? '')),
                maxDate: @js(old('extras.max_date', $extras['max_date'] ?? '')),
                minTime: @js(old('extras.min_time', $extras['min_time'] ?? '')),
                maxTime: @js(old('extras.max_time', $extras['max_time'] ?? '')),
                mimes: @js($oldMimes),
                mtypes: @js($oldMtypes),
                message: @js(old('message', $validation->message)),
            })"
                x-init="init()">

                <form method="POST" action="{{ route('admin.questions.validations.update', $validation) }}"
                    @submit.prevent="beforeSubmit($event)">
                    @csrf
                    @method('PUT')

                    <div class="p-6 space-y-6">
                        {{-- TIPE --}}
                        <div>
                            <label class="block text-sm font-medium text-emerald-900">Tipe</label>
                            <select name="validation_type" x-model="t" @change="onTypeChange()"
                                class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300"
                                required>
                                @foreach (\App\Models\QuestionValidation::TYPES as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-emerald-700/70" x-text="hint"></p>
                        </div>

                        {{-- MIN/MAX --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2" x-show="showMinMax()" x-cloak>
                            <div>
                                <label class="block text-sm font-medium text-emerald-900">Min Value</label>
                                <input type="number" step="1" name="min_value" x-model="minValue"
                                    :disabled="t === 'file_size'"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                                @error('min_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-emerald-900">Max Value</label>
                                <input type="number" step="1" name="max_value" x-model="maxValue"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                                <div class="mt-1 text-xs text-emerald-700/70" x-show="t==='file_size'">Satuan: KB</div>
                                @error('max_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- REGEX --}}
                        <div x-show="t==='regex'" x-cloak>
                            <label class="block text-sm font-medium text-emerald-900">Pattern</label>
                            <input type="text" name="pattern" x-model="pattern" placeholder="\d{5,}"
                                class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2 placeholder:text-emerald-900/40">
                            <p class="mt-1 text-xs text-emerald-700/70">Contoh: <code>^[A-Za-z0-9_]{4,12}$</code></p>
                            @error('pattern')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- DATE RANGE --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2" x-show="t==='date_range'" x-cloak>
                            <div>
                                <label class="block text-sm font-medium text-emerald-900">Min Date (Y-m-d)</label>
                                <input type="date" name="extras[min_date]" x-model="minDate"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-emerald-900">Max Date (Y-m-d)</label>
                                <input type="date" name="extras[max_date]" x-model="maxDate"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                            </div>
                        </div>

                        {{-- TIME RANGE --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2" x-show="t==='time_range'" x-cloak>
                            <div>
                                <label class="block text-sm font-medium text-emerald-900">Min Time (HH:mm)</label>
                                <input type="time" name="extras[min_time]" x-model="minTime"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-emerald-900">Max Time (HH:mm)</label>
                                <input type="time" name="extras[max_time]" x-model="maxTime"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                            </div>
                        </div>

                        {{-- FILE TYPE --}}
                        <div x-show="t==='file_type'" x-cloak>
                            <label class="block text-sm font-medium text-emerald-900">Mimes atau Mime-types</label>
                            <input type="text" name="extras[mimes]" x-model="mimes"
                                class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2"
                                placeholder="jpg,png,pdf (opsional)">
                            <div class="mt-1 text-xs text-emerald-700/70">
                                Atau <code>extras[mimetypes]</code> (mis: image/jpeg,application/pdf)
                            </div>
                            <input type="text" name="extras[mimetypes]" x-model="mtypes"
                                class="mt-2 w-full rounded-xl border border-emerald-200 px-3 py-2"
                                placeholder="image/jpeg,application/pdf (opsional)">
                        </div>

                        {{-- REQUIRED (info) --}}
                        <template x-if="t==='required'">
                            <div
                                class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-3 text-sm text-emerald-900">
                                Tipe <strong>required</strong> tidak membutuhkan parameter lainnya.
                            </div>
                        </template>

                        {{-- MESSAGE --}}
                        <div>
                            <label class="block text-sm font-medium text-emerald-900">Custom Message (opsional)</label>
                            <input type="text" name="message" x-model="message" placeholder="Pesan error khusus"
                                class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- RINGKASAN --}}
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-3 text-sm text-emerald-900">
                            <div class="font-medium">Ringkasan</div>
                            <div class="mt-1 text-emerald-800" x-text="summary()"></div>
                        </div>

                        {{-- Client-side error --}}
                        <p class="text-sm text-red-600" x-show="clientError" x-text="clientError"></p>
                    </div>

                    {{-- STICKY ACTIONS --}}
                    <div
                        class="sticky bottom-0 -mx-6 rounded-b-2xl border-t border-emerald-100 bg-white/90 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-white/70">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.questions.validations.index', $question) }}"
                                class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-emerald-800 hover:bg-emerald-50">
                                Batal
                            </a>
                            <div class="flex gap-2">
                                {{-- DELETE pakai form terpisah --}}
                                <button type="submit" form="delete-validation-form"
                                    class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                                    onclick="return confirm('Hapus validation ini?')">
                                    Hapus
                                </button>
                                <button type="submit" :disabled="submitting"
                                    class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- FORM DELETE TERPISAH --}}
            <form id="delete-validation-form" method="POST"
                action="{{ route('admin.questions.validations.destroy', $validation) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>

    <script>
        function valEdit(cfg) {
            return {
                // state
                t: cfg.t || 'text_length',
                minValue: cfg.minValue ?? '',
                maxValue: cfg.maxValue ?? '',
                pattern: cfg.pattern ?? '',
                minDate: cfg.minDate ?? '',
                maxDate: cfg.maxDate ?? '',
                minTime: cfg.minTime ?? '',
                maxTime: cfg.maxTime ?? '',
                mimes: cfg.mimes ?? '',
                mtypes: cfg.mtypes ?? '',
                message: cfg.message ?? '',
                hint: '',
                clientError: '',
                submitting: false,

                init() {
                    this.onTypeChange();
                },

                onTypeChange() {
                    const map = {
                        text_length: 'Batasi panjang teks (min/max karakter).',
                        regex: 'Cocokkan pola teks dengan Regular Expression.',
                        number_range: 'Batasi nilai numerik (min/max).',
                        date_range: 'Batasi tanggal (min/max) format YYYY-MM-DD.',
                        time_range: 'Batasi waktu (min/max) format HH:mm.',
                        file_type: 'Batasi jenis berkas (mimes/mimetypes).',
                        file_size: 'Batasi ukuran berkas (max dalam KB).',
                        required: 'Wajib diisi, tanpa parameter tambahan.',
                    };
                    this.hint = map[this.t] || '';
                    this.clientError = '';
                },

                showMinMax() {
                    return ['text_length', 'number_range', 'file_size'].includes(this.t)
                },

                summary() {
                    const t = this.t;
                    if (t === 'text_length' || t === 'number_range') {
                        return `${t}: min=${this.minValue || '—'}, max=${this.maxValue || '—'}`;
                    }
                    if (t === 'file_size') {
                        return `file_size: max=${this.maxValue || '—'} KB`;
                    }
                    if (t === 'regex') {
                        return `regex: ${this.pattern || '—'}`;
                    }
                    if (t === 'date_range') {
                        return `date_range: min=${this.minDate || '—'}, max=${this.maxDate || '—'}`;
                    }
                    if (t === 'time_range') {
                        return `time_range: min=${this.minTime || '—'}, max=${this.maxTime || '—'}`;
                    }
                    if (t === 'file_type') {
                        return `file_type: mimes=[${(this.mimes||'').trim()}], mimetypes=[${(this.mtypes||'').trim()}]`;
                    }
                    if (t === 'required') {
                        return 'required';
                    }
                    return '';
                },

                // validasi ringan client-side (opsional)
                validateClient() {
                    if (this.t === 'regex' && !this.pattern.trim()) {
                        this.clientError = 'Isi pattern untuk tipe regex.';
                        return false;
                    }
                    if (this.t === 'file_size' && (this.maxValue === '' || this.maxValue === null)) {
                        this.clientError = 'Isi maksimal ukuran berkas (KB) untuk file_size.';
                        return false;
                    }
                    if (this.t === 'date_range') {
                        if (this.minDate && this.maxDate && this.minDate > this.maxDate) {
                            this.clientError = 'Min Date tidak boleh lebih besar dari Max Date.';
                            return false;
                        }
                    }
                    if (this.t === 'time_range') {
                        if (this.minTime && this.maxTime && this.minTime > this.maxTime) {
                            this.clientError = 'Min Time tidak boleh lebih besar dari Max Time.';
                            return false;
                        }
                    }
                    if (this.t === 'text_length' || this.t === 'number_range') {
                        if (this.minValue !== '' && this.maxValue !== '' && Number(this.minValue) > Number(this.maxValue)) {
                            this.clientError = 'Min Value tidak boleh lebih besar dari Max Value.';
                            return false;
                        }
                    }
                    this.clientError = '';
                    return true;
                },

                beforeSubmit(e) {
                    if (!this.validateClient()) {
                        this.$nextTick(() => e.target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        }));
                        return;
                    }
                    this.submitting = true;
                    this.$nextTick(() => e.target.submit());
                }
            }
        }
    </script>
</x-app-layout>
