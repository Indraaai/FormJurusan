<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-emerald-900 leading-tight">Daftar Form</h2>
                <p class="text-sm text-emerald-700/70 mt-1">Kelola form dengan tampilan yang rapi & minimalis.</p>
            </div>

            @if (Route::has('admin.forms.create'))
                <a href="{{ route('admin.forms.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm
                          hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 transition">
                    <!-- plus icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Form
                </a>
            @endif
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm">
                @if ($forms->count())
                    <!-- Desktop table -->
                    <div class="hidden sm:block">
                        <div class="overflow-x-auto rounded-2xl">
                            <table class="min-w-full text-sm">
                                <thead class="bg-emerald-50/60 text-emerald-900">
                                    <tr>
                                        <th class="py-3.5 pl-6 pr-4 text-left font-medium">Judul</th>
                                        <th class="py-3.5 px-4 text-left font-medium">UID</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Status</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Responses</th>
                                        <th class="py-3.5 px-4 text-left font-medium">Dibuat</th>
                                        <th class="py-3.5 pr-6 pl-4 text-right font-medium">Menu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-emerald-100">
                                    @foreach ($forms as $form)
                                        <tr class="hover:bg-emerald-50/40 transition">
                                            <td class="py-3.5 pl-6 pr-4 font-medium text-emerald-900">
                                                <a href="{{ route('admin.forms.edit', $form) }}"
                                                    class="text-emerald-700 hover:underline">
                                                    {{ $form->title }}
                                                </a>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <code
                                                    class="rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] text-emerald-700">
                                                    {{ $form->uid }}
                                                </code>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                @php $published = (bool) $form->is_published; @endphp
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs
                                                    {{ $published ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                                    <span
                                                        class="h-2 w-2 rounded-full {{ $published ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                    {{ $published ? 'Published' : 'Draft' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-emerald-900">
                                                {{ $form->responses_count ?? $form->responses()->count() }}
                                            </td>
                                            <td class="py-3.5 px-4 text-emerald-900">
                                                {{ $form->created_at->timezone(config('app.timezone'))->format('d M Y H:i') }}

                                            </td>
                                            <td class="py-3.5 pr-6 pl-4">
                                                <div class="flex items-center justify-end gap-2" x-data="{ open: false }"
                                                    @keydown.escape.window="open=false">
                                                    <!-- Quick Preview -->
                                                    <a href="{{ route('admin.forms.preview', $form) }}"
                                                        class="hidden lg:inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs text-white hover:bg-emerald-700 transition">
                                                        <!-- eye icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        Preview
                                                    </a>


                                                    <!-- Ellipsis menu (DESKTOP) -->
                                                    <div class="relative" x-data="{
                                                        open: false,
                                                        top: 0,
                                                        left: 0,
                                                        placement: 'bottom',
                                                        openMenu() {
                                                            this.open = true;
                                                            this.$nextTick(() => {
                                                                const btn = this.$refs.btn.getBoundingClientRect();
                                                                const menuEl = this.$refs.menu;
                                                                // render dulu agar ada ukuran
                                                                const menu = menuEl.getBoundingClientRect();
                                                                const spaceBelow = window.innerHeight - btn.bottom;
                                                                const spaceAbove = btn.top;
                                                                const dropUp = spaceBelow < menu.height && spaceAbove > spaceBelow;
                                                    
                                                                this.placement = dropUp ? 'top' : 'bottom';
                                                                const top = dropUp ?
                                                                    (btn.top + window.scrollY - menu.height - 8) :
                                                                    (btn.bottom + window.scrollY + 8);
                                                                const left = (btn.right + window.scrollX) - menu.width; // rata kanan tombol
                                                    
                                                                this.top = Math.max(8, top);
                                                                this.left = Math.max(8, left);
                                                            });
                                                        }
                                                    }"
                                                        @keydown.escape.window="open=false" x-init="const onReflow = () => { if (open) { open = false } };
                                                        window.addEventListener('resize', onReflow);
                                                        window.addEventListener('scroll', onReflow, { passive: true });">
                                                        <button type="button" x-ref="btn"
                                                            @click="open ? open=false : openMenu()"
                                                            class="inline-flex items-center rounded-xl border border-emerald-200 bg-white p-2 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 transition"
                                                            aria-haspopup="true" :aria-expanded="open">
                                                            <!-- dots icon -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                        </button>

                                                        <!-- Backdrop untuk klik di luar -->
                                                        <template x-if="open">
                                                            <div class="fixed inset-0 z-40" @click="open=false"
                                                                aria-hidden="true"></div>
                                                        </template>

                                                        <!-- Pop-up fixed dengan auto-flip (atas/bawah) -->
                                                        <div x-cloak x-show="open" x-transition x-ref="menu"
                                                            class="fixed z-50 w-56 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl ring-1 ring-emerald-100"
                                                            :style="`top:${top}px;left:${left}px`" role="menu"
                                                            tabindex="-1">
                                                            <div class="max-h-72 overflow-auto p-1.5">
                                                                <a href="{{ route('admin.forms.sections.index', $form) }}"
                                                                    class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                    role="menuitem">
                                                                    <span
                                                                        class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                                    Sections
                                                                </a>
                                                                <a href="{{ route('admin.forms.questions.index', $form) }}"
                                                                    class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                    role="menuitem">
                                                                    <span
                                                                        class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                                    Questions
                                                                </a>
                                                                <a href="{{ route('admin.forms.logic.index', $form) }}"
                                                                    class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                    role="menuitem">
                                                                    <span
                                                                        class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                                    Logic
                                                                </a>
                                                                <a href="{{ route('admin.forms.responses.index', $form) }}"
                                                                    class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                    role="menuitem">
                                                                    <span
                                                                        class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                                    Responses
                                                                </a>
                                                                <a href="{{ route('admin.forms.settings.edit', $form) }}"
                                                                    class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50"
                                                                    role="menuitem">
                                                                    <span
                                                                        class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                                    Settings
                                                                </a>
                                                                <a href="{{ route('admin.forms.preview', $form) }}"
                                                                    class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50 lg:hidden"
                                                                    role="menuitem">
                                                                    <span
                                                                        class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                                    Preview
                                                                </a>

                                                                <div class="my-1 border-t border-emerald-100"></div>

                                                                @if (!$form->is_published)
                                                                    <form method="POST"
                                                                        action="{{ route('admin.forms.publish', $form) }}">
                                                                        @csrf @method('PUT')
                                                                        <button type="submit"
                                                                            class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm text-emerald-800 hover:bg-emerald-50"
                                                                            role="menuitem">
                                                                            Publish
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <form method="POST"
                                                                        action="{{ route('admin.forms.unpublish', $form) }}">
                                                                        @csrf @method('PUT')
                                                                        <button type="submit"
                                                                            class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm text-emerald-800 hover:bg-emerald-50"
                                                                            role="menuitem">
                                                                            Unpublish
                                                                        </button>
                                                                    </form>
                                                                @endif

                                                                <form method="POST"
                                                                    action="{{ route('admin.forms.destroy', $form) }}"
                                                                    onsubmit="return confirm('Hapus form ini?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit"
                                                                        class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm text-red-700 hover:bg-red-50"
                                                                        role="menuitem">
                                                                        Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 pb-6 pt-4">
                            <div class="rounded-xl border border-emerald-100 bg-white/60 p-3">
                                {{ $forms->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- Mobile cards -->
                    <div class="sm:hidden">
                        <ul class="divide-y divide-emerald-100">
                            @foreach ($forms as $form)
                                <li class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.forms.edit', $form) }}"
                                                class="block font-medium text-emerald-900">
                                                {{ $form->title }}
                                            </a>
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                                <code
                                                    class="rounded bg-emerald-50 px-2 py-0.5 text-emerald-700">{{ $form->uid }}</code>
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">
                                                    <span
                                                        class="h-1.5 w-1.5 rounded-full {{ $form->is_published ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                    {{ $form->is_published ? 'Published' : 'Draft' }}
                                                </span>
                                            </div>
                                            <div class="mt-2 text-sm text-emerald-900/80">
                                                {{ $form->responses_count ?? $form->responses()->count() }} responses ·
                                                {{ $form->created_at?->format('Y-m-d H:i') }}
                                            </div>
                                        </div>

                                        <!-- Mobile action menu -->
                                        <!-- Mobile action menu (MOBILE) -->
                                        <div class="relative" x-data="{
                                            open: false,
                                            top: 0,
                                            left: 0,
                                            placement: 'bottom',
                                            openMenu() {
                                                this.open = true;
                                                this.$nextTick(() => {
                                                    const btn = this.$refs.btn.getBoundingClientRect();
                                                    const menuEl = this.$refs.menu;
                                                    const menu = menuEl.getBoundingClientRect();
                                                    const spaceBelow = window.innerHeight - btn.bottom;
                                                    const spaceAbove = btn.top;
                                                    const dropUp = spaceBelow < menu.height && spaceAbove > spaceBelow;
                                        
                                                    this.placement = dropUp ? 'top' : 'bottom';
                                                    const top = dropUp ?
                                                        (btn.top + window.scrollY - menu.height - 8) :
                                                        (btn.bottom + window.scrollY + 8);
                                                    const left = (btn.right + window.scrollX) - menu.width;
                                        
                                                    this.top = Math.max(8, top);
                                                    this.left = Math.max(8, left);
                                                });
                                            }
                                        }"
                                            @keydown.escape.window="open=false" x-init="const onReflow = () => { if (open) { open = false } };
                                            window.addEventListener('resize', onReflow);
                                            window.addEventListener('scroll', onReflow, { passive: true });">
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
                                                :style="`top:${top}px;left:${left}px`" role="menu" tabindex="-1">
                                                <div class="max-h-72 overflow-auto p-1.5">
                                                    <a href="{{ route('admin.forms.preview', $form) }}"
                                                        class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                        role="menuitem">Preview</a>
                                                    <a href="{{ route('admin.forms.sections.index', $form) }}"
                                                        class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                        role="menuitem">Sections</a>
                                                    <a href="{{ route('admin.forms.questions.index', $form) }}"
                                                        class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                        role="menuitem">Questions</a>
                                                    <a href="{{ route('admin.forms.logic.index', $form) }}"
                                                        class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                        role="menuitem">Logic</a>
                                                    <a href="{{ route('admin.forms.responses.index', $form) }}"
                                                        class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                        role="menuitem">Responses</a>
                                                    <a href="{{ route('admin.forms.settings.edit', $form) }}"
                                                        class="block rounded-xl px-3 py-2 text-sm hover:bg-emerald-50"
                                                        role="menuitem">Settings</a>
                                                    <div class="my-1 border-t border-emerald-100"></div>
                                                    @if (!$form->is_published)
                                                        <form method="POST"
                                                            action="{{ route('admin.forms.publish', $form) }}">
                                                            @csrf @method('PUT')
                                                            <button type="submit"
                                                                class="block w-full rounded-xl px-3 py-2 text-left text-sm hover:bg-emerald-50"
                                                                role="menuitem">
                                                                Publish
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST"
                                                            action="{{ route('admin.forms.unpublish', $form) }}">
                                                            @csrf @method('PUT')
                                                            <button type="submit"
                                                                class="block w-full rounded-xl px-3 py-2 text-left text-sm hover:bg-emerald-50"
                                                                role="menuitem">
                                                                Unpublish
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST"
                                                        action="{{ route('admin.forms.destroy', $form) }}"
                                                        onsubmit="return confirm('Hapus form ini?');">
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

                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="px-4 pb-6 pt-2">
                            <div class="rounded-xl border border-emerald-100 bg-white/60 p-3">
                                {{ $forms->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/40 p-8">
                            <div class="mx-auto h-10 w-10 rounded-full bg-emerald-200"></div>
                        </div>
                        <p class="text-emerald-900">Belum ada form.</p>
                        @if (Route::has('admin.forms.create'))
                            <a href="{{ route('admin.forms.create') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm hover:bg-emerald-700 transition">
                                Buat Form
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
