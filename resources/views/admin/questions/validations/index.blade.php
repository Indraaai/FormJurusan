<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Validations — Q#{{ $question->position }} ({{ $question->title }})
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.questions.validations.create', $question) }}"
                    class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Tambah
                </a>
                <a href="{{ route('admin.forms.questions.index', $form) }}"
                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
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
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3 text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm" x-data="vList()"
                x-init="init()">

                {{-- Toolbar: search, filter, sort --}}
                <div
                    class="flex flex-col gap-3 border-b border-emerald-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-wrap items-center gap-2">
                        <div class="relative">
                            <input type="text" x-model="q" @input="apply()"
                                placeholder="Cari tipe/pesan/parameter…"
                                class="w-64 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 placeholder:text-emerald-900/40 focus-visible:ring-2 focus-visible:ring-emerald-300">
                            <div class="pointer-events-none absolute right-2 top-2.5 text-emerald-700/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-width="2"
                                        d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                </svg>
                            </div>
                        </div>

                        <select x-model="type" @change="apply()"
                            class="w-44 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300">
                            <option value="">Tipe: Semua</option>
                            @foreach (['text_length', 'number_range', 'regex', 'date_range', 'time_range', 'file_type', 'file_size'] as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>

                        <select x-model="sort" @change="apply()"
                            class="w-44 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-emerald-900 focus-visible:ring-2 focus-visible:ring-emerald-300">
                            <option value="type_asc">Sort: Tipe A→Z</option>
                            <option value="type_desc">Sort: Tipe Z→A</option>
                        </select>

                        <button type="button" @click="resetAll()"
                            class="rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50">
                            Reset
                        </button>
                    </div>

                    <div class="text-sm text-emerald-700/70">
                        <span x-text="visibleCount"></span> / <span x-text="totalCount"></span> validation
                    </div>
                </div>

                {{-- BODY --}}
                @if ($validations->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/40 p-8">
                            <div class="mx-auto h-10 w-10 rounded-full bg-emerald-200"></div>
                        </div>
                        <p class="text-emerald-900">Belum ada validation.</p>
                    </div>
                @else
                    {{-- DESKTOP TABLE --}}
                    <div class="hidden sm:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-emerald-50/60 text-emerald-900">
                                    <tr>
                                        <th class="py-3.5 pl-6 pr-4 text-left font-medium">Type</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Params</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Message</th>
                                        <th class="py-3.5 pr-6 pl-4 text-left font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-emerald-100" x-ref="tbody">
                                    @foreach ($validations as $v)
                                        @php
                                            // Render params ringkas
                                            switch ($v->validation_type) {
                                                case 'text_length':
                                                case 'number_range':
                                                    $paramStr =
                                                        'min=' .
                                                        ($v->min_value ?? '—') .
                                                        ', max=' .
                                                        ($v->max_value ?? '—');
                                                    break;
                                                case 'regex':
                                                    $paramStr = $v->pattern ? "/{$v->pattern}/" : '—';
                                                    break;
                                                case 'file_size':
                                                    $paramStr = 'max=' . ($v->max_value ?? '—') . ' KB';
                                                    break;
                                                case 'date_range':
                                                case 'time_range':
                                                case 'file_type':
                                                    $paramStr = is_array($v->extras)
                                                        ? json_encode($v->extras)
                                                        : ($v->extras ?:
                                                        '—');
                                                    break;
                                                default:
                                                    $paramStr = '—';
                                            }
                                            $blob = strtolower(
                                                ($v->validation_type ?? '') .
                                                    ' ' .
                                                    ($paramStr ?? '') .
                                                    ' ' .
                                                    ($v->message ?? ''),
                                            );
                                        @endphp
                                        <tr class="v-row hover:bg-emerald-50/40 transition"
                                            data-type="{{ $v->validation_type }}" data-q="{{ $blob }}">
                                            <td class="py-3.5 pl-6 pr-4">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs text-emerald-800">
                                                    {{ $v->validation_type }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-emerald-900">
                                                {{ $paramStr }}
                                            </td>
                                            <td class="py-3.5 px-4 text-emerald-900">
                                                {{ $v->message ?? '—' }}
                                            </td>
                                            <td class="py-3.5 pr-6 pl-4">
                                                {{-- Menu titik-tiga --}}
                                                <div class="relative" x-data="{ open: false, style: '' }" x-init="const reflow = () => { if (open) { place() } };
                                                window.addEventListener('resize', reflow);
                                                window.addEventListener('scroll', reflow, { passive: true });
                                                place = () => {
                                                    const b = $refs.btn.getBoundingClientRect();
                                                    const m = $refs.menu;
                                                    const pd = m.style.display,
                                                        pv = m.style.visibility;
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
                                                    m.style.display = pd;
                                                    m.style.visibility = pv;
                                                }">
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
                                                        <div class="fixed inset-0 z-[60]" @click="open=false"></div>
                                                    </template>
                                                    <div x-cloak x-show="open" x-transition.origin.top.right
                                                        x-ref="menu"
                                                        class="fixed z-[70] w-44 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl ring-1 ring-emerald-100"
                                                        :style="style" role="menu" tabindex="-1">
                                                        <div class="p-1.5">
                                                            <a href="{{ route('admin.questions.validations.edit', $v) }}"
                                                                class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                role="menuitem">
                                                                Edit
                                                            </a>
                                                            <div class="my-1 border-t border-emerald-100"></div>
                                                            <form method="POST"
                                                                action="{{ route('admin.questions.validations.destroy', $v) }}"
                                                                onsubmit="return confirm('Hapus validation ini?')">
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
                                                {{-- /menu --}}
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
                            @foreach ($validations as $v)
                                @php
                                    switch ($v->validation_type) {
                                        case 'text_length':
                                        case 'number_range':
                                            $paramStr =
                                                'min=' . ($v->min_value ?? '—') . ', max=' . ($v->max_value ?? '—');
                                            break;
                                        case 'regex':
                                            $paramStr = $v->pattern ? "/{$v->pattern}/" : '—';
                                            break;
                                        case 'file_size':
                                            $paramStr = 'max=' . ($v->max_value ?? '—') . ' KB';
                                            break;
                                        case 'date_range':
                                        case 'time_range':
                                        case 'file_type':
                                            $paramStr = is_array($v->extras)
                                                ? json_encode($v->extras)
                                                : ($v->extras ?:
                                                '—');
                                            break;
                                        default:
                                            $paramStr = '—';
                                    }
                                    $blob = strtolower(
                                        ($v->validation_type ?? '') .
                                            ' ' .
                                            ($paramStr ?? '') .
                                            ' ' .
                                            ($v->message ?? ''),
                                    );
                                @endphp
                                <li class="v-card py-4" data-type="{{ $v->validation_type }}"
                                    data-q="{{ $blob }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="mb-1">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] text-emerald-800">
                                                    {{ $v->validation_type }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-emerald-900/90">
                                                <span class="text-emerald-700/70">Params:</span> {{ $paramStr }}
                                            </div>
                                            <div class="mt-1 text-sm text-emerald-900/90">
                                                <span class="text-emerald-700/70">Message:</span>
                                                {{ $v->message ?? '—' }}
                                            </div>
                                        </div>

                                        {{-- Tools --}}
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
                                                        <a href="{{ route('admin.questions.validations.edit', $v) }}"
                                                            class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50">Edit</a>
                                                        <div class="my-1 border-t border-emerald-100"></div>
                                                        <form method="POST"
                                                            action="{{ route('admin.questions.validations.destroy', $v) }}"
                                                            onsubmit="return confirm('Hapus validation ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="block w-full rounded-xl px-3 py-2 text-left text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- /Tools --}}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Alpine helpers --}}
    <script>
        function vList() {
            return {
                q: '',
                type: '',
                sort: 'type_asc',
                totalCount: 0,
                visibleCount: 0,
                init() {
                    const rows = this.$refs.tbody ? this.$refs.tbody.querySelectorAll('.v-row') : [];
                    const cards = this.$refs.cards ? this.$refs.cards.querySelectorAll('.v-card') : [];
                    this.totalCount = rows.length ? rows.length : cards.length;
                    this.apply();
                },
                matches(el) {
                    const q = (this.q || '').trim().toLowerCase();
                    const okType = !this.type || (el.dataset.type === this.type);
                    const okQ = !q || (el.dataset.q || '').includes(q);
                    return okType && okQ;
                },
                apply() {
                    const rows = this.$refs.tbody ? Array.from(this.$refs.tbody.querySelectorAll('.v-row')) : [];
                    const cards = this.$refs.cards ? Array.from(this.$refs.cards.querySelectorAll('.v-card')) : [];
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

                    const cmp = (a, b) => {
                        const A = a.dataset.type || '',
                            B = b.dataset.type || '';
                        if (this.sort === 'type_asc') return A.localeCompare(B);
                        if (this.sort === 'type_desc') return B.localeCompare(A);
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
                    this.type = '';
                    this.sort = 'type_asc';
                    this.apply();
                }
            }
        }
    </script>
</x-app-layout>
