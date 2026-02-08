<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-emerald-900 leading-tight">Review Jawaban</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-3">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <div class="font-semibold mb-2">⚠️ Tidak dapat mengirim jawaban:</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- progress singkat --}}
            @php
                $sections = ($form->sections ?? collect())->sortBy('position')->values();
                $total = $sections->count();
                $percent = 100; // di halaman review dianggap sudah 100%
            @endphp
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="h-2 bg-emerald-100 rounded">
                    <div class="h-2 bg-emerald-600 rounded transition-all" style="width: {{ $percent }}%;"></div>
                </div>
                <div class="text-sm text-emerald-700/80 mt-2">Review sebelum kirim</div>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <p class="text-emerald-800/80 mb-6">
                    Silakan periksa ringkasan jawabanmu di bawah ini. Kamu masih bisa
                    <strong>mengubah jawaban</strong> dengan menekan tombol <em>Edit</em> pada section terkait.
                </p>

                @php
                    // Ambil jawaban yang sudah dipass dari controller; kalau belum ada, fallback load sendiri.
                    if (!isset($answers) || !($answers instanceof \Illuminate\Support\Collection)) {
                        $answers = $response
                            ->answers()
                            ->with(['selectedOptions', 'gridCells', 'option', 'fileMedia', 'question.section'])
                            ->get()
                            ->keyBy('question_id');
                    }
                @endphp

                @forelse ($sections as $i => $section)
                    <div class="border rounded-xl p-5 mb-6 border-emerald-100">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm text-emerald-700/70">Section {{ $section->position ?? $i + 1 }} dari
                                    {{ $total }}</div>
                                <div class="font-semibold text-lg text-emerald-900">{{ $section->title ?? 'Section' }}</div>
                                @if (!empty($section->description))
                                    <div class="text-sm text-emerald-700/70 mt-1">{{ $section->description }}</div>
                                @endif
                            </div>
                            <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => $section->position]) }}"
                                class="px-3 py-1.5 bg-white border border-emerald-200 rounded-lg text-sm text-emerald-800 hover:bg-emerald-50">
                                Edit
                            </a>
                        </div>

                        <div class="mt-4 space-y-4">
                            @forelse(($section->questions ?? collect())->sortBy('position') as $q)
                                @php
                                    $ans = $answers[$q->id] ?? null;

                                    // derive nilai tampilan
                                    $value =
                                        $ans?->long_text_value ??
                                        ($ans?->text_value ??
                                            (($ans?->option_label_snapshot ?: optional($ans?->option)->label) ??
                                                ((!is_null($ans?->number_value) ? (string) $ans->number_value : null) ??
                                                    (($ans?->date_value ? $ans->date_value->format('Y-m-d') : null) ??
                                                        (($ans?->time_value ?: null) ??
                                                            ($ans?->datetime_value
                                                                ? $ans->datetime_value->format('Y-m-d H:i:s')
                                                                : null))))));

                                    $selected = $ans?->selectedOptions ?? collect();
                                    $grid = $ans?->gridCells ?? collect();
                                    $file = $ans?->fileMedia;
                                @endphp

                                <div class="border-b pb-4 last:border-0 last:pb-0">
                                    <div class="text-sm text-emerald-700/70">
                                        Q#{{ $q->position ?? '-' }} • {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                        @if ($q->required)
                                            <span class="text-red-600">*</span>
                                        @endif
                                    </div>
                                    <div class="font-medium text-emerald-900">{{ $q->title }}</div>
                                    @if (!empty($q->description))
                                        <div class="text-sm text-emerald-700/70 mb-1">{{ $q->description }}</div>
                                    @endif

                                    {{-- renderer nilai --}}
                                    <div class="mt-1 text-emerald-900">
                                        @if ($grid->count())
                                            <div class="overflow-x-auto">
                                                <table class="min-w-[400px] text-sm">
                                                    <thead class="text-left text-emerald-700">
                                                        <tr>
                                                            <th class="py-1 pr-4">Row</th>
                                                            <th class="py-1">Column</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($grid as $cell)
                                                            <tr class="border-t">
                                                                <td class="py-1 pr-4">{{ $cell->row_label_snapshot }}
                                                                </td>
                                                                <td class="py-1">{{ $cell->col_label_snapshot }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif($selected->count())
                                            <ul class="list-disc list-inside text-sm">
                                                @foreach ($selected as $opt)
                                                    <li>{{ $opt->option_label_snapshot }}</li>
                                                @endforeach
                                            </ul>
                                        @elseif($file)
                                            <div class="text-sm">
                                                <a href="{{ $file->url }}" target="_blank" rel="noopener"
                                                    class="text-emerald-700 underline">
                                                    {{ $file->original_name ?? basename($file->path) }}
                                                </a>
                                                <span class="text-emerald-700/70">
                                                    ({{ $file->mime ?? 'file' }},
                                                    {{ number_format(($file->size_kb ?? 0) / 1024, 2) }} MB)
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-emerald-900">{{ $value ?? '—' }}</div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-emerald-700/70">Tidak ada pertanyaan pada section ini.</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p class="text-emerald-800/80">Form ini belum memiliki section.</p>
                @endforelse

                {{-- Actions --}}
                <form method="POST" action="{{ route('forms.submit', $form) }}" id="submitForm">
                    @csrf
                    <div class="flex justify-between items-center gap-4">
                        @php $last = $sections->last(); @endphp
                        @if ($last)
                            <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => $last->position]) }}"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                ← Kembali ke Section Terakhir
                            </a>
                        @else
                            <a href="{{ route('forms.start', $form->uid) }}"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                ← Kembali
                            </a>
                        @endif

                        <button type="submit"
                            onclick="return confirm('⚠️ Kirim jawaban sekarang?\n\nSetelah dikirim, jawaban tidak dapat diubah lagi.')"
                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                            Kirim Jawaban
                        </button>
                    </div>

                    {{-- Helper text --}}
                    <p class="text-sm text-emerald-700/70 mt-4 text-center">
                        💡 Pastikan semua jawaban sudah benar sebelum mengirim. Kamu bisa edit dengan klik tombol "Edit" di setiap section.
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
