<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">
                    Detail Pertanyaan
                </h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Form:
                    <span class="font-semibold text-primary-600">
                        {{ $question->section->form->title }}
                    </span>
                </p>
            </div>

            <div class="flex items-center gap-2">

                <a href="{{ route('admin.questions.edit', $question) }}"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-primary-100 px-4 py-2 text-sm font-medium
                           text-primary-700 hover:bg-primary-200 transition">
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </a>

                <a href="{{ route('admin.forms.questions.index', $question->section->form) }}"
                    class="inline-flex items-center gap-2 rounded-lg
                           bg-primary-600 px-4 py-2 text-sm font-semibold
                           text-white shadow-sm hover:bg-primary-700 transition">
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
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">

            {{-- INFO UTAMA --}}
            <div class="bg-white rounded-xl shadow-soft border border-primary-100 p-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>
                        <div class="text-xs uppercase tracking-wide text-secondary-500">
                            Section
                        </div>
                        <div class="mt-1 font-medium text-secondary-900">
                            <span
                                class="mr-1 inline-flex items-center rounded-lg
                                       bg-primary-100 px-2.5 py-1
                                       text-xs text-primary-700">
                                #{{ $question->section->position }}
                            </span>
                            {{ $question->section->title ?? 'Tanpa judul' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-secondary-500">
                            Tipe
                        </div>
                        <span
                            class="mt-1 inline-flex items-center rounded-full
                                   bg-secondary-100 px-2.5 py-1
                                   text-xs font-medium text-secondary-700">
                            {{ strtoupper(str_replace('_', ' ', $question->type)) }}
                        </span>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-secondary-500">
                            Posisi
                        </div>
                        <span
                            class="mt-1 inline-flex items-center rounded-lg
                                   bg-primary-100 px-2.5 py-1
                                   text-sm text-primary-700">
                            {{ $question->position }}
                        </span>
                    </div>

                </div>

                <div class="mt-6">
                    <div class="text-xs uppercase tracking-wide text-secondary-500">
                        Judul
                    </div>
                    <div class="mt-1 font-medium text-secondary-900">
                        {{ $question->title }}
                    </div>
                </div>

                @if ($question->description)
                    <div class="mt-6">
                        <div class="text-xs uppercase tracking-wide text-secondary-500">
                            Deskripsi
                        </div>
                        <div
                            class="mt-2 rounded-xl border border-primary-100
                                   bg-primary-50/40 p-4 text-secondary-900">
                            {!! nl2br(e($question->description)) !!}
                        </div>
                    </div>
                @endif

                {{-- FLAGS --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">

                    @php
                        $flags = [
                            'Wajib' => $question->required,
                            'Acak opsi' => $question->shuffle_options,
                            'Opsi lainnya' => $question->other_option_enabled,
                        ];
                    @endphp

                    @foreach ($flags as $label => $value)
                        <div
                            class="rounded-xl border border-primary-100
                                   bg-primary-50/40 p-3">

                            <div class="text-xs text-secondary-500">
                                {{ $label }}
                            </div>

                            <span
                                class="mt-1 inline-flex items-center gap-1.5
                                       rounded-full px-2.5 py-1 text-xs
                                       {{ $value ? 'bg-primary-100 text-primary-700' : 'bg-secondary-100 text-secondary-600' }}">

                                <span
                                    class="h-1.5 w-1.5 rounded-full
                                           {{ $value ? 'bg-primary-500' : 'bg-secondary-400' }}"></span>

                                {{ $value ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    @endforeach

                </div>

                {{-- SETTINGS --}}
                @if (!empty($question->settings))
                    <div class="mt-6" x-data="{ copied: false }" x-cloak>

                        <div class="flex items-center justify-between">
                            <div class="text-xs uppercase tracking-wide text-secondary-500">
                                Settings
                            </div>

                            <button type="button"
                                class="inline-flex items-center rounded-lg
                                       border border-primary-200 bg-white
                                       px-2.5 py-1 text-xs
                                       text-primary-700 hover:bg-primary-50"
                                @click="
                                    navigator.clipboard
                                        .writeText(@js(json_encode($question->settings, JSON_UNESCAPED_SLASHES)))
                                        .then(()=>{copied=true; setTimeout(()=>copied=false,1500)})
                                ">
                                <span x-show="!copied">Copy JSON</span>
                                <span x-show="copied">Tersalin ✓</span>
                            </button>
                        </div>

                        <pre
                            class="mt-2 max-h-64 overflow-auto
                                   rounded-xl bg-secondary-50 p-3
                                   text-xs">{{ json_encode($question->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                @endif

            </div>

            {{-- PREVIEW --}}
            @php $type = $question->type; @endphp

            <div class="bg-white rounded-xl shadow-soft border border-primary-100">

                <div class="flex items-center justify-between p-6 border-b border-primary-100">
                    <div>
                        <h3 class="font-semibold text-secondary-900">
                            Preview
                        </h3>
                        <p class="text-sm text-secondary-600">
                            Pratinjau tampilan untuk responden
                        </p>
                    </div>
                </div>

                <div class="p-6">

                    <label class="block mb-2 text-sm font-medium text-secondary-900">
                        {{ $question->title }}
                        @if ($question->required)
                            <span class="text-danger-600">*</span>
                        @endif
                    </label>

                    @if (in_array($type, ['short_text']))
                        <input disabled type="text" class="w-full rounded-lg border border-secondary-300 px-3 py-2">
                    @elseif(in_array($type, ['long_text']))
                        <textarea disabled rows="4" class="w-full rounded-lg border border-secondary-300 px-3 py-2"></textarea>
                    @elseif(in_array($type, ['multiple_choice']))
                        <div class="space-y-2">
                            @foreach ($question->options as $opt)
                                <label class="flex items-center gap-2 text-sm">
                                    <input disabled type="radio">
                                    <span>{{ $opt->label }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif(in_array($type, ['checkboxes']))
                        <div class="space-y-2">
                            @foreach ($question->options as $opt)
                                <label class="flex items-center gap-2 text-sm">
                                    <input disabled type="checkbox">
                                    <span>{{ $opt->label }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif(in_array($type, ['dropdown']))
                        <select disabled class="w-full rounded-lg border border-secondary-300 px-3 py-2">
                            <option>Pilih...</option>
                            @foreach ($question->options as $opt)
                                <option>{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    @elseif(in_array($type, ['date']))
                        <input disabled type="date" class="w-full rounded-lg border border-secondary-300 px-3 py-2">
                    @elseif(in_array($type, ['time']))
                        <input disabled type="time" class="w-full rounded-lg border border-secondary-300 px-3 py-2">
                    @elseif(in_array($type, ['file_upload']))
                        <input disabled type="file" class="w-full rounded-lg border border-secondary-300 px-3 py-2">
                    @else
                        <p class="text-sm text-secondary-600">
                            Pratinjau tidak tersedia untuk tipe ini.
                        </p>
                    @endif

                </div>
            </div>

            {{-- OPSI --}}
            <div class="bg-white rounded-xl shadow-soft border border-primary-100">

                <div class="p-6">

                    <h3 class="font-semibold text-secondary-900">
                        Opsi / Grid Items
                    </h3>

                    @if ($question->options->isEmpty())

                        <p class="mt-2 text-secondary-600">
                            Belum ada opsi.
                        </p>
                    @else
                        <div class="hidden sm:block mt-4 overflow-x-auto">

                            <table class="min-w-full divide-y divide-primary-100">

                                <thead class="bg-primary-50">
                                    <tr>
                                        <th class="py-3.5 pl-6 pr-4 text-left text-xs font-semibold">Role</th>
                                        <th class="py-3.5 px-4 text-left text-xs font-semibold">Label</th>
                                        <th class="py-3.5 px-4 text-left text-xs font-semibold">Value</th>
                                        <th class="py-3.5 px-4 text-left text-xs font-semibold">Posisi</th>
                                        <th class="py-3.5 pr-6 pl-4 text-left text-xs font-semibold">Lainnya</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-primary-100">

                                    @foreach ($question->options as $opt)
                                        <tr class="hover:bg-primary-50/50">

                                            <td class="py-3.5 pl-6 pr-4">{{ $opt->role ?? '-' }}</td>
                                            <td class="py-3.5 px-4">{{ $opt->label }}</td>
                                            <td class="py-3.5 px-4">{{ $opt->value ?? '-' }}</td>

                                            <td class="py-3.5 px-4">
                                                <span
                                                    class="inline-flex items-center rounded-lg
                                                           bg-primary-100 px-2.5 py-1
                                                           text-xs text-primary-700">
                                                    {{ $opt->position }}
                                                </span>
                                            </td>

                                            <td class="py-3.5 pr-6 pl-4">
                                                @if ($opt->is_other)
                                                    <span
                                                        class="inline-flex items-center gap-1.5
                                                               rounded-full bg-primary-100
                                                               px-2.5 py-1 text-xs
                                                               text-primary-700">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>
                                                        Ya
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
