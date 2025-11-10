<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Pertanyaan — {{ $form->title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.sections.index', $form) }}"
                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Kelola Sections
                </a>
                <a href="{{ route('admin.forms.questions.create', $form) }}"
                    class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Tambah Pertanyaan
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

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-6">
                @forelse($form->sections as $section)
                    <div class="mb-8 last:mb-0">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-semibold text-emerald-900">
                                <span
                                    class="mr-2 inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">
                                    #{{ $section->position }}
                                </span>
                                {{ $section->title ?? 'Tanpa judul' }}
                            </h3>
                            <a class="text-sm text-emerald-700 hover:underline"
                                href="{{ route('admin.sections.show', $section) }}">Lihat Section</a>
                        </div>

                        @if ($section->questions->isEmpty())
                            <p class="mt-2 text-sm text-emerald-700/70">Belum ada pertanyaan.</p>
                        @else
                            {{-- DESKTOP TABLE --}}
                            <div class="mt-4 hidden sm:block">
                                <div class="overflow-x-auto rounded-2xl">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-emerald-50/60 text-emerald-900">
                                            <tr>
                                                <th class="py-3.5 pl-6 pr-4 text-left font-medium">Pos</th>
                                                <th class="py-3.5 px-4 text-left font-medium">Tipe</th>
                                                <th class="py-3.5 px-4 text-left font-medium">Judul</th>
                                                <th class="py-3.5 pr-6 pl-4 text-right font-medium">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-emerald-100">
                                            @foreach ($section->questions as $q)
                                                <tr class="hover:bg-emerald-50/40 transition">
                                                    <td class="py-3.5 pl-6 pr-4">
                                                        <span
                                                            class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-1 text-xs text-emerald-800">
                                                            {{ $q->position }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-4">
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">
                                                            {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3.5 px-4">
                                                        <a href="{{ route('admin.questions.show', $q) }}"
                                                            class="text-emerald-700 hover:underline">
                                                            {{ \Illuminate\Support\Str::limit($q->title, 80) }}
                                                        </a>
                                                    </td>
                                                    <td class="py-3.5 pr-6 pl-4">
                                                        {{-- POP-UP AKSI (AUTO-FLIP, FIXED) --}}
                                                        <div class="flex justify-end" x-data="{
                                                            open: false,
                                                            top: 0,
                                                            left: 0,
                                                            openMenu() {
                                                                this.open = true;
                                                                this.$nextTick(() => {
                                                                    const btn = this.$refs.btn.getBoundingClientRect();
                                                                    const menuEl = this.$refs.menu;
                                                                    const menu = menuEl.getBoundingClientRect();
                                                                    const below = window.innerHeight - btn.bottom;
                                                                    const above = btn.top;
                                                                    const dropUp = below < menu.height && above > below;
                                                                    const top = dropUp ?
                                                                        (btn.top + window.scrollY - menu.height - 8) :
                                                                        (btn.bottom + window.scrollY + 8);
                                                                    const left = (btn.right + window.scrollX) - menu.width;
                                                                    this.top = Math.max(8, top);
                                                                    this.left = Math.max(8, left);
                                                                });
                                                            }
                                                        }"
                                                            @keydown.escape.window="open=false" x-init="const closeAll = () => { if (open) { open = false } };
                                                            window.addEventListener('resize', closeAll);
                                                            window.addEventListener('scroll', closeAll, { passive: true });">
                                                            <button type="button" x-ref="btn"
                                                                @click="open ? open=false : openMenu()"
                                                                class="inline-flex items-center rounded-xl border border-emerald-200 bg-white p-2 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 focus-visible:ring-2 focus-visible:ring-emerald-500 transition"
                                                                aria-haspopup="true" :aria-expanded="open">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path
                                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                </svg>
                                                            </button>

                                                            <template x-if="open">
                                                                <div class="fixed inset-0 z-40" @click="open=false"
                                                                    aria-hidden="true"></div>
                                                            </template>

                                                            <div x-cloak x-show="open" x-transition x-ref="menu"
                                                                class="fixed z-50 w-56 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl ring-1 ring-emerald-100"
                                                                :style="`top:${top}px;left:${left}px`" role="menu"
                                                                tabindex="-1">
                                                                <div class="max-h-72 overflow-auto p-1.5">
                                                                    <a href="{{ route('admin.questions.validations.index', $q) }}"
                                                                        class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                        role="menuitem">
                                                                        Tambah Validasi
                                                                    </a>
                                                                    <a href="{{ route('admin.questions.show', $q) }}"
                                                                        class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                        role="menuitem">
                                                                        Lihat
                                                                    </a>
                                                                    <a href="{{ route('admin.questions.edit', $q) }}"
                                                                        class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                        role="menuitem">
                                                                        Edit
                                                                    </a>
                                                                    <div class="my-1 border-t border-emerald-100"></div>
                                                                    <form method="POST"
                                                                        action="{{ route('admin.questions.destroy', $q) }}"
                                                                        onsubmit="return confirm('Hapus pertanyaan ini?');">
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
                                                        {{-- /POP-UP --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- MOBILE CARDS --}}
                            <div class="mt-4 sm:hidden">
                                <ul class="divide-y divide-emerald-100">
                                    @foreach ($section->questions as $q)
                                        <li class="py-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div
                                                        class="flex flex-wrap items-center gap-2 text-xs text-emerald-700/70">
                                                        <span>#{{ $q->position }}</span>
                                                        <span>•</span>
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-800">
                                                            {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                                        </span>
                                                    </div>
                                                    <a href="{{ route('admin.questions.show', $q) }}"
                                                        class="mt-1 block font-medium text-emerald-900">
                                                        {{ \Illuminate\Support\Str::limit($q->title, 80) }}
                                                    </a>
                                                </div>

                                                {{-- POP-UP MOBILE --}}
                                                <div class="relative" x-data="{
                                                    open: false,
                                                    top: 0,
                                                    left: 0,
                                                    openMenu() {
                                                        this.open = true;
                                                        this.$nextTick(() => {
                                                            const btn = this.$refs.btn.getBoundingClientRect();
                                                            const menu = this.$refs.menu.getBoundingClientRect();
                                                            const below = window.innerHeight - btn.bottom;
                                                            const above = btn.top;
                                                            const dropUp = below < menu.height && above > below;
                                                            const top = dropUp ?
                                                                (btn.top + window.scrollY - menu.height - 8) :
                                                                (btn.bottom + window.scrollY + 8);
                                                            const left = (btn.right + window.scrollX) - menu.width;
                                                            this.top = Math.max(8, top);
                                                            this.left = Math.max(8, left);
                                                        });
                                                    }
                                                }"
                                                    @keydown.escape.window="open=false" x-init="const closeAll = () => { if (open) { open = false } };
                                                    window.addEventListener('resize', closeAll);
                                                    window.addEventListener('scroll', closeAll, { passive: true });">
                                                    <button type="button" x-ref="btn"
                                                        @click="open ? open=false : openMenu()"
                                                        class="rounded-xl border border-emerald-200 bg-white p-2 text-emerald-700 hover:bg-emerald-50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path
                                                                d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                    </button>

                                                    <template x-if="open">
                                                        <div class="fixed inset-0 z-40" @click="open=false"
                                                            aria-hidden="true"></div>
                                                    </template>

                                                    <div x-cloak x-show="open" x-transition x-ref="menu"
                                                        class="fixed z-50 w-56 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl ring-1 ring-emerald-100"
                                                        :style="`top:${top}px;left:${left}px`" role="menu"
                                                        tabindex="-1">
                                                        <div class="max-h-72 overflow-auto p-1.5">
                                                            <a href="{{ route('admin.questions.show', $q) }}"
                                                                class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                                role="menuitem">Lihat</a>
                                                            <a href="{{ route('admin.questions.edit', $q) }}"
                                                                class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                                role="menuitem">Edit</a>
                                                            <div class="my-1 border-t border-emerald-100"></div>
                                                            <form method="POST"
                                                                action="{{ route('admin.questions.destroy', $q) }}"
                                                                onsubmit="return confirm('Hapus pertanyaan ini?');">
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
                                                {{-- /POP-UP MOBILE --}}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center gap-3 py-10 text-center">
                        <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/40 p-8">
                            <div class="mx-auto h-10 w-10 rounded-full bg-emerald-200"></div>
                        </div>
                        <p class="text-emerald-900">Belum ada section. Buat section terlebih dulu.</p>
                        <a href="{{ route('admin.forms.sections.create', $form) }}"
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm hover:bg-emerald-700 transition">
                            Tambah Section
                        </a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
