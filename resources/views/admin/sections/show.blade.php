<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Section Detail — {{ $section->form->title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sections.edit', $section) }}"
                    class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Edit Section
                </a>
                <a href="{{ route('admin.forms.sections.index', $section->form) }}"
                    class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                    Kembali ke Sections
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

            {{-- KARTU DETAIL SECTION --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-emerald-700/70">Judul</div>
                            <div class="mt-1 font-medium text-emerald-900">
                                {{ $section->title ?? 'Tanpa judul' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-emerald-700/70">Posisi</div>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-sm text-emerald-800">
                                    {{ $section->position }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-emerald-700/70">Form</div>
                            <div class="mt-1 font-medium text-emerald-900">
                                {{ $section->form->title }}
                            </div>
                        </div>
                    </div>

                    @if ($section->description)
                        <div class="mt-6">
                            <div class="text-xs uppercase tracking-wide text-emerald-700/70">Deskripsi</div>
                            <div
                                class="mt-2 rounded-xl border border-emerald-100 bg-emerald-50/40 p-4 text-emerald-900">
                                <div class="prose max-w-none prose-p:my-2 prose-li:my-1">
                                    {!! nl2br(e($section->description)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- DAFTAR PERTANYAAN --}}
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 p-6">
                    <div>
                        <h3 class="font-semibold text-emerald-900">Pertanyaan</h3>
                        <p class="text-sm text-emerald-700/70">Pertanyaan dalam section ini.</p>
                    </div>
                    <a href="{{ route('admin.forms.questions.create', $section->form) }}?section_id={{ $section->id }}"
                        class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700 focus-visible:ring-2 focus-visible:ring-emerald-500 transition">
                        Tambah Pertanyaan
                    </a>
                </div>

                <div class="px-6 pb-6">
                    @if ($section->questions->isEmpty())
                        <div class="flex flex-col items-center gap-3 py-10 text-center">
                            <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/40 p-8">
                                <div class="mx-auto h-10 w-10 rounded-full bg-emerald-200"></div>
                            </div>
                            <p class="text-emerald-900">Belum ada pertanyaan.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-emerald-100">
                            @foreach ($section->questions as $q)
                                <li class="py-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 text-xs text-emerald-700/70">
                                                <span>#{{ $q->position }}</span>
                                                <span>•</span>
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-800">
                                                    {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                                </span>
                                            </div>
                                            <div class="mt-1 font-medium text-emerald-900">
                                                {{ $q->title }}
                                            </div>
                                        </div>

                                        {{-- MENU TITIK TIGA (POPOVER FIXED + AUTO-FLIP) --}}
                                        <div class="relative" x-data="{
                                            open: false,
                                            style: '',
                                            toggle() { this.open ? this.close() : this.openMenu() },
                                            close() { this.open = false },
                                            openMenu() { this.open = true;
                                                this.$nextTick(() => this.place()) },
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
                                                class="inline-flex items-center rounded-xl border border-emerald-200 bg-white p-2 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 focus-visible:ring-2 focus-visible:ring-emerald-500 transition"
                                                aria-haspopup="true" :aria-expanded="open">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>

                                            {{-- backdrop (klik di backdrop saja yang menutup) --}}
                                            <template x-if="open">
                                                <div class="fixed inset-0 z-[60]" @click.self="close()"
                                                    aria-hidden="true"></div>
                                            </template>

                                            {{-- popover menu --}}
                                            <div x-cloak x-show="open" x-transition.origin.top.right x-ref="menu"
                                                class="fixed z-[70] w-56 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl ring-1 ring-emerald-100"
                                                :style="style" role="menu" tabindex="-1" @click.stop>
                                                <div class="max-h-72 overflow-auto p-1.5">
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
