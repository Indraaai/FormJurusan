<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Sections — {{ $form->title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.edit', $form) }}"
                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Kembali ke Form
                </a>
                <a href="{{ route('admin.forms.sections.create', $form) }}"
                    class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Tambah Section
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

            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3 text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <div class="p-6">
                    @if ($sections->isEmpty())
                        <div class="flex flex-col items-center gap-3 py-10 text-center">
                            <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/40 p-8">
                                <div class="mx-auto h-10 w-10 rounded-full bg-emerald-200"></div>
                            </div>
                            <p class="text-emerald-900">
                                Belum ada section. Klik <span class="font-medium">Tambah Section</span> untuk membuat
                                yang pertama.
                            </p>
                            <a href="{{ route('admin.forms.sections.create', $form) }}"
                                class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm hover:bg-emerald-700 transition">
                                Tambah Section
                            </a>
                        </div>
                    @else
                        {{-- DESKTOP TABLE --}}
                        <div class="hidden sm:block">
                            <div class="overflow-x-auto rounded-2xl">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-emerald-50/60 text-emerald-900">
                                        <tr>
                                            <th class="py-3.5 pl-6 pr-4 text-left font-medium">#</th>
                                            <th class="py-3.5 px-4 text-left font-medium">Judul</th>
                                            <th class="py-3.5 px-4 text-left font-medium">Deskripsi</th>
                                            <th class="py-3.5 px-4 text-left font-medium">Posisi</th>
                                            <th class="py-3.5 pr-6 pl-4 text-right font-medium">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-100 align-top">
                                        @foreach ($sections as $sec)
                                            <tr class="hover:bg-emerald-50/40 transition">
                                                <td class="py-3.5 pl-6 pr-4 text-emerald-900">{{ $loop->iteration }}
                                                </td>
                                                <td class="py-3.5 px-4 font-medium">
                                                    <a href="{{ route('admin.sections.show', $sec) }}"
                                                        class="text-emerald-700 hover:underline">
                                                        {{ $sec->title ?? 'Tanpa judul' }}
                                                    </a>
                                                </td>
                                                <td class="py-3.5 px-4 text-emerald-900/80">
                                                    {{ \Illuminate\Support\Str::limit($sec->description, 120) }}
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span
                                                        class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">
                                                        {{ $sec->position }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 pr-6 pl-4">
                                                    {{-- POP-UP AKSI (AUTO-FLIP) --}}
                                                    <div class="flex justify-end" x-data="{
                                                        open: false,
                                                        top: 0,
                                                        left: 0,
                                                        openMenu() {
                                                            this.open = true;
                                                            this.$nextTick(() => {
                                                                const btn = this.$refs.btn.getBoundingClientRect();
                                                                const menu = this.$refs.menu.getBoundingClientRect();
                                                                const spaceBelow = window.innerHeight - btn.bottom;
                                                                const spaceAbove = btn.top;
                                                                const dropUp = spaceBelow < menu.height && spaceAbove > spaceBelow;
                                                                const top = dropUp ?
                                                                    (btn.top + window.scrollY - menu.height - 8) :
                                                                    (btn.bottom + window.scrollY + 8);
                                                                const left = (btn.right + window.scrollX) - menu.width;
                                                                this.top = Math.max(8, top);
                                                                this.left = Math.max(8, left);
                                                            });
                                                        }
                                                    }"
                                                        @keydown.escape.window="open=false" x-init="const closeOnReflow = () => { if (open) { open = false } };
                                                        window.addEventListener('resize', closeOnReflow);
                                                        window.addEventListener('scroll', closeOnReflow, { passive: true });">
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
                                                                <a href="{{ route('admin.sections.edit', $sec) }}"
                                                                    class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                    role="menuitem">
                                                                    Edit
                                                                </a>
                                                                <a href="{{ route('admin.sections.show', $sec) }}"
                                                                    class="block rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                    role="menuitem">
                                                                    Lihat Section
                                                                </a>
                                                                <div class="my-1 border-t border-emerald-100"></div>
                                                                <form method="POST"
                                                                    action="{{ route('admin.sections.destroy', $sec) }}"
                                                                    onsubmit="return confirm('Hapus section ini? Pertanyaan di dalamnya juga akan terhapus.');">
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
                        <div class="sm:hidden">
                            <ul class="divide-y divide-emerald-100">
                                @foreach ($sections as $sec)
                                    <li class="p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.sections.show', $sec) }}"
                                                    class="block font-medium text-emerald-900">
                                                    {{ $sec->title ?? 'Tanpa judul' }}
                                                </a>
                                                <p class="mt-1 line-clamp-2 text-sm text-emerald-900/80">
                                                    {{ \Illuminate\Support\Str::limit($sec->description, 160) }}
                                                </p>
                                                <div class="mt-2 text-xs text-emerald-700/70">
                                                    Posisi: <span
                                                        class="rounded bg-emerald-50 px-1.5 py-0.5 text-emerald-800">{{ $sec->position }}</span>
                                                </div>
                                            </div>

                                            {{-- POP-UP AKSI (MOBILE) --}}
                                            <div class="relative" x-data="{
                                                open: false,
                                                top: 0,
                                                left: 0,
                                                openMenu() {
                                                    this.open = true;
                                                    this.$nextTick(() => {
                                                        const btn = this.$refs.btn.getBoundingClientRect();
                                                        const menu = this.$refs.menu.getBoundingClientRect();
                                                        const spaceBelow = window.innerHeight - btn.bottom;
                                                        const spaceAbove = btn.top;
                                                        const dropUp = spaceBelow < menu.height && spaceAbove > spaceBelow;
                                                        const top = dropUp ?
                                                            (btn.top + window.scrollY - menu.height - 8) :
                                                            (btn.bottom + window.scrollY + 8);
                                                        const left = (btn.right + window.scrollX) - menu.width;
                                                        this.top = Math.max(8, top);
                                                        this.left = Math.max(8, left);
                                                    });
                                                }
                                            }"
                                                @keydown.escape.window="open=false" x-init="const closeOnReflow = () => { if (open) { open = false } };
                                                window.addEventListener('resize', closeOnReflow);
                                                window.addEventListener('scroll', closeOnReflow, { passive: true });">
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
                                                        <a href="{{ route('admin.sections.edit', $sec) }}"
                                                            class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                            role="menuitem">Edit</a>
                                                        <a href="{{ route('admin.sections.show', $sec) }}"
                                                            class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                            role="menuitem">Lihat Section</a>
                                                        <div class="my-1 border-t border-emerald-100"></div>
                                                        <form method="POST"
                                                            action="{{ route('admin.sections.destroy', $sec) }}"
                                                            onsubmit="return confirm('Hapus section ini? Pertanyaan di dalamnya juga akan terhapus.');">
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
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
