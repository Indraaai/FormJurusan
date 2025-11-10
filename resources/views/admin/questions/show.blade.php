<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Detail Pertanyaan — {{ $question->section->form->title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.questions.edit', $question) }}"
                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Edit
                </a>
                <a href="{{ route('admin.forms.questions.index', $question->section->form) }}"
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
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">

            {{-- Informasi utama --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-emerald-700/70">Section</div>
                        <div class="mt-1 font-medium text-emerald-900">
                            <span
                                class="mr-1 inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">
                                #{{ $question->section->position }}
                            </span>
                            {{ $question->section->title ?? 'Tanpa judul' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-emerald-700/70">Tipe</div>
                        <div class="mt-1">
                            <span
                                class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-800">
                                {{ strtoupper(str_replace('_', ' ', $question->type)) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-emerald-700/70">Posisi</div>
                        <div class="mt-1">
                            <span
                                class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-sm text-emerald-800">
                                {{ $question->position }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="text-xs uppercase tracking-wide text-emerald-700/70">Judul</div>
                    <div class="mt-1 font-medium text-emerald-900">{{ $question->title }}</div>
                </div>

                @if ($question->description)
                    <div class="mt-6">
                        <div class="text-xs uppercase tracking-wide text-emerald-700/70">Deskripsi</div>
                        <div class="mt-2 rounded-xl border border-emerald-100 bg-emerald-50/40 p-4 text-emerald-900">
                            {!! nl2br(e($question->description)) !!}
                        </div>
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-3">
                        <div class="text-xs text-emerald-700/70">Wajib</div>
                        <div class="mt-1">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs
                                {{ $question->required ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                <span
                                    class="h-1.5 w-1.5 rounded-full {{ $question->required ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $question->required ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-3">
                        <div class="text-xs text-emerald-700/70">Acak opsi</div>
                        <div class="mt-1">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs
                                {{ $question->shuffle_options ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                <span
                                    class="h-1.5 w-1.5 rounded-full {{ $question->shuffle_options ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $question->shuffle_options ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-3">
                        <div class="text-xs text-emerald-700/70">Opsi “Lainnya”</div>
                        <div class="mt-1">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs
                                {{ $question->other_option_enabled ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                <span
                                    class="h-1.5 w-1.5 rounded-full {{ $question->other_option_enabled ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $question->other_option_enabled ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if (!empty($question->settings))
                    <div class="mt-6" x-data="{ copied: false }" x-cloak>
                        <div class="flex items-center justify-between">
                            <div class="text-xs uppercase tracking-wide text-emerald-700/70">Settings</div>
                            <button type="button"
                                class="inline-flex items-center rounded-lg border border-emerald-200 bg-white px-2.5 py-1 text-xs text-emerald-800 hover:bg-emerald-50"
                                @click="navigator.clipboard.writeText(@js(json_encode($question->settings, JSON_UNESCAPED_SLASHES))).then(()=>{copied=true; setTimeout(()=>copied=false,1500)})">
                                <span x-show="!copied">Copy JSON</span>
                                <span x-show="copied">Tersalin ✓</span>
                            </button>
                        </div>
                        <pre class="mt-2 max-h-64 overflow-auto rounded-xl bg-gray-50 p-3 text-xs">{{ json_encode($question->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                @endif
            </div>

            {{-- Preview tampilan pertanyaan (read-only) --}}
            @php $type = $question->type; @endphp
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 p-6">
                    <div>
                        <h3 class="font-semibold text-emerald-900">Preview</h3>
                        <p class="text-sm text-emerald-700/70">Pratinjau tampilan pertanyaan untuk responden.</p>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <label class="mb-2 block text-sm font-medium text-emerald-900">
                        {{ $question->title }}
                        @if ($question->required)
                            <span class="text-red-600">*</span>
                        @endif
                    </label>

                    @if (in_array($type, ['short_text']))
                        <input type="text" class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2"
                            placeholder="Jawaban anda" disabled>
                    @elseif(in_array($type, ['long_text']))
                        <textarea rows="4" class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2"
                            placeholder="Jawaban anda" disabled></textarea>
                    @elseif(in_array($type, ['multiple_choice']))
                        <div class="space-y-2">
                            @foreach ($question->options as $opt)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" disabled>
                                    <span>{{ $opt->label }} @if ($opt->is_other)
                                            <span
                                                class="ml-1 rounded bg-emerald-50 px-1.5 py-0.5 text-[11px] text-emerald-800">Lainnya</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @elseif(in_array($type, ['checkboxes']))
                        <div class="space-y-2">
                            @foreach ($question->options as $opt)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" disabled>
                                    <span>{{ $opt->label }} @if ($opt->is_other)
                                            <span
                                                class="ml-1 rounded bg-emerald-50 px-1.5 py-0.5 text-[11px] text-emerald-800">Lainnya</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @elseif(in_array($type, ['dropdown']))
                        <select class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2" disabled>
                            <option>Pilih...</option>
                            @foreach ($question->options as $opt)
                                <option>{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    @elseif(in_array($type, ['linear_scale']))
                        @php
                            $min = data_get($question->settings, 'min', 1);
                            $max = data_get($question->settings, 'max', 5);
                            $left = data_get($question->settings, 'label_left');
                            $right = data_get($question->settings, 'label_right');
                            $step = data_get($question->settings, 'step', 1);
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-emerald-700/70">{{ $left }}</span>
                            <input type="range" class="flex-1" min="{{ $min }}" max="{{ $max }}"
                                step="{{ $step }}" disabled>
                            <span class="text-sm text-emerald-700/70">{{ $right }}</span>
                        </div>
                        <div class="mt-1 text-xs text-emerald-700/70">Skala {{ $min }}–{{ $max }}
                            (step {{ $step }})</div>
                    @elseif(in_array($type, ['date']))
                        <input type="date" class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2"
                            disabled>
                    @elseif(in_array($type, ['time']))
                        <input type="time" class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2"
                            disabled>
                    @elseif(in_array($type, ['file_upload']))
                        <input type="file" class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2"
                            disabled>
                    @elseif(in_array($type, ['mc_grid', 'checkbox_grid']))
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4 text-sm text-emerald-900">
                            Pratinjau grid tidak ditampilkan di sini. Kelola baris/kolom pada halaman Edit.
                        </div>
                    @else
                        <div class="text-sm text-emerald-700/70">Tipe tidak dikenali untuk pratinjau.</div>
                    @endif

                    @if ($question->description)
                        <p class="mt-2 text-sm text-emerald-700/80">{!! nl2br(e($question->description)) !!}</p>
                    @endif
                </div>
            </div>

            {{-- Opsi / Grid items --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <div class="p-6">
                    <h3 class="font-semibold text-emerald-900">Opsi / Grid Items</h3>
                    @if ($question->options->isEmpty())
                        <p class="mt-2 text-emerald-700/70">Belum ada opsi.</p>
                    @else
                        {{-- Desktop table --}}
                        <div class="mt-3 hidden sm:block">
                            <div class="overflow-x-auto rounded-2xl">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-emerald-50/60 text-emerald-900">
                                        <tr>
                                            <th class="py-3.5 pl-6 pr-4 text-left font-medium">Role</th>
                                            <th class="py-3.5 px-4 text-left font-medium">Label</th>
                                            <th class="py-3.5 px-4 text-left font-medium">Value</th>
                                            <th class="py-3.5 px-4 text-left font-medium">Posisi</th>
                                            <th class="py-3.5 pr-6 pl-4 text-left font-medium">Lainnya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-100">
                                        @foreach ($question->options as $opt)
                                            <tr class="hover:bg-emerald-50/40 transition">
                                                <td class="py-3.5 pl-6 pr-4 text-emerald-900">{{ $opt->role ?? '-' }}
                                                </td>
                                                <td class="py-3.5 px-4 text-emerald-900">{{ $opt->label ?? '—' }}</td>
                                                <td class="py-3.5 px-4 text-emerald-900/80">{{ $opt->value ?? '—' }}
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span
                                                        class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">
                                                        {{ $opt->position }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 pr-6 pl-4">
                                                    @if ($opt->is_other)
                                                        <span
                                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs text-emerald-800">
                                                            <span
                                                                class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                            Ya
                                                        </span>
                                                    @else
                                                        <span class="text-emerald-700/60">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Mobile cards --}}
                        <div class="sm:hidden">
                            <ul class="divide-y divide-emerald-100">
                                @foreach ($question->options as $opt)
                                    <li class="py-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="text-xs text-emerald-700/70">{{ $opt->role ?? '-' }}</div>
                                                <div class="font-medium text-emerald-900">{{ $opt->label ?? '—' }}
                                                </div>
                                                <div class="mt-1 text-sm text-emerald-900/80">Value:
                                                    {{ $opt->value ?? '—' }}</div>
                                                <div class="mt-1 text-xs text-emerald-700/70">Posisi:
                                                    <span
                                                        class="rounded bg-emerald-50 px-1.5 py-0.5 text-emerald-800">{{ $opt->position }}</span>
                                                    @if ($opt->is_other)
                                                        <span
                                                            class="ml-2 rounded bg-emerald-100 px-1.5 py-0.5 text-emerald-800">Lainnya</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Validasi --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <div class="p-6">
                    <h3 class="font-semibold text-emerald-900">Validasi</h3>
                    @if ($question->validations->isEmpty())
                        <p class="mt-2 text-emerald-700/70">Belum ada aturan validasi.</p>
                    @else
                        <ul class="mt-2 divide-y divide-emerald-100">
                            @foreach ($question->validations as $v)
                                <li class="py-3">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="text-xs uppercase tracking-wide text-emerald-700/70">
                                                {{ $v->validation_type }}</div>
                                            <div class="mt-1 text-sm text-emerald-900">
                                                @if ($v->min_value !== null)
                                                    <span class="mr-2 rounded bg-emerald-50 px-2 py-0.5">min:
                                                        {{ $v->min_value }}</span>
                                                @endif
                                                @if ($v->max_value !== null)
                                                    <span class="mr-2 rounded bg-emerald-50 px-2 py-0.5">max:
                                                        {{ $v->max_value }}</span>
                                                @endif
                                                @if ($v->pattern)
                                                    <span class="mr-2 rounded bg-emerald-50 px-2 py-0.5">pattern:
                                                        <code>{{ $v->pattern }}</code></span>
                                                @endif
                                                @if ($v->message)
                                                    <span class="mr-2 rounded bg-emerald-50 px-2 py-0.5">msg:
                                                        “{{ $v->message }}”</span>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- Tempat tombol Edit/Hapus validasi jika nanti ditambahkan --}}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
