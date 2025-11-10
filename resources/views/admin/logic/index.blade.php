<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">Logic Rules</h2>
                <p class="text-sm text-secondary-600 mt-1">Form: <span
                        class="font-semibold text-primary-600">{{ $form->title }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.questions.index', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 transition-all">
                    <i class="bi bi-question-circle"></i>
                    <span>Pertanyaan</span>
                </a>
                <a href="{{ route('admin.forms.logic.create', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:shadow-soft-md transition-all">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Rule</span>
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
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div
                    class="rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-success-800 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- PANEL DAFTAR RULES --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="rulesList()"
                x-init="init()">
                {{-- toolbar filter/sort --}}
                <div
                    class="flex flex-col gap-3 border-b border-emerald-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-wrap items-center gap-2">
                        <div class="relative">
                            <input type="text" x-model="q" @input="apply()"
                                placeholder="Cari sumber/target/angka/nilai…"
                                class="w-64 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300">
                            <div class="pointer-events-none absolute right-2 top-2.5 text-emerald-700/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-width="2"
                                        d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                </svg>
                            </div>
                        </div>

                        <select x-model="status" @change="apply()"
                            class="w-[9rem] rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300">
                            <option value="">Status: Semua</option>
                            <option value="enabled">Enabled</option>
                            <option value="disabled">Disabled</option>
                        </select>

                        <select x-model="operator" @change="apply()"
                            class="w-[11rem] rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300">
                            <option value="">Operator: Semua</option>
                            @foreach (['=', '!=', '>', '<', '>=', '<=', 'contains', 'in', 'answered', 'not_answered'] as $op)
                                <option value="{{ $op }}">{{ $op }}</option>
                            @endforeach
                        </select>

                        <select x-model="sort" @change="apply()"
                            class="w-[12rem] rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300">
                            <option value="priority_asc">Sort: Prioritas ↑</option>
                            <option value="priority_desc">Sort: Prioritas ↓</option>
                            <option value="status">Sort: Status</option>
                        </select>

                        <button type="button" @click="resetAll()"
                            class="rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50">
                            Reset
                        </button>
                    </div>

                    <div class="text-sm text-emerald-700/70"><span x-text="visibleCount"></span> / <span
                            x-text="totalCount"></span> rule</div>
                </div>

                {{-- EMPTY STATE --}}
                @if ($rules->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/40 p-8">
                            <div class="mx-auto h-10 w-10 rounded-full bg-emerald-200"></div>
                        </div>
                        <p class="text-emerald-900">Belum ada rule. Klik <strong>Tambah Rule</strong> untuk membuat.</p>
                    </div>
                @else
                    {{-- DESKTOP TABLE --}}
                    <div class="hidden sm:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-emerald-50/60 text-emerald-900">
                                    <tr>
                                        <th class="py-3.5 pl-6 pr-4 text-left font-medium">Prioritas</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Sumber</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Operator</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Nilai</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Aksi</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Status</th>
                                        <th class="py-3.5 pr-6 pl-4 text-left font-medium">Tools</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-emerald-100" x-ref="tbody">
                                    @foreach ($rules as $rule)
                                        @php
                                            $src = $rule->sourceQuestion()->first();
                                            $target = $rule->targetSection()->first();
                                            $val =
                                                $rule->value_text ??
                                                ($rule->value_number !== null ? (string) $rule->value_number : null);
                                            if (!$val && $rule->option_id) {
                                                $val = 'option_id:' . $rule->option_id;
                                            }
                                            $status = $rule->is_enabled ? 'enabled' : 'disabled';
                                            $operator = $rule->operator ?? '';
                                            $searchBlob = trim(
                                                ($src ? $src->title : '[Pertanyaan hilang]') .
                                                    ' ' .
                                                    ($target ? $target->title ?? '' : '') .
                                                    ' ' .
                                                    ($val ?? ''),
                                            );
                                        @endphp
                                        <tr class="rule-row hover:bg-emerald-50/40 transition"
                                            data-status="{{ $status }}" data-operator="{{ $operator }}"
                                            data-priority="{{ $rule->priority }}"
                                            data-q="{{ Str::lower($searchBlob) }}">
                                            <td class="py-3.5 pl-6 pr-4 text-emerald-900">
                                                <span
                                                    class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">
                                                    {{ $rule->priority }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                @if ($src)
                                                    <div class="text-xs text-emerald-700/70">Q#{{ $src->position }}
                                                        (Sec#{{ $src->section->position }})</div>
                                                    <div class="font-medium text-emerald-900">
                                                        {{ Str::limit($src->title, 70) }}</div>
                                                @else
                                                    <span class="text-red-600">[Pertanyaan hilang]</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs text-emerald-800">
                                                    {{ $rule->operator }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-emerald-900">
                                                @if ($val)
                                                    @if (Str::startsWith($val, 'option_id:'))
                                                        <span
                                                            class="rounded bg-emerald-50 px-2 py-0.5 text-xs text-emerald-800">Opsi
                                                            #{{ Str::after($val, 'option_id:') }}</span>
                                                    @else
                                                        {{ $val }}
                                                    @endif
                                                @else
                                                    <span class="text-emerald-700/60">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4">
                                                @if ($rule->action === 'submit')
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-gray-900 px-2.5 py-1 text-xs text-white">submit</span>
                                                @else
                                                    <div class="text-xs text-emerald-700/70">goto_section</div>
                                                    <div class="font-medium text-emerald-900">
                                                        @if ($target)
                                                            Sec#{{ $target->position }} —
                                                            {{ $target->title ?? 'Tanpa judul' }}
                                                        @else
                                                            <span class="text-emerald-700/60">—</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs
                                                {{ $rule->is_enabled ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                                    <span
                                                        class="h-1.5 w-1.5 rounded-full {{ $rule->is_enabled ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                    {{ $rule->is_enabled ? 'enabled' : 'disabled' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 pr-6 pl-4">
                                                {{-- menu titik-tiga --}}
                                                <div class="relative" x-data="{ open: false, style: '' }"
                                                    x-init="const reflow = () => { if (open) { place() } };
                                                    window.addEventListener('resize', reflow);
                                                    window.addEventListener('scroll', reflow, { passive: true });
                                                    place = () => {
                                                        const b = $refs.btn.getBoundingClientRect();
                                                        const m = $refs.menu;
                                                        const prevD = m.style.display,
                                                            prevV = m.style.visibility;
                                                        m.style.visibility = 'hidden';
                                                        m.style.display = 'block';
                                                        const r = m.getBoundingClientRect();
                                                        let top = b.bottom + 8 + window.scrollY,
                                                            left = b.right - r.width + window.scrollX;
                                                        const below = window.innerHeight - b.bottom,
                                                            above = b.top;
                                                        if (below < r.height + 12 && above > below) { top = b.top - r.height - 8 + window.scrollY }
                                                        const maxL = window.scrollX + window.innerWidth - r.width - 8,
                                                            maxT = window.scrollY + window.innerHeight - r.height - 8;
                                                        left = Math.max(8 + window.scrollX, Math.min(left, maxL));
                                                        top = Math.max(8 + window.scrollY, Math.min(top, maxT));
                                                        style = `top:${top}px;left:${left}px`;
                                                        m.style.display = prevD;
                                                        m.style.visibility = prevV;
                                                    };">
                                                    <button type="button" x-ref="btn"
                                                        @click="open?open=false:(open=true,$nextTick(()=>place()))"
                                                        class="inline-flex items-center rounded-xl border border-emerald-200 bg-white p-2 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 focus-visible:ring-2 focus-visible:ring-emerald-500"
                                                        aria-haspopup="true" :aria-expanded="open">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path
                                                                d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                    </button>
                                                    <template x-if="open">
                                                        <div class="fixed inset-0 z-[60]" @click="open=false"
                                                            aria-hidden="true"></div>
                                                    </template>
                                                    <div x-cloak x-show="open" x-transition.origin.top.right
                                                        x-ref="menu"
                                                        class="fixed z-[70] w-44 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl ring-1 ring-emerald-100"
                                                        :style="style" role="menu" tabindex="-1">
                                                        <div class="p-1.5">
                                                            <a href="{{ route('admin.logic.edit', $rule) }}"
                                                                class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                role="menuitem">
                                                                Edit
                                                            </a>
                                                            <div class="my-1 border-t border-emerald-100"></div>
                                                            <form method="POST"
                                                                action="{{ route('admin.logic.destroy', $rule) }}"
                                                                onsubmit="return confirm('Hapus rule ini?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="block w-full rounded-xl px-3 py-2 text-left text-sm text-red-700 hover:bg-red-50"
                                                                    role="menuitem">
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- MOBILE CARDS --}}
                    <div class="sm:hidden">
                        <ul class="divide-y divide-emerald-100" x-ref="cards">
                            @foreach ($rules as $rule)
                                @php
                                    $src = $rule->sourceQuestion()->first();
                                    $target = $rule->targetSection()->first();
                                    $val =
                                        $rule->value_text ??
                                        ($rule->value_number !== null ? (string) $rule->value_number : null);
                                    if (!$val && $rule->option_id) {
                                        $val = 'option_id:' . $rule->option_id;
                                    }
                                    $status = $rule->is_enabled ? 'enabled' : 'disabled';
                                    $operator = $rule->operator ?? '';
                                    $searchBlob = trim(
                                        ($src ? $src->title : '[Pertanyaan hilang]') .
                                            ' ' .
                                            ($target ? $target->title ?? '' : '') .
                                            ' ' .
                                            ($val ?? ''),
                                    );
                                @endphp
                                <li class="rule-card py-4" data-status="{{ $status }}"
                                    data-operator="{{ $operator }}" data-priority="{{ $rule->priority }}"
                                    data-q="{{ Str::lower($searchBlob) }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="mb-1 flex flex-wrap items-center gap-2">
                                                <span
                                                    class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">P{{ $rule->priority }}</span>
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] text-emerald-800">{{ $rule->operator }}</span>
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px]
                                                {{ $rule->is_enabled ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                                    <span
                                                        class="h-1.5 w-1.5 rounded-full {{ $rule->is_enabled ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                    {{ $rule->is_enabled ? 'enabled' : 'disabled' }}
                                                </span>
                                            </div>
                                            @if ($src)
                                                <div class="text-xs text-emerald-700/70">Q#{{ $src->position }}
                                                    (Sec#{{ $src->section->position }})</div>
                                                <div class="font-medium text-emerald-900">
                                                    {{ Str::limit($src->title, 90) }}</div>
                                            @else
                                                <div class="text-red-600">[Pertanyaan hilang]</div>
                                            @endif
                                            <div class="mt-2 text-sm text-emerald-900/90">
                                                <span class="text-emerald-700/70">Nilai:</span>
                                                @if ($val)
                                                    @if (Str::startsWith($val, 'option_id:'))
                                                        <span
                                                            class="rounded bg-emerald-50 px-1.5 py-0.5 text-xs text-emerald-800">Opsi
                                                            #{{ Str::after($val, 'option_id:') }}</span>
                                                    @else
                                                        {{ $val }}
                                                    @endif
                                                @else
                                                    <span class="text-emerald-700/60">—</span>
                                                @endif
                                            </div>
                                            <div class="mt-1 text-sm text-emerald-900/90">
                                                <span class="text-emerald-700/70">Aksi:</span>
                                                @if ($rule->action === 'submit')
                                                    <span
                                                        class="rounded bg-gray-900 px-1.5 py-0.5 text-xs text-white">submit</span>
                                                @else
                                                    @if ($target)
                                                        goto Sec#{{ $target->position }} —
                                                        {{ $target->title ?? 'Tanpa judul' }}
                                                    @else
                                                        <span class="text-emerald-700/60">—</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- tools --}}
                                        <div class="flex-shrink-0">
                                            <div class="relative" x-data="{ o: false, s: '' }">
                                                <button type="button" x-ref="b"
                                                    @click="o?o=false:(o=true,$nextTick(()=>{const R=$refs.r.getBoundingClientRect(),B=$refs.b.getBoundingClientRect();let t=B.bottom+8+window.scrollY,l=B.right-R.width+window.scrollX;const bl=window.innerHeight-B.bottom,ab=B.top;if(bl<R.height+12&&ab>bl){t=B.top-R.height-8+window.scrollY}const ml=window.scrollX+window.innerWidth-R.width-8,mt=window.scrollY+window.innerHeight-R.height-8;l=Math.max(8+window.scrollX,Math.min(l,ml));t=Math.max(8+window.scrollY,Math.min(t,mt));s=`top:${t}px;left:${l}px`}))"
                                                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white p-2 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </button>
                                                <template x-if="o">
                                                    <div class="fixed inset-0 z-[60]" @click="o=false"></div>
                                                </template>
                                                <div x-cloak x-show="o" x-transition.origin.top.right
                                                    x-ref="r"
                                                    class="fixed z-[70] w-40 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl ring-1 ring-emerald-100"
                                                    :style="s">
                                                    <div class="p-1.5">
                                                        <a href="{{ route('admin.logic.edit', $rule) }}"
                                                            class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50">Edit</a>
                                                        <div class="my-1 border-t border-emerald-100"></div>
                                                        <form method="POST"
                                                            action="{{ route('admin.logic.destroy', $rule) }}"
                                                            onsubmit="return confirm('Hapus rule ini?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="block w-full rounded-xl px-3 py-2 text-left text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Bantuan Operator (collapsible) --}}
            <div x-data="{ open: false }" class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <button type="button" @click="open=!open"
                    class="flex w-full items-center justify-between px-6 py-4">
                    <h3 class="font-semibold text-emerald-900">Catatan Operator</h3>
                    <svg x-bind:class="{ 'rotate-180': open }" class="h-5 w-5 text-emerald-700/70 transition"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 0 1 1.08 1.04l-4.25 4.25a.75.75 0 0 1-1.06 0L5.21 8.27a.75.75 0 0 1 .02-1.06z" />
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition class="px-6 pb-6">
                    <ul class="list-inside list-disc space-y-1 text-sm text-emerald-800">
                        <li><code>=</code>, <code>!=</code>, <code>&gt;</code>, <code>&lt;</code>, <code>&gt;=</code>,
                            <code>&lt;=</code> — gunakan <em>value_text</em> atau <em>value_number</em> sesuai tipe
                            pertanyaan.
                        </li>
                        <li><code>contains</code>, <code>in</code> — cocok untuk teks/checkbox; <code>in</code> bisa
                            pakai koma: <em>a,b,c</em>.</li>
                        <li><code>answered</code>, <code>not_answered</code> — tidak perlu isi nilai.</li>
                        <li>Untuk pilihan spesifik, isi <strong>option_id</strong> (ID opsi).</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- Alpine helper for filters/sorting --}}
    <script>
        function rulesList() {
            return {
                q: '',
                status: '',
                operator: '',
                sort: 'priority_asc',
                totalCount: 0,
                visibleCount: 0,
                init() {
                    this.totalCount = this.$refs.tbody ? this.$refs.tbody.querySelectorAll('.rule-row').length : (this.$refs
                        .cards?.querySelectorAll('.rule-card').length || 0);
                    this.apply();
                },
                matches(el) {
                    const q = this.q.trim().toLowerCase();
                    const ds = el.dataset || {};
                    const okStatus = !this.status || (ds.status === this.status);
                    const okOperator = !this.operator || (ds.operator === this.operator);
                    const okQuery = !q || (ds.q || '').includes(q);
                    return okStatus && okOperator && okQuery;
                },
                apply() {
                    const rows = this.$refs.tbody ? Array.from(this.$refs.tbody.querySelectorAll('.rule-row')) : [];
                    const cards = this.$refs.cards ? Array.from(this.$refs.cards.querySelectorAll('.rule-card')) : [];
                    // filter
                    let vis = 0;
                    for (const r of rows) {
                        const show = this.matches(r);
                        r.classList.toggle('hidden', !show);
                        if (show) vis++;
                    }
                    for (const c of cards) {
                        const show = this.matches(c);
                        c.classList.toggle('hidden', !show);
                        if (show && rows.length === 0) vis++;
                    }
                    // sort (only visible items are re-appended in order)
                    const cmp = (a, b) => {
                        if (this.sort === 'priority_asc') return (+a.dataset.priority) - (+b.dataset.priority);
                        if (this.sort === 'priority_desc') return (+b.dataset.priority) - (+a.dataset.priority);
                        if (this.sort === 'status') {
                            // enabled first, then disabled, then priority
                            const rank = s => s === 'enabled' ? 0 : 1;
                            if (rank(a.dataset.status) !== rank(b.dataset.status)) return rank(a.dataset.status) - rank(
                                b.dataset.status);
                            return (+a.dataset.priority) - (+b.dataset.priority);
                        }
                        return 0;
                    };
                    if (rows.length) {
                        const visRows = rows.filter(r => !r.classList.contains('hidden')).sort(cmp);
                        visRows.forEach(r => this.$refs.tbody.appendChild(r));
                    }
                    if (cards.length) {
                        const visCards = cards.filter(r => !r.classList.contains('hidden')).sort(cmp);
                        visCards.forEach(r => this.$refs.cards.appendChild(r));
                    }
                    this.visibleCount = vis;
                },
                resetAll() {
                    this.q = '';
                    this.status = '';
                    this.operator = '';
                    this.sort = 'priority_asc';
                    this.apply();
                }
            }
        }
    </script>
</x-app-layout>
