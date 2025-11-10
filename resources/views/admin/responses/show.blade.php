<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Response Detail — {{ $response->form->title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.responses.index', $response->form) }}"
                    class="px-3 py-2 text-sm bg-gray-100 rounded-lg">Kembali</a>
                <a href="{{ route('admin.forms.responses.export', $response->form) }}"
                    class="px-3 py-2 text-sm bg-gray-900 text-white rounded-lg">Export CSV (All)</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Meta --}}
            <div class="bg-white shadow-sm rounded-xl p-6">
                @php
                    $email = $response->respondent_email ?? (optional($response->respondent)->email ?? '-');
                    $name = optional($response->respondent)->name ?? '-';
                    $dur = $response->duration_seconds ? gmdate('H:i:s', $response->duration_seconds) : '-';
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-sm text-gray-500">Response UID</div>
                        <div class="font-medium"><code class="text-xs">{{ $response->uid }}</code></div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Status</div>
                        <div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 text-xs rounded
                                {{ $response->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $response->status }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Submitted At</div>
                        <div class="font-medium">
                            {{ optional($response->submitted_at)->format('Y-m-d H:i:s') ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Started At</div>
                        <div class="font-medium">
                            {{ optional($response->started_at)->format('Y-m-d H:i:s') ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Duration</div>
                        <div class="font-medium">{{ $dur }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Respondent</div>
                        <div class="font-medium">{{ $name }}</div>
                        <div class="text-sm text-gray-600">{{ $email }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <div class="text-sm text-gray-500">IP</div>
                        <div class="font-medium">{{ $response->source_ip ?? '-' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-sm text-gray-500">User Agent</div>
                        <div class="font-medium break-all">{{ $response->user_agent ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Jawaban --}}
            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="font-semibold mb-4">Jawaban</h3>

                @php
                    // Urut: section.position -> question.position
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

                        // Derive value (time_value string; date/datetime pakai Carbon)
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

                        $gridCells = $ans->relationLoaded('gridCells') ? $ans->gridCells : $ans->gridCells()->get();

                        $file = $ans->relationLoaded('fileMedia') ? $ans->fileMedia : $ans->fileMedia()->first();
                    @endphp

                    {{-- Section header saat berganti --}}
                    @if ($secPos !== $currentSection || $currentSection === null)
                        @php $currentSection = $secPos; @endphp
                        <div class="mt-6 mb-2 pt-4 border-t">
                            <div class="text-sm text-gray-500">Section #{{ $secPos ?? '-' }}</div>
                            <div class="font-semibold">{{ $secTitle }}</div>
                        </div>
                    @endif

                    <div class="py-3 border-b last:border-0">
                        <div class="text-sm text-gray-500">
                            Q#{{ $question->position ?? '-' }} •
                            {{ strtoupper(str_replace('_', ' ', $question->type ?? '')) }}
                            @if ($question?->required)
                                <span class="text-red-600">*</span>
                            @endif
                        </div>
                        <div class="font-medium">
                            {{ $ans->question_text_snapshot ?: $question->title ?? '[Pertanyaan]' }}
                        </div>

                        {{-- Renderer nilai --}}
                        <div class="mt-2">
                            @if ($gridCells->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-[400px] text-sm">
                                        <thead class="text-left text-gray-600">
                                            <tr>
                                                <th class="py-1 pr-4">Row</th>
                                                <th class="py-1">Column</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($gridCells as $cell)
                                                <tr class="border-t">
                                                    <td class="py-1 pr-4">{{ $cell->row_label_snapshot }}</td>
                                                    <td class="py-1">{{ $cell->col_label_snapshot }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif ($selectedOptions->count())
                                <ul class="list-disc list-inside text-sm text-gray-800">
                                    @foreach ($selectedOptions as $opt)
                                        <li>{{ $opt->option_label_snapshot }}</li>
                                    @endforeach
                                </ul>
                            @elseif ($file)
                                <div class="text-sm">
                                    <div class="text-gray-500 mb-1">File</div>
                                    <a href="{{ $file->url }}" target="_blank" rel="noopener"
                                        class="px-3 py-1.5 bg-gray-100 rounded-lg inline-block">
                                        Download ({{ $file->mime ?? 'file' }},
                                        {{ number_format(($file->size_kb ?? 0) / 1024, 2) }} MB)
                                    </a>
                                    @if ($file->original_name)
                                        <div class="text-xs text-gray-500 mt-1">{{ $file->original_name }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-800">{{ $value ?? '—' }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600">Tidak ada jawaban.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
