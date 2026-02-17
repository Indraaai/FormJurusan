<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-secondary-900 leading-tight">
                    Response Detail
                </h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Form: <span class="font-semibold text-primary-600">{{ $response->form->title }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.responses.index', $response->form) }}"
                    class="inline-flex items-center rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus:ring-2 focus:ring-primary-200 transition">
                    Kembali
                </a>

                <a href="{{ route('admin.forms.responses.export', $response->form) }}"
                    class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 transition">
                    Export CSV (All)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- META CARD --}}
            <div class="bg-white border border-primary-100 shadow-soft rounded-2xl p-6">

                @php
                    $email = $response->respondent_email ?? (optional($response->respondent)->email ?? '-');
                    $name = optional($response->respondent)->name ?? '-';
                    $dur = $response->duration_seconds ? gmdate('H:i:s', $response->duration_seconds) : '-';
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>
                        <div class="text-xs font-semibold text-secondary-500">Response UID</div>
                        <div class="mt-1 font-medium text-secondary-900">
                            <code class="text-xs bg-secondary-100 px-2 py-1 rounded-md">
                                {{ $response->uid }}
                            </code>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-secondary-500">Status</div>
                        <div class="mt-1">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                {{ $response->status === 'submitted'
                                    ? 'bg-success-100 text-success-700'
                                    : 'bg-warning-100 text-warning-700' }}">
                                {{ $response->status }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-secondary-500">Submitted At</div>
                        <div class="mt-1 font-medium text-secondary-900">
                            {{ optional($response->submitted_at)->format('Y-m-d H:i:s') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-secondary-500">Started At</div>
                        <div class="mt-1 font-medium text-secondary-900">
                            {{ optional($response->started_at)->format('Y-m-d H:i:s') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-secondary-500">Duration</div>
                        <div class="mt-1 font-medium text-secondary-900">{{ $dur }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-secondary-500">Respondent</div>
                        <div class="mt-1 font-medium text-secondary-900">{{ $name }}</div>
                        <div class="text-sm text-secondary-600">{{ $email }}</div>
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-secondary-200">

                    <div>
                        <div class="text-xs font-semibold text-secondary-500">IP</div>
                        <div class="mt-1 font-medium text-secondary-900">
                            {{ $response->source_ip ?? '-' }}
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="text-xs font-semibold text-secondary-500">User Agent</div>
                        <div class="mt-1 font-medium text-secondary-900 break-all">
                            {{ $response->user_agent ?? '-' }}
                        </div>
                    </div>

                </div>
            </div>

            {{-- JAWABAN --}}
            <div class="bg-white border border-primary-100 shadow-soft rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-secondary-900 mb-4">
                    Jawaban
                </h3>

                @php
                    $answers = $response->answers->sortBy(function ($a) {
                        $secPos = optional(optional($a->question)->section)->position ?? 9999;
                        $qPos = optional($a->question)->position ?? 9999;
                        return sprintf('%05d-%05d', $secPos, $qPos);
                    });
                    $currentSection = null;
                @endphp

                @forelse ($answers as $ans)

                    @php
                        $question = $ans->question;
                        $section = optional($question)->section;
                        $secTitle = $section?->title ?? 'Section';
                        $secPos = $section?->position;

                        $value =
                            $ans->long_text_value ??
                            ($ans->text_value ??
                                (($ans->option_label_snapshot ?: optional($ans->option)->label) ??
                                    ((!is_null($ans->number_value) ? (string) $ans->number_value : null) ??
                                        (($ans->date_value ? $ans->date_value->format('Y-m-d') : null) ??
                                            (($ans->time_value ?: null) ??
                                                ($ans->datetime_value
                                                    ? $ans->datetime_value->format('Y-m-d H:i:s')
                                                    : null))))));

                        $selectedOptions = $ans->relationLoaded('selectedOptions')
                            ? $ans->selectedOptions
                            : $ans->selectedOptions()->get();

                        $gridCells = $ans->relationLoaded('gridCells')
                            ? $ans->gridCells
                            : $ans->gridCells()->get();

                        $file = $ans->relationLoaded('fileMedia')
                            ? $ans->fileMedia
                            : $ans->fileMedia()->first();
                    @endphp

                    {{-- SECTION HEADER --}}
                    @if ($secPos !== $currentSection || $currentSection === null)
                        @php $currentSection = $secPos; @endphp
                        <div class="mt-8 mb-3 pt-6 border-t border-secondary-200">
                            <div class="text-xs font-semibold text-secondary-500">
                                Section #{{ $secPos ?? '-' }}
                            </div>
                            <div class="text-base font-semibold text-secondary-900">
                                {{ $secTitle }}
                            </div>
                        </div>
                    @endif

                    <div class="py-4 border-b border-secondary-200 last:border-0">

                        <div class="text-xs text-secondary-500">
                            Q#{{ $question->position ?? '-' }} •
                            {{ strtoupper(str_replace('_', ' ', $question->type ?? '')) }}
                            @if ($question?->required)
                                <span class="text-danger-600">*</span>
                            @endif
                        </div>

                        <div class="mt-1 font-medium text-secondary-900">
                            {{ $ans->question_text_snapshot ?: $question->title ?? '[Pertanyaan]' }}
                        </div>

                        <div class="mt-3">

                            @if ($gridCells->count())
                                <div class="overflow-x-auto border border-secondary-200 rounded-xl">
                                    <table class="min-w-[400px] text-sm w-full">
                                        <thead class="bg-secondary-50 text-secondary-700">
                                            <tr>
                                                <th class="py-2 px-4 text-left font-semibold">Row</th>
                                                <th class="py-2 px-4 text-left font-semibold">Column</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-secondary-200">
                                            @foreach ($gridCells as $cell)
                                                <tr>
                                                    <td class="py-2 px-4">{{ $cell->row_label_snapshot }}</td>
                                                    <td class="py-2 px-4">{{ $cell->col_label_snapshot }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            @elseif ($selectedOptions->count())
                                <ul class="list-disc list-inside text-sm text-secondary-800 space-y-1">
                                    @foreach ($selectedOptions as $opt)
                                        <li>{{ $opt->option_label_snapshot }}</li>
                                    @endforeach
                                </ul>

                            @elseif ($file)
                                <div class="text-sm">
                                    <div class="text-secondary-500 mb-2">File</div>
                                    <a href="{{ $file->url }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 transition">
                                        Download ({{ $file->mime ?? 'file' }},
                                        {{ number_format(($file->size_kb ?? 0) / 1024, 2) }} MB)
                                    </a>
                                    @if ($file->original_name)
                                        <div class="text-xs text-secondary-500 mt-1">
                                            {{ $file->original_name }}
                                        </div>
                                    @endif
                                </div>

                            @else
                                <div class="text-secondary-900">
                                    {{ $value ?? '—' }}
                                </div>
                            @endif

                        </div>
                    </div>

                @empty
                    <p class="text-secondary-600">Tidak ada jawaban.</p>
                @endforelse

            </div>

        </div>
    </div>
</x-app-layout>
