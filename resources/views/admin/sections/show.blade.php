<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-secondary-900 leading-tight">Detail Section</h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Form: <span class="font-semibold text-primary-600">{{ $section->form->title }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sections.edit', $section) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-secondary-200 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                    <i class="bi bi-pencil"></i>
                    Edit
                </a>
                <a href="{{ route('admin.forms.sections.index', $section->form) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-soft hover:bg-primary-700 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                    <i class="bi bi-arrow-left"></i>
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
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div
                    class="rounded-xl border border-success-200 bg-success-50 p-4 text-success-800 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- KARTU DETAIL SECTION --}}
            <div class="rounded-2xl border border-primary-100 bg-white shadow-soft">
                <div class="border-b border-primary-100 p-6">
                    <h3 class="text-lg font-semibold text-secondary-900">Informasi Section</h3>
                    <p class="mt-1 text-sm text-secondary-500">Detail lengkap section dalam form ini</p>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-secondary-500 mb-2">Judul
                                Section</div>
                            <div class="text-base font-medium text-secondary-900">
                                {{ $section->title ?? '—' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-secondary-500 mb-2">Posisi
                            </div>
                            <div>
                                <span
                                    class="inline-flex items-center gap-1 rounded-xl bg-primary-100 px-3 py-1.5 text-sm font-semibold text-primary-700">
                                    <i class="bi bi-sort-numeric-up"></i>
                                    {{ $section->position }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-secondary-500 mb-2">Jumlah
                                Pertanyaan</div>
                            <div class="text-base font-semibold text-secondary-900">
                                {{ $section->questions->count() }} pertanyaan
                            </div>
                        </div>
                    </div>

                    @if ($section->description)
                        <div class="mt-8">
                            <div class="text-xs font-semibold uppercase tracking-wider text-secondary-500 mb-2">
                                Deskripsi</div>
                            <div
                                class="rounded-xl border border-secondary-200 bg-secondary-50/60 p-4 text-base text-secondary-900">
                                {!! nl2br(e($section->description)) !!}
                            </div>
                        </div>
                    @else
                        <div class="mt-8">
                            <div class="text-xs font-semibold uppercase tracking-wider text-secondary-500 mb-2">
                                Deskripsi</div>
                            <div class="text-sm text-secondary-400 italic">Tidak ada deskripsi</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- DAFTAR PERTANYAAN --}}
            <div class="rounded-2xl border border-primary-100 bg-white shadow-soft">
                <div class="border-b border-primary-100 p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-secondary-900">Pertanyaan</h3>
                            <p class="text-sm text-secondary-500">Daftar pertanyaan dalam section ini</p>
                        </div>
                        <a href="{{ route('admin.forms.questions.create', $section->form) }}?section_id={{ $section->id }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-soft hover:bg-primary-700 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                            <i class="bi bi-plus-lg"></i>
                            Tambah Pertanyaan
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if ($section->questions->isEmpty())
                        <div class="flex flex-col items-center gap-4 py-12 text-center">
                            <div class="rounded-2xl bg-secondary-50 p-6">
                                <i class="bi bi-inbox text-5xl text-secondary-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-secondary-900">Belum ada pertanyaan</p>
                                <p class="mt-1 text-sm text-secondary-500">Mulai tambahkan pertanyaan untuk section ini
                                </p>
                            </div>
                            <a href="{{ route('admin.forms.questions.create', $section->form) }}?section_id={{ $section->id }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-medium text-white shadow-soft hover:bg-primary-700 transition">
                                <i class="bi bi-plus-lg"></i>
                                Tambah Pertanyaan Pertama
                            </a>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach ($section->questions as $q)
                                <li
                                    class="rounded-xl border border-secondary-100 bg-white p-4 hover:bg-secondary-50/50 transition-colors">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3 min-w-0 flex-1">
                                            <span
                                                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-primary-100 text-sm font-semibold text-primary-700">
                                                {{ $q->position }}
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-secondary-100 px-2.5 py-0.5 text-xs font-semibold text-secondary-700">
                                                        <i class="bi bi-tag"></i>
                                                        {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                                    </span>
                                                </div>
                                                <div class="font-medium text-secondary-900">
                                                    {{ $q->title }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- MENU TITIK TIGA (POPOVER FIXED + AUTO-FLIP) --}}
                                        <div class="relative" x-data="{
                                            open: false,
                                            style: '',
                                            toggle() { this.open ? this.close() : this.openMenu() },
                                            close() { this.open = false },
                                            openMenu() {
                                                this.open = true;
                                                this.$nextTick(() => this.place())
                                            },
                                            place() {
                                                const btn = this.$refs.btn.getBoundingClientRect();
                                                const menuEl = this.$refs.menu;
                                        
                                                const prevDisplay = menuEl.style.display;
                                                const prevVis = menuEl.style.visibility;
                                                menuEl.style.visibility = 'hidden';
                                                menuEl.style.display = 'block';
                                                const m = menuEl.getBoundingClientRect();
                                        
                                                let top = btn.bottom + 8 + window.scrollY;
                                                let left = btn.right - m.width + window.scrollX;
                                        
                                                const spaceBelow = window.innerHeight - btn.bottom;
                                                const spaceAbove = btn.top;
                                                if (spaceBelow < m.height + 12 && spaceAbove > spaceBelow) {
                                                    top = btn.top - m.height - 8 + window.scrollY;
                                                }
                                        
                                                const maxLeft = window.scrollX + window.innerWidth - m.width - 8;
                                                const maxTop = window.scrollY + window.innerHeight - m.height - 8;
                                                left = Math.max(8 + window.scrollX, Math.min(left, maxLeft));
                                                top = Math.max(8 + window.scrollY, Math.min(top, maxTop));
                                        
                                                this.style = `top:${top}px;left:${left}px`;
                                        
                                                menuEl.style.display = prevDisplay;
                                                menuEl.style.visibility = prevVis;
                                            }
                                        }" x-init="const reflow = () => { if (open) place() };
                                        window.addEventListener('resize', reflow);
                                        window.addEventListener('scroll', reflow, { passive: true });"
                                            @keydown.escape.window="close()" @click.outside="close()">
                                            <button type="button" x-ref="btn" @click.stop="toggle()"
                                                class="inline-flex items-center rounded-xl border border-primary-200 bg-white p-2 text-secondary-600 hover:bg-primary-50 hover:text-primary-700 hover:border-primary-300 focus-visible:ring-2 focus-visible:ring-primary-500 transition"
                                                aria-haspopup="true" :aria-expanded="open">
                                                <i class="bi bi-three-dots-vertical text-lg"></i>
                                            </button>

                                            {{-- backdrop (klik di backdrop saja yang menutup) --}}
                                            <template x-if="open">
                                                <div class="fixed inset-0 z-[60]" @click.self="close()"
                                                    aria-hidden="true"></div>
                                            </template>

                                            {{-- popover menu --}}
                                            <div x-cloak x-show="open" x-transition.origin.top.right x-ref="menu"
                                                class="fixed z-[70] w-56 overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-soft-lg"
                                                :style="style" role="menu" tabindex="-1" @click.stop>
                                                <div class="max-h-72 overflow-auto p-1.5 space-y-0.5">
                                                    <a href="{{ route('admin.questions.edit', $q) }}"
                                                        class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors"
                                                        role="menuitem">
                                                        <i class="bi bi-pencil-square"></i>
                                                        <span>Edit Pertanyaan</span>
                                                    </a>
                                                    <div class="my-1 border-t border-primary-100"></div>
                                                    <form method="POST"
                                                        action="{{ route('admin.questions.destroy', $q) }}"
                                                        onsubmit="return confirm('Hapus pertanyaan ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-danger-700 hover:bg-danger-50 transition-colors"
                                                            role="menuitem">
                                                            <i class="bi bi-trash"></i>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- /MENU --}}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- (Opsional) Helper popMenu() tidak dipakai; boleh dihapus jika tidak diperlukan --}}
    <script>
        function popMenu() {
            return {
                open: false,
                style: '',
                toggle() {
                    this.open ? this.close() : this.openMenu()
                },
                close() {
                    this.open = false
                },
                openMenu() {
                    this.open = true;
                    this.$nextTick(() => this.place())
                },
                place() {
                    const btn = this.$refs.btn.getBoundingClientRect();
                    const menuEl = this.$refs.menu;

                    const prevDisplay = menuEl.style.display;
                    const prevVis = menuEl.style.visibility;
                    menuEl.style.visibility = 'hidden';
                    menuEl.style.display = 'block';
                    const m = menuEl.getBoundingClientRect();

                    let top = btn.bottom + 8 + window.scrollY;
                    let left = btn.right - m.width + window.scrollX;

                    const spaceBelow = window.innerHeight - btn.bottom;
                    const spaceAbove = btn.top;
                    if (spaceBelow < m.height + 12 && spaceAbove > spaceBelow) {
                        top = btn.top - m.height - 8 + window.scrollY;
                    }

                    const maxLeft = window.scrollX + window.innerWidth - m.width - 8;
                    const maxTop = window.scrollY + window.innerHeight - m.height - 8;
                    left = Math.max(8 + window.scrollX, Math.min(left, maxLeft));
                    top = Math.max(8 + window.scrollY, Math.min(top, maxTop));

                    this.style = `top:${top}px;left:${left}px`;

                    menuEl.style.display = prevDisplay;
                    menuEl.style.visibility = prevVis;
                },
                init() {
                    const reflow = () => {
                        if (this.open) this.place()
                    };
                    window.addEventListener('resize', reflow);
                    window.addEventListener('scroll', reflow, {
                        passive: true
                    });
                }
            }
        }
    </script>
</x-app-layout>
