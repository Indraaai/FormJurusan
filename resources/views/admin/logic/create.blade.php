@php
    $operators = ['=', '!=', 'contains', 'in', 'between', '>=', '<=', 'answered', 'not_answered'];
    $actions = ['goto_section' => 'Goto Section', 'submit' => 'Submit (akhiri form)'];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Tambah Logic Rule — {{ $form->title }}
            </h2>
            <a href="{{ route('admin.forms.logic.index', $form) }}"
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

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="logicCreate()"
                x-init="init()">

                <form method="POST" action="{{ route('admin.forms.logic.store', $form) }}"
                    @submit.prevent="beforeSubmit($event)">
                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Source Question --}}
                        <div>
                            <label for="source_question_id"
                                class="block text-sm font-medium text-emerald-900">Pertanyaan Sumber</label>
                            <select id="source_question_id" name="source_question_id" required
                                class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                <option value="">— Pilih pertanyaan —</option>
                                @foreach ($questions as $q)
                                    <option value="{{ $q->id }}" @selected(old('source_question_id') == $q->id)">
                                        Sec#{{ $q->section->position }} • Q#{{ $q->position }} —
                                        {{ \Illuminate\Support\Str::limit($q->title, 80) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('source_question_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Operator --}}
                        <div>
                            <label for="op" class="block text-sm font-medium text-emerald-900">Operator</label>
                            <select id="op" name="operator" x-model="op" @change="onOpChange()"
                                class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300"
                                required>
                                @foreach ($operators as $o)
                                    <option value="{{ $o }}" @selected(old('operator') === $o)>
                                        {{ $o }}</option>
                                @endforeach
                            </select>
                            @error('operator')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-emerald-700/70" x-text="hint"></p>
                        </div>

                        {{-- Values (dinamis) --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2" x-cloak>

                            {{-- Text value --}}
                            <div x-show="showText()">
                                <label class="block text-sm font-medium text-emerald-900">Value (Text)</label>
                                <input type="text" x-model="textVal" placeholder="mis. Ya / Jakarta / A,B,C untuk IN"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2 placeholder:text-emerald-900/40">
                                @error('value_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Number value --}}
                            <div x-show="showNumberSingle()">
                                <label class="block text-sm font-medium text-emerald-900">Value (Number)</label>
                                <input type="number" step="any" x-model="numVal" placeholder="mis. 5 / 10.5"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                                @error('value_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Between (min/max number) --}}
                            <div class="md:col-span-2" x-show="op==='between'">
                                <label class="block text-sm font-medium text-emerald-900">Between (Number)</label>
                                <div class="mt-1 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <input type="number" step="any" x-model="minVal" placeholder="min"
                                        class="rounded-xl border border-emerald-200 px-3 py-2">
                                    <input type="number" step="any" x-model="maxVal" placeholder="max"
                                        class="rounded-xl border border-emerald-200 px-3 py-2">
                                </div>
                                <p class="mt-1 text-xs text-emerald-700/70">Nilai akan dikirim sebagai
                                    <code>value_text</code> dengan format <strong>min,max</strong>.</p>
                            </div>

                            {{-- Option ID (opsional) --}}
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-emerald-900">Option ID
                                        (opsional)</label>
                                    <span class="text-xs text-emerald-700/70">Untuk pilihan spesifik (Multiple
                                        choice/Checkbox/Dropdown)</span>
                                </div>
                                <input type="number" x-model="optionId"
                                    placeholder="ID opsi spesifik (lihat di halaman Edit Pertanyaan)"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2">
                                @error('option_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Hidden fields untuk submit ke backend TANPA ubah struktur --}}
                        <input type="hidden" name="value_text" :value="packedValueText">
                        <input type="hidden" name="value_number" :value="packedValueNumber">
                        <input type="hidden" name="option_id" :value="optionId || ''">

                        {{-- Action & Target --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="action" class="block text-sm font-medium text-emerald-900">Action</label>
                                <select id="action" name="action" x-model="action" @change="syncAction()"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300"
                                    required>
                                    @foreach ($actions as $k => $label)
                                        <option value="{{ $k }}" @selected(old('action', 'goto_section') === $k)>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('action')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="target_section_wrap" :class="action === 'submit' ? 'opacity-50' : ''">
                                <label for="target_section_id" class="block text-sm font-medium text-emerald-900">Target
                                    Section</label>
                                <select id="target_section_id" name="target_section_id" :disabled="action === 'submit'"
                                    class="mt-1 w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    <option value="">— Pilih section —</option>
                                    @foreach ($sections as $sec)
                                        <option value="{{ $sec->id }}" @selected(old('target_section_id') == $sec->id)">
                                            Sec#{{ $sec->position }} — {{ $sec->title ?? 'Tanpa judul' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('target_section_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Priority & Enabled --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="priority"
                                    class="block text-sm font-medium text-emerald-900">Prioritas</label>
                                <input id="priority" type="number" name="priority" min="0"
                                    value="{{ old('priority', 0) }}"
                                    class="mt-1 w-40 rounded-xl border border-emerald-200 px-3 py-2">
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <label class="mt-7 flex items-center gap-2">
                                <input type="hidden" name="is_enabled" value="0">
                                <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', 1))>
                                <span class="text-sm text-emerald-900">Enabled</span>
                            </label>
                        </div>

                        {{-- Client-side error --}}
                        <p class="text-sm text-red-600" x-show="clientError" x-text="clientError"></p>
                    </div>

                    {{-- Sticky footer actions --}}
                    <div
                        class="sticky bottom-0 -mx-6 rounded-b-2xl border-t border-emerald-100 bg-white/90 px-6 py-4 backdrop-blur supports-[backdrop-filter]:bg-white/70">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.forms.logic.index', $form) }}"
                                class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-emerald-800 hover:bg-emerald-50">
                                Batal
                            </a>
                            <button type="submit" :disabled="submitting"
                                class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                                Simpan Rule
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tips --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-6">
                <h3 class="font-semibold text-emerald-900 mb-2">Tips</h3>
                <ul class="list-inside list-disc space-y-1 text-sm text-emerald-800">
                    <li><strong>answered / not_answered</strong> tidak butuh nilai (field nilai akan otomatis
                        dinonaktifkan).</li>
                    <li><strong>in</strong>: isi <em>Value (Text)</em> dengan daftar koma, contoh: <code>A,B,C</code>.
                    </li>
                    <li><strong>between</strong>: isi angka min & max; sistem mengirim <code>value_text</code> dengan
                        format <code>min,max</code>.</li>
                    <li>Jika action = <em>submit</em>, biarkan Target Section kosong (otomatis dinonaktifkan).</li>
                </ul>
            </div>

        </div>
    </div>

    <script>
        function logicCreate() {
            return {
                // state
                op: @js(old('operator', '=')),
                action: @js(old('action', 'goto_section')),
                // nilai UI (bukan langsung hidden)
                textVal: @js(old('value_text', '')),
                numVal: @js(old('value_number', '')),
                minVal: '',
                maxVal: '',
                optionId: @js(old('option_id', '')),
                // packed hidden values
                packedValueText: '',
                packedValueNumber: '',
                // lainnya
                hint: '',
                clientError: '',
                submitting: false,

                init() {
                    this.onOpChange();
                    this.syncAction();
                    // jika user mengisi between sebelumnya, coba parse "min,max" dari old('value_text')
                    if (this.op === 'between' && this.textVal && this.textVal.includes(',')) {
                        const [mi, ma] = this.textVal.split(',');
                        this.minVal = mi?.trim() ?? '';
                        this.maxVal = ma?.trim() ?? '';
                    }
                },

                showText() {
                    return ['=', '!=', 'contains', 'in'].includes(this.op)
                },
                showNumberSingle() {
                    return ['=', '!=', '>=', '<='].includes(this.op)
                },

                onOpChange() {
                    const map = {
                        '=': 'Bandingkan nilai sama persis (teks/angka/option_id).',
                        '!=': 'Bandingkan nilai tidak sama (teks/angka/option_id).',
                        'contains': 'Cocokkan substring pada teks atau salah satu dari checkbox.',
                        'in': 'Cocok dengan salah satu nilai dari daftar koma. Contoh: A,B,C',
                        'between': 'Cocokkan dalam rentang angka (min sampai max).',
                        '>=': 'Lebih besar atau sama dengan (angka).',
                        '<=': 'Lebih kecil atau sama dengan (angka).',
                        'answered': 'Terpenuhi jika pertanyaan sudah dijawab.',
                        'not_answered': 'Terpenuhi jika pertanyaan belum dijawab.',
                    };
                    this.hint = map[this.op] || '';
                },

                syncAction() {
                    const sel = document.getElementById('target_section_id');
                    if (!sel) return;
                    if (this.action === 'submit') {
                        sel.value = '';
                        sel.setAttribute('disabled', 'disabled');
                    } else {
                        sel.removeAttribute('disabled');
                    }
                },

                // packing nilai ke field yang backend pakai
                packValues() {
                    // reset
                    this.packedValueText = '';
                    this.packedValueNumber = '';

                    if (['answered', 'not_answered'].includes(this.op)) {
                        // tidak kirim nilai
                        return true;
                    }

                    if (this.op === 'between') {
                        if (this.minVal === '' || this.maxVal === '') {
                            this.clientError = 'Untuk operator "between", isi angka min dan max.';
                            return false;
                        }
                        this.packedValueText = `${this.minVal},${this.maxVal}`;
                        return true;
                    }

                    if (['contains', 'in'].includes(this.op)) {
                        if (!this.textVal.trim()) {
                            this.clientError = 'Isi Value (Text) untuk operator ini.';
                            return false;
                        }
                        this.packedValueText = this.textVal.trim();
                        return true;
                    }

                    if (['>=', '<='].includes(this.op)) {
                        if (this.numVal === '') {
                            this.clientError = 'Isi Value (Number) untuk operator angka.';
                            return false;
                        }
                        this.packedValueNumber = String(this.numVal);
                        return true;
                    }

                    // '=' atau '!=' → pilih prioritas: option_id > number > text
                    if (['=', '!='].includes(this.op)) {
                        if (String(this.optionId || '').trim() !== '') {
                            // gunakan option_id, lainnya biarkan kosong
                            return true;
                        }
                        if (this.numVal !== '') {
                            this.packedValueNumber = String(this.numVal);
                            return true;
                        }
                        if (this.textVal.trim() !== '') {
                            this.packedValueText = this.textVal.trim();
                            return true;
                        }
                        this.clientError = 'Isi salah satu: Option ID / Value (Number) / Value (Text).';
                        return false;
                    }

                    return true;
                },

                beforeSubmit(e) {
                    this.clientError = '';
                    if (!this.packValues()) {
                        // scroll to error
                        this.$nextTick(() => e.target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        }));
                        return;
                    }
                    this.submitting = true;
                    this.$nextTick(() => e.target.submit());
                },
            }
        }
    </script>
</x-app-layout>
