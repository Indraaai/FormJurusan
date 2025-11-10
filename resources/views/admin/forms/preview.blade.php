<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl leading-tight text-gray-900">
                    Preview Form
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $form->title }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium
                    {{ $form->is_published ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200' }}">
                    <span
                        class="w-2 h-2 rounded-full {{ $form->is_published ? 'bg-emerald-500' : 'bg-yellow-500' }}"></span>
                    {{ $form->is_published ? 'Published' : 'Draft' }}
                </span>

                <a href="{{ route('admin.forms.edit', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Kembali ke Edit
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }

        /* Custom scrollbar for navigation */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @php
        $card = 'rounded-lg border border-gray-200 bg-white shadow-sm';
        $btn =
            'inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-emerald-300 transition-all duration-200';
        $chip =
            'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200';
        $input =
            'w-full rounded-lg border-gray-300 shadow-sm text-gray-900 placeholder:text-gray-400 bg-white disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200';
        $qlabel = 'block text-sm font-semibold text-gray-900';
        $qdesc = 'mt-1.5 text-sm text-gray-600';
    @endphp

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Preview Mode Warning --}}
            <div class="rounded-lg border-l-4 border-amber-500 bg-amber-50 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-amber-900">Mode Preview (Admin)</h3>
                        <p class="mt-1 text-sm text-amber-800">Semua input dinonaktifkan. Ini adalah tampilan read-only
                            untuk admin.</p>
                    </div>
                </div>
            </div>

            {{-- Form Description --}}
            @if ($form->description)
                <div class="{{ $card }}">
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-900">Deskripsi Form</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none text-gray-700 whitespace-pre-line leading-relaxed">
                            {{ $form->description }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Section Navigation --}}
            @if ($form->sections->isNotEmpty())
                <div class="{{ $card }}">
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-900">Navigasi Section</h3>
                            <span class="ml-auto text-xs text-gray-500">{{ $form->sections->count() }} Section</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="no-scrollbar flex gap-2 overflow-x-auto pb-2">
                            @foreach ($form->sections as $s)
                                <a href="#sec-{{ $s->id }}"
                                    class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:border-emerald-400 hover:text-emerald-700 transition-all duration-200">
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-xs font-semibold text-gray-700">
                                        {{ $s->position }}
                                    </span>
                                    <span class="whitespace-nowrap">{{ $s->title ?: 'Tanpa judul' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Sections & Questions --}}
            @forelse($form->sections as $sec)
                <section id="sec-{{ $sec->id }}" class="{{ $card }} scroll-mt-24">
                    {{-- Section Header --}}
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 text-white text-sm font-bold">
                                        {{ $sec->position }}
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        {{ $sec->title ?? 'Section ' . $sec->position }}
                                    </h3>
                                </div>
                                @if ($sec->description)
                                    <p class="mt-2 ml-11 text-sm text-gray-600 leading-relaxed">{{ $sec->description }}
                                    </p>
                                @endif
                            </div>
                            <span class="{{ $chip }}">
                                {{ $sec->questions->count() }} Pertanyaan
                            </span>
                        </div>
                    </div>

                    {{-- Questions --}}
                    <div class="p-6 space-y-8 bg-gray-50/30">
                        @forelse($sec->questions as $qIndex => $q)
                            <div
                                class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                                {{-- Question Header --}}
                                <div class="mb-4">
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
                                            {{ $qIndex + 1 }}
                                        </span>
                                        <div class="flex-1">
                                            <label class="{{ $qlabel }}">
                                                {{ $q->title }}
                                                @if ($q->required)
                                                    <span class="text-red-500 ml-1">*</span>
                                                @endif
                                            </label>
                                            @if ($q->description)
                                                <div class="{{ $qdesc }}">{{ $q->description }}</div>
                                            @endif
                                        </div>
                                        <span
                                            class="flex-shrink-0 px-2.5 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-600">
                                            {{ str_replace('_', ' ', ucfirst($q->type)) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Question Input --}}
                                <div class="mt-4">

                                    {{-- Question Input --}}
                                    <div class="mt-4">
                                        @switch($q->type)
                                            @case('short_text')
                                                <input type="text" class="{{ $input }}"
                                                    placeholder="Jawaban singkat..." disabled>
                                            @break

                                            @case('long_text')
                                                <textarea rows="4" class="{{ $input }} resize-y" placeholder="Masukkan jawaban lengkap..." disabled></textarea>
                                            @break

                                            @case('multiple_choice')
                                                <div class="space-y-3">
                                                    @foreach ($q->options as $opt)
                                                        @if ($opt->role === 'option' || $opt->role === null)
                                                            <label
                                                                class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-not-allowed transition-colors">
                                                                <input type="radio" disabled
                                                                    class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                                                <span class="text-sm text-gray-700">{{ $opt->label }}</span>
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                    @if ($q->other_option_enabled || $q->options->where('is_other', true)->count())
                                                        <div class="pl-7">
                                                            <label class="block">
                                                                <span
                                                                    class="text-xs font-medium text-gray-600 mb-1 block">Lainnya:</span>
                                                                <input type="text" class="{{ $input }}"
                                                                    placeholder="Isi jawaban lain…" disabled>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            @break

                                            @case('checkboxes')
                                                <div class="space-y-3">
                                                    @foreach ($q->options as $opt)
                                                        @if ($opt->role === 'option' || $opt->role === null)
                                                            <label
                                                                class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-not-allowed transition-colors">
                                                                <input type="checkbox" disabled
                                                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                                <span class="text-sm text-gray-700">{{ $opt->label }}</span>
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                    @if ($q->other_option_enabled || $q->options->where('is_other', true)->count())
                                                        <div class="pl-7">
                                                            <label class="block">
                                                                <span
                                                                    class="text-xs font-medium text-gray-600 mb-1 block">Lainnya:</span>
                                                                <input type="text" class="{{ $input }}"
                                                                    placeholder="Isi jawaban lain…" disabled>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            @break

                                            @case('dropdown')
                                                <select class="{{ $input }}" disabled>
                                                    <option>— Pilih salah satu —</option>
                                                    @foreach ($q->options as $opt)
                                                        @if ($opt->role === 'option' || $opt->role === null)
                                                            <option>{{ $opt->label }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @break

                                            @case('linear_scale')
                                                @php
                                                    $min = (int) ($q->settings['min'] ?? 1);
                                                    $max = (int) ($q->settings['max'] ?? 5);
                                                    $labelL = $q->settings['label_left'] ?? $min;
                                                    $labelR = $q->settings['label_right'] ?? $max;
                                                @endphp
                                                <div>
                                                    <div
                                                        class="mb-3 flex items-center justify-between text-xs font-medium text-gray-500">
                                                        <span>{{ $labelL }}</span>
                                                        <span>{{ $labelR }}</span>
                                                    </div>
                                                    <div class="flex flex-wrap gap-3">
                                                        @for ($i = $min; $i <= $max; $i++)
                                                            <label
                                                                class="flex items-center gap-2 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-not-allowed transition-colors">
                                                                <input type="radio" disabled
                                                                    class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                                                <span
                                                                    class="text-sm font-medium text-gray-700">{{ $i }}</span>
                                                            </label>
                                                        @endfor
                                                    </div>
                                                </div>
                                            @break

                                            @case('mc_grid')
                                                @php
                                                    $rows = $q->options->where('role', 'row');
                                                    $cols = $q->options->where('role', 'column');
                                                @endphp
                                                @if ($rows->count() && $cols->count())
                                                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-100">
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                    </th>
                                                                    @foreach ($cols as $c)
                                                                        <th
                                                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                            {{ $c->label }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach ($rows as $r)
                                                                    <tr class="hover:bg-gray-50">
                                                                        <th
                                                                            class="px-4 py-3 text-left text-sm font-medium text-gray-900">
                                                                            {{ $r->label }}</th>
                                                                        @foreach ($cols as $c)
                                                                            <td class="px-4 py-3 text-center">
                                                                                <input type="radio" disabled
                                                                                    class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                                        <p class="text-sm text-gray-600">Tambahkan rows/columns pada pertanyaan
                                                            ini untuk melihat grid.</p>
                                                    </div>
                                                @endif
                                            @break

                                            @case('checkbox_grid')
                                                @php
                                                    $rows = $q->options->where('role', 'row');
                                                    $cols = $q->options->where('role', 'column');
                                                @endphp
                                                @if ($rows->count() && $cols->count())
                                                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-100">
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                    </th>
                                                                    @foreach ($cols as $c)
                                                                        <th
                                                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                            {{ $c->label }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach ($rows as $r)
                                                                    <tr class="hover:bg-gray-50">
                                                                        <th
                                                                            class="px-4 py-3 text-left text-sm font-medium text-gray-900">
                                                                            {{ $r->label }}</th>
                                                                        @foreach ($cols as $c)
                                                                            <td class="px-4 py-3 text-center">
                                                                                <input type="checkbox" disabled
                                                                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                                        <p class="text-sm text-gray-600">Tambahkan rows/columns pada pertanyaan
                                                            ini untuk melihat grid.</p>
                                                    </div>
                                                @endif
                                            @break

                                            @case('date')
                                                <input type="date" class="{{ $input }}" disabled>
                                            @break

                                            @case('time')
                                                <input type="time" class="{{ $input }}" disabled>
                                            @break

                                            @case('file_upload')
                                                <div
                                                    class="relative rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center hover:border-gray-400 transition-colors">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 48 48">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" />
                                                    </svg>
                                                    <div class="mt-4">
                                                        <p class="text-sm font-medium text-gray-700">Upload file (disabled in
                                                            preview)</p>
                                                        <p class="mt-1 text-xs text-gray-500">Drag & drop atau klik untuk
                                                            memilih file</p>
                                                    </div>
                                                </div>
                                            @break

                                            @default
                                                <input type="text" class="{{ $input }}" placeholder="Input..."
                                                    disabled>
                                        @endswitch
                                    </div>
                                </div>
                                @empty
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">Belum ada pertanyaan di section ini.</p>
                                    </div>
                            @endforelse
                        </div>
                    </section>
                    @empty
                        <div class="{{ $card }}">
                            <div class="p-12 text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-4 text-lg font-semibold text-gray-900">Belum Ada Section</h3>
                                <p class="mt-2 text-sm text-gray-600">Tambahkan section dan pertanyaan untuk mulai membuat
                                    form.</p>
                            </div>
                        </div>
                    @endforelse

                </div>
            </div>
        </x-app-layout>
