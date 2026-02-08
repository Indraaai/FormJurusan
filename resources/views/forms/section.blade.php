<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-emerald-900">
            {{ $form->title ?? 'Form' }} — Section
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">

            @php
                $sections = ($form->sections ?? collect())->sortBy('position')->values();
                $total = $sections->count();
                $index = max(1, (int) $sections->search(fn($s) => $s->id === $section->id) + 1);
                $percent = $total ? round(($index / $total) * 100) : 0;
                $isLast = $index === $total;
                $prev = $sections->firstWhere('position', ($section->position ?? 0) - 1);
            @endphp

            {{-- Progress --}}
            <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm">
                <div class="h-2 w-full rounded bg-emerald-100" role="progressbar" aria-valuenow="{{ $percent }}"
                    aria-valuemin="0" aria-valuemax="100" aria-label="Progress pengisian">
                    <div class="h-2 rounded bg-emerald-600 transition-all" style="width: {{ $percent }}%;"></div>
                </div>
                <div class="mt-2 flex items-center justify-between text-sm text-emerald-700/80">
                    <span>Section {{ $index }} dari {{ $total }}</span>
                    <span>{{ $percent }}%</span>
                </div>
            </div>

            {{-- Alerts --}}
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-3 text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif
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

            <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="mb-1 text-lg font-semibold text-emerald-900">
                    {{ $section->title ?? "Section $index" }}
                </h3>
                @if (!empty($section->description))
                    <p class="mb-6 text-emerald-800/80">{{ $section->description }}</p>
                @endif

                {{-- ===== FORM (satu) ===== --}}
                <form id="sectionForm" method="POST"
                    action="{{ route('forms.section.save', ['form' => $form->uid, 'pos' => $section->position]) }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        @foreach ($section->questions as $loopIndex => $q)
                            @php
                                $ans = $answers[$q->id] ?? null;

                                $oldVal = old("q.{$q->id}");
                                $selectedSingle = $oldVal !== null ? $oldVal : $ans?->option_id;
                                $selectedMany =
                                    $oldVal !== null
                                        ? (array) $oldVal
                                        : $ans?->selectedOptions?->pluck('option_id')->all() ?? [];

                                $textVal = $oldVal !== null ? $oldVal : $ans?->text_value;
                                $longTextVal = $oldVal !== null ? $oldVal : $ans?->long_text_value;
                                $numberVal = $oldVal !== null ? $oldVal : $ans?->number_value;
                                $dateVal = $oldVal !== null ? $oldVal : $ans?->date_value?->format('Y-m-d');

                                if ($oldVal !== null) {
                                    $timeVal = $oldVal;
                                } else {
                                    if ($ans?->time_value instanceof \Carbon\Carbon) {
                                        $timeVal = $ans->time_value->format('H:i');
                                    } elseif (!empty($ans?->time_value)) {
                                        $timeVal = substr((string) $ans->time_value, 0, 5);
                                    } else {
                                        $timeVal = null;
                                    }
                                }

                                $dtVal = $oldVal !== null ? $oldVal : $ans?->datetime_value?->format('Y-m-d\TH:i');

                                $vByType = ($q->validations ?? collect())->keyBy('validation_type');

                                $vRange = $vByType->get('number_range');
                                $rangeMin = $vRange?->min_value ?? 1;
                                $rangeMax = $vRange?->max_value ?? 5;
                                $step =
                                    is_array($vRange?->extras) && isset($vRange->extras['step'])
                                        ? $vRange->extras['step']
                                        : null;

                                $vText = $vByType->get('text_length');
                                $minLen = $vText?->min_value;
                                $maxLen = $vText?->max_value;

                                $vRegex = $vByType->get('regex');
                                $pattern = $vRegex?->pattern;

                                $vDate = $vByType->get('date_range');
                                $dateMin = $vDate?->min_value
                                    ? \Carbon\Carbon::parse($vDate->min_value)->format('Y-m-d')
                                    : null;
                                $dateMax = $vDate?->max_value
                                    ? \Carbon\Carbon::parse($vDate->max_value)->format('Y-m-d')
                                    : null;

                                $vTime = $vByType->get('time_range');
                                $timeMin = $vTime?->min_value ? substr($vTime->min_value, 0, 5) : null;
                                $timeMax = $vTime?->max_value ? substr($vTime->max_value, 0, 5) : null;

                                $vFile = $vByType->get('file_type');
                                $accept = '';
                                if ($vFile && is_array($vFile->extras)) {
                                    if (!empty($vFile->extras['mimetypes'])) {
                                        $accept = implode(',', (array) $vFile->extras['mimetypes']);
                                    } elseif (!empty($vFile->extras['mimes'])) {
                                        $accept = implode(
                                            ',',
                                            array_map(fn($e) => '.' . ltrim($e, '.'), (array) $vFile->extras['mimes']),
                                        );
                                    }
                                }
                                $vFileSize = $vByType->get('file_size');
                                $maxKB = $vFileSize?->max_value ? (int) $vFileSize->max_value : null;

                                $isRequired = (bool) ($q->required || $vByType->has('required'));

                                $inputId = 'q-' . $q->id;
                                $hintId = 'hint-' . $q->id;
                                $autoFocus = $loopIndex === 0 ? 'autofocus' : '';

                                $hints = [];
                                if ($minLen || $maxLen) {
                                    $hints[] =
                                        'Panjang teks' .
                                        ($minLen ? ' min ' . $minLen : '') .
                                        ($maxLen ? ' max ' . $maxLen : '');
                                }
                                if ($pattern) {
                                    $hints[] = 'Format khusus';
                                }
                                if ($vRange && ($vRange->min_value || $vRange->max_value)) {
                                    $hints[] =
                                        'Rentang angka' .
                                        ($vRange->min_value ? ' min ' . $vRange->min_value : '') .
                                        ($vRange->max_value ? ' max ' . $vRange->max_value : '') .
                                        ($step ? ' step ' . $step : '');
                                }
                                if ($dateMin || $dateMax) {
                                    $hints[] =
                                        'Rentang tanggal' .
                                        ($dateMin ? ' min ' . $dateMin : '') .
                                        ($dateMax ? ' max ' . $dateMax : '');
                                }
                                if ($timeMin || $timeMax) {
                                    $hints[] =
                                        'Rentang waktu' .
                                        ($timeMin ? ' min ' . $timeMin : '') .
                                        ($timeMax ? ' max ' . $timeMax : '');
                                }
                                if ($maxKB) {
                                    $hints[] = 'Ukuran file maks ~' . number_format($maxKB / 1024, 2) . ' MB';
                                }
                            @endphp

                            <div class="border-b pb-6 last:border-0 last:pb-0">
                                <div class="mb-1 text-sm text-emerald-700/70">
                                    {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                    @if ($isRequired)
                                        <span class="text-red-600">*</span>
                                    @endif
                                </div>

                                <label class="mb-1 block font-medium text-emerald-900" for="{{ $inputId }}">
                                    {{ $q->title }}
                                </label>

                                @if (!empty($q->description) || $hints)
                                    <div id="{{ $hintId }}"
                                        class="mb-2 rounded-xl border border-emerald-100 bg-emerald-50/40 p-3 text-sm text-emerald-900">
                                        @if (!empty($q->description))
                                            <div>{{ $q->description }}</div>
                                        @endif
                                        @if ($hints)
                                            <div class="mt-1 text-emerald-700/80">{{ implode(' • ', $hints) }}</div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Inputs --}}
                                @switch($q->type)
                                    @case('short_text')
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="text"
                                            name="q[{{ $q->id }}]" value="{{ $textVal }}"
                                            @if ($isRequired) required @endif
                                            @if ($minLen) minlength="{{ (int) $minLen }}" @endif
                                            @if ($maxLen) maxlength="{{ (int) $maxLen }}" @endif
                                            @if ($pattern) pattern="{{ $pattern }}" @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @case('long_text')
                                        <textarea id="{{ $inputId }}" {{ $autoFocus }} name="q[{{ $q->id }}]" rows="4"
                                            @if ($isRequired) required @endif
                                            @if ($minLen) minlength="{{ (int) $minLen }}" @endif
                                            @if ($maxLen) maxlength="{{ (int) $maxLen }}" @endif aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">{{ $longTextVal }}</textarea>
                                    @break

                                    @case('number')
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="number"
                                            name="q[{{ $q->id }}]" value="{{ $numberVal }}"
                                            @if ($isRequired) required @endif
                                            @if ($vRange?->min_value !== null) min="{{ $vRange->min_value }}" @endif
                                            @if ($vRange?->max_value !== null) max="{{ $vRange->max_value }}" @endif
                                            @if ($step) step="{{ $step }}" @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @case('email')
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="email"
                                            name="q[{{ $q->id }}]" value="{{ $textVal }}"
                                            @if ($isRequired) required @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @case('url')
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="url"
                                            name="q[{{ $q->id }}]" value="{{ $textVal }}"
                                            @if ($isRequired) required @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @case('multiple_choice')
                                        <div class="mt-1 space-y-2" id="{{ $inputId }}"
                                            aria-describedby="{{ $hintId }}">
                                            @foreach ($q->options as $opt)
                                                <label
                                                    class="flex items-center gap-2 rounded-xl border border-transparent p-2 hover:border-emerald-200 hover:bg-emerald-50/40">
                                                    <input type="radio" name="q[{{ $q->id }}]"
                                                        value="{{ $opt->id }}"
                                                        {{ (string) $selectedSingle === (string) $opt->id ? 'checked' : '' }}
                                                        @if ($isRequired && $loop->first && !$selectedSingle) required @endif>
                                                    <span class="text-emerald-900">{{ $opt->label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @break

                                    @case('checkboxes')
                                        <div class="mt-1 space-y-2" id="{{ $inputId }}"
                                            aria-describedby="{{ $hintId }}"
                                            @if ($isRequired) data-checkbox-group-required="true" @endif>
                                            @foreach ($q->options as $opt)
                                                <label
                                                    class="flex items-center gap-2 rounded-xl border border-transparent p-2 hover:border-emerald-200 hover:bg-emerald-50/40">
                                                    <input type="checkbox" name="q[{{ $q->id }}][]"
                                                        value="{{ $opt->id }}"
                                                        {{ in_array((string) $opt->id, array_map('strval', $selectedMany ?? []), true) ? 'checked' : '' }}
                                                        {{-- BUG-008 FIX: Use JS group validation instead of HTML required on first only --}} data-group="checkbox-{{ $q->id }}">
                                                    <span class="text-emerald-900">{{ $opt->label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @break

                                    @case('dropdown')
                                        <select id="{{ $inputId }}" {{ $autoFocus }} name="q[{{ $q->id }}]"
                                            @if ($isRequired) required @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                            <option value="">— pilih —</option>
                                            @foreach ($q->options as $opt)
                                                <option value="{{ $opt->id }}"
                                                    {{ (string) $selectedSingle === (string) $opt->id ? 'selected' : '' }}>
                                                    {{ $opt->label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @break

                                    {{-- BUG-007 FIX: Multiple-choice grid --}}
                                    @case('mc_grid')
                                        @php
                                            $rowOpts = $q->options->where('role', 'row');
                                            $colOpts = $q->options->where('role', 'column');
                                            $gridCells = $ans?->gridCells?->keyBy('row_option_id') ?? collect();
                                        @endphp
                                        <div class="mt-1 overflow-x-auto" aria-describedby="{{ $hintId }}">
                                            <table class="min-w-full border border-emerald-200 text-sm">
                                                <thead>
                                                    <tr class="bg-emerald-50">
                                                        <th class="border border-emerald-200 px-3 py-2 text-left"></th>
                                                        @foreach ($colOpts as $col)
                                                            <th
                                                                class="border border-emerald-200 px-3 py-2 text-center text-emerald-900">
                                                                {{ $col->label }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($rowOpts as $row)
                                                        @php $gridOldVal = old("q.{$q->id}.{$row->id}") ?? $gridCells->get($row->id)?->col_option_id; @endphp
                                                        <tr>
                                                            <td
                                                                class="border border-emerald-200 px-3 py-2 font-medium text-emerald-900">
                                                                {{ $row->label }}</td>
                                                            @foreach ($colOpts as $col)
                                                                <td class="border border-emerald-200 px-3 py-2 text-center">
                                                                    <input type="radio"
                                                                        name="q[{{ $q->id }}][{{ $row->id }}]"
                                                                        value="{{ $col->id }}"
                                                                        {{ (string) $gridOldVal === (string) $col->id ? 'checked' : '' }}
                                                                        @if ($isRequired && $loop->parent->first) required @endif>
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @break

                                    {{-- BUG-007 FIX: Checkbox grid --}}
                                    @case('checkbox_grid')
                                        @php
                                            $rowOpts = $q->options->where('role', 'row');
                                            $colOpts = $q->options->where('role', 'column');
                                            $gridCells = $ans?->gridCells ?? collect();
                                            $gridCellsByRow = $gridCells->groupBy('row_option_id');
                                        @endphp
                                        <div class="mt-1 overflow-x-auto" aria-describedby="{{ $hintId }}">
                                            <table class="min-w-full border border-emerald-200 text-sm">
                                                <thead>
                                                    <tr class="bg-emerald-50">
                                                        <th class="border border-emerald-200 px-3 py-2 text-left"></th>
                                                        @foreach ($colOpts as $col)
                                                            <th
                                                                class="border border-emerald-200 px-3 py-2 text-center text-emerald-900">
                                                                {{ $col->label }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($rowOpts as $row)
                                                        @php
                                                            $rowSelectedCols =
                                                                old("q.{$q->id}.{$row->id}") ??
                                                                ($gridCellsByRow
                                                                    ->get($row->id)
                                                                    ?->pluck('col_option_id')
                                                                    ->all() ??
                                                                    []);
                                                            $rowSelectedCols = array_map(
                                                                'strval',
                                                                (array) $rowSelectedCols,
                                                            );
                                                        @endphp
                                                        <tr>
                                                            <td
                                                                class="border border-emerald-200 px-3 py-2 font-medium text-emerald-900">
                                                                {{ $row->label }}</td>
                                                            @foreach ($colOpts as $col)
                                                                <td class="border border-emerald-200 px-3 py-2 text-center">
                                                                    <input type="checkbox"
                                                                        name="q[{{ $q->id }}][{{ $row->id }}][]"
                                                                        value="{{ $col->id }}"
                                                                        {{ in_array((string) $col->id, $rowSelectedCols, true) ? 'checked' : '' }}>
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @break

                                    @case('linear_scale')
                                        <div class="mt-1 flex items-center gap-3" aria-describedby="{{ $hintId }}">
                                            <input type="range" id="{{ $inputId }}" {{ $autoFocus }}
                                                min="{{ $rangeMin }}" max="{{ $rangeMax }}"
                                                @if ($step) step="{{ $step }}" @endif
                                                name="q[{{ $q->id }}]" value="{{ $numberVal ?? $rangeMin }}"
                                                oninput="document.getElementById('s-{{ $q->id }}').innerText=this.value"
                                                @if ($isRequired) required @endif
                                                class="w-full accent-emerald-600" />
                                            <span id="s-{{ $q->id }}" class="text-sm text-emerald-900">
                                                {{ $numberVal ?? $rangeMin }}
                                            </span>
                                            <span
                                                class="text-xs text-emerald-700/70">({{ $rangeMin }}–{{ $rangeMax }})</span>
                                        </div>
                                    @break

                                    @case('date')
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="date"
                                            name="q[{{ $q->id }}]" value="{{ $dateVal }}"
                                            @if ($isRequired) required @endif
                                            @if ($dateMin) min="{{ $dateMin }}" @endif
                                            @if ($dateMax) max="{{ $dateMax }}" @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @case('time')
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="time"
                                            name="q[{{ $q->id }}]" value="{{ $timeVal }}"
                                            @if ($isRequired) required @endif
                                            @if ($timeMin) min="{{ $timeMin }}" @endif
                                            @if ($timeMax) max="{{ $timeMax }}" @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @case('datetime')
                                    @case('date_time')
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="datetime-local"
                                            name="q[{{ $q->id }}]" value="{{ $dtVal }}"
                                            @if ($isRequired) required @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @case('file_upload')
                                        @php $file = $ans?->fileMedia; @endphp
                                        @if ($file)
                                            <div class="mb-2 text-sm text-emerald-900">
                                                <a href="{{ $file->url }}" target="_blank" rel="noopener"
                                                    class="text-emerald-700 underline">
                                                    {{ $file->original_name ?? basename($file->path) }}
                                                </a>
                                                <span class="text-emerald-700/70">
                                                    ({{ $file->mime ?? 'file' }},
                                                    {{ number_format(($file->size_kb ?? 0) / 1024, 2) }} MB)
                                                </span>
                                            </div>
                                        @endif
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="file"
                                            name="qfile[{{ $q->id }}]" {{ $accept ? "accept=$accept" : '' }}
                                            @if ($isRequired && !$file) required @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                    @break

                                    @default
                                        <input id="{{ $inputId }}" {{ $autoFocus }} type="text"
                                            name="q[{{ $q->id }}]" value="{{ $textVal }}"
                                            @if ($isRequired) required @endif
                                            aria-describedby="{{ $hintId }}"
                                            class="mt-1 w-full rounded-xl border border-emerald-200 p-2 focus-visible:ring-2 focus-visible:ring-emerald-300">
                                @endswitch

                                {{-- Error per field --}}
                                @error("q.$q->id")
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                                @if ($q->type === 'file_upload')
                                    @error("qfile.$q->id")
                                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Sticky actions bar --}}
                    <div
                        class="sticky bottom-0 -mx-6 -mb-6 mt-6 border-t border-emerald-100 bg-white/85 px-6 py-4 backdrop-blur">
                        <div class="flex items-center justify-between">
                            {{-- Prev --}}
                            @if ($prev)
                                <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => $prev->position]) }}"
                                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-sm text-emerald-800 hover:bg-emerald-50">
                                    Sebelumnya
                                </a>
                            @else
                                <span
                                    class="inline-flex items-center rounded-xl border border-emerald-100 bg-white px-4 py-2 text-sm text-emerald-400">
                                    Sebelumnya
                                </span>
                            @endif

                            <div class="flex gap-2">
                                {{-- Simpan & Lanjut (untuk section bukan terakhir) --}}
                                @if (!$isLast)
                                    <button type="submit"
                                        class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                                        Simpan & Lanjut
                                    </button>
                                @else
                                    {{-- Section terakhir: Simpan & Review --}}
                                    <button type="submit" name="go_review" value="1"
                                        class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                                        Simpan & Review
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Info siap submit --}}
                @if (session('ready_to_submit') && $isLast)
                    <div class="mt-4 rounded-xl border border-yellow-200 bg-yellow-50 p-3 text-yellow-800">
                        Jawaban tersimpan. Kamu bisa menekan <strong>Kirim Jawaban</strong> untuk menyelesaikan.
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- BUG-008 FIX: JavaScript validation for required checkbox groups --}}
    <script>
        document.getElementById('sectionForm')?.addEventListener('submit', function(e) {
            const requiredGroups = this.querySelectorAll('[data-checkbox-group-required="true"]');
            let firstError = null;

            requiredGroups.forEach(function(group) {
                const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                const checked = group.querySelectorAll('input[type="checkbox"]:checked');
                const existingError = group.parentElement.querySelector('.checkbox-group-error');

                if (existingError) existingError.remove();

                if (checkboxes.length > 0 && checked.length === 0) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'checkbox-group-error mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'Pilih minimal satu opsi.';
                    group.parentElement.appendChild(errorDiv);
                    if (!firstError) firstError = group;
                }
            });

            if (firstError) {
                e.preventDefault();
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>
</x-app-layout>
