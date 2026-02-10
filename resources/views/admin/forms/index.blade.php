<x-app-layout>
    @php
        $totalForms = \App\Models\Form::count();
        $publishedCount = \App\Models\Form::where('is_published', true)->count();
        $draftCount = max($totalForms - $publishedCount, 0);
        $totalResponses = \App\Models\FormResponse::count();
        $avgResponses = $totalForms > 0 ? number_format($totalResponses / max($totalForms, 1), 1) : '0.0';
        $lastUpdatedForm = \App\Models\Form::orderByDesc('updated_at')->select('updated_at')->first();
        $lastUpdatedLabel = $lastUpdatedForm?->updated_at
            ? $lastUpdatedForm->updated_at->timezone(config('app.timezone'))->format('d M Y, H:i')
            : 'Belum ada data';
        $firstForm = $forms->first();
    @endphp

    <x-slot name="header">
        <div class="space-y-4 animate-fade-in">
            <div class="relative overflow-hidden rounded-3xl bg-primary-600 text-white shadow-soft-lg">
                <div class="relative z-10 p-6 lg:p-8 space-y-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.35em] text-white/70 font-semibold">Forms Directory
                            </p>
                            <h2 class="mt-2 text-3xl font-bold leading-tight">Kelola Forms</h2>
                            <p class="mt-2 text-white/80 text-sm sm:text-base max-w-2xl">
                                Konsol terpusat untuk membuat, mengedit, dan memonitor seluruh form Anda. Pembaruan
                                terakhir: <span class="font-semibold">{{ $lastUpdatedLabel }}</span>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            @if ($firstForm)
                                <a href="{{ route('admin.forms.preview', $firstForm) }}" rel="noopener"
                                    class="inline-flex items-center gap-2 rounded-2xl border border-white/30 px-5 py-2.5 text-sm font-semibold text-white/90 backdrop-blur hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 transition-all">
                                    <i class="bi bi-eye"></i>
                                    Preview Form Terbaru
                                </a>
                            @endif

                            @if (Route::has('admin.forms.create'))
                                <a href="{{ route('admin.forms.create') }}"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-primary-700 shadow-soft hover:shadow-soft-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-200 transition-all">
                                    <i class="bi bi-plus-lg"></i>
                                    Buat Form Baru
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 text-white/85 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-wide text-white/60">Total Forms</p>
                            <p class="mt-1 text-3xl font-bold text-white">{{ number_format($totalForms) }}</p>
                            <p class="text-xs">Published: {{ number_format($publishedCount) }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-wide text-white/60">Draft</p>
                            <p class="mt-1 text-3xl font-bold text-white">{{ number_format($draftCount) }}</p>
                            <p class="text-xs">Perlu tindakan publikasi</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-wide text-white/60">Total Respons</p>
                            <p class="mt-1 text-3xl font-bold text-white">{{ number_format($totalResponses) }}</p>
                            <p class="text-xs">Rata-rata {{ $avgResponses }} / form</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-wide text-white/60">Workflow</p>
                            <p class="mt-1 text-base font-semibold text-white">Gunakan pencarian & filter di bawah</p>
                            <p class="text-xs">Optimalkan proses kurasi form</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <div class="py-8" x-data="formListing()">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-secondary-100 bg-white/95 p-4 shadow-soft space-y-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-secondary-400"></i>
                        <input type="search" name="q" placeholder="Cari judul atau UID form..."
                            x-model.debounce.200ms="search" @keydown.escape="search = ''"
                            class="w-full rounded-2xl border border-secondary-200 bg-secondary-50 pl-11 pr-4 py-3 text-sm text-secondary-900 placeholder:text-secondary-400 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="setStatus('all')"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-all"
                            :class="status === 'all' ? 'bg-primary-600 text-white shadow-soft' :
                                'bg-secondary-100 text-secondary-600'">
                            <i class="bi bi-asterisk"></i>
                            Semua
                        </button>
                        <button type="button" @click="setStatus('published')"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-all"
                            :class="status === 'published' ? 'bg-success-600 text-white shadow-soft' :
                                'bg-secondary-100 text-secondary-600'">
                            <i class="bi bi-send-check"></i>
                            Published
                        </button>
                        <button type="button" @click="setStatus('draft')"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-all"
                            :class="status === 'draft' ? 'bg-warning-500 text-white shadow-soft' :
                                'bg-secondary-100 text-secondary-600'">
                            <i class="bi bi-pencil"></i>
                            Draft
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs uppercase tracking-wide text-secondary-500">Tampilan</span>
                        <div class="inline-flex rounded-2xl border border-secondary-200 bg-secondary-50 p-0.5">
                            <button type="button" @click="setView('table')"
                                class="inline-flex items-center gap-1.5 rounded-2xl px-3 py-1.5 text-sm font-semibold transition-all"
                                :class="view === 'table' ? 'bg-white text-primary-700 shadow-soft' : 'text-secondary-500'">
                                <i class="bi bi-table"></i>
                                Tabel
                            </button>
                            <button type="button" @click="setView('cards')"
                                class="inline-flex items-center gap-1.5 rounded-2xl px-3 py-1.5 text-sm font-semibold transition-all"
                                :class="view === 'cards' ? 'bg-white text-primary-700 shadow-soft' : 'text-secondary-500'">
                                <i class="bi bi-grid"></i>
                                Kartu
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-xs text-secondary-500">
                    <span class="inline-flex items-center gap-1 font-semibold text-secondary-700">
                        <i class="bi bi-funnel"></i>
                        <span x-text="statusLabel()"></span>
                    </span>
                    <span>Halaman ini memuat {{ $forms->count() }} form dari {{ number_format($totalForms) }} total
                        form.</span>
                </div>
            </div>

            @if ($forms->count())
                <div x-cloak x-show="view === 'table'"
                    class="relative rounded-2xl border border-primary-100 bg-white shadow-soft overflow-visible">
                    <div class="overflow-x-auto">
                        <table class="min-w-[60rem] w-full divide-y divide-primary-50">
                            <thead class="bg-primary-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-secondary-800">
                                        Judul Form</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-secondary-800">
                                        UID</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-secondary-800">
                                        Status</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-secondary-800">
                                        Respons</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-secondary-800">
                                        Dibuat</th>
                                    <th scope="col"
                                        class="py-3.5 pl-3 pr-6 text-right text-xs font-semibold uppercase tracking-wider text-secondary-800">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary-50 bg-white">
                                @foreach ($forms as $form)
                                    @php
                                        $published = (bool) $form->is_published;
                                        $rowTitle = strtolower($form->title ?? '');
                                        $rowUid = strtolower($form->uid ?? '');
                                    @endphp
                                    <tr data-form-row data-status="{{ $published ? 'published' : 'draft' }}"
                                        data-title="{{ e($rowTitle) }}" data-uid="{{ e($rowUid) }}"
                                        x-show="matches($el)" x-transition.opacity.duration.150ms
                                        class="hover:bg-primary-50/70 transition-colors duration-150">
                                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-10 w-10 flex-shrink-0 rounded-xl bg-primary-600 flex items-center justify-center">
                                                    <i class="bi bi-file-earmark-text text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.forms.edit', $form) }}"
                                                        class="font-semibold text-secondary-900 hover:text-primary-600 transition-colors">
                                                        {{ $form->title }}
                                                    </a>
                                                    <p class="text-xs text-secondary-500 mt-0.5">
                                                        {{ $form->questions_count ?? 0 }} pertanyaan
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4">
                                            <code
                                                class="inline-flex items-center rounded-2xl bg-secondary-100 px-2.5 py-1 text-xs font-mono text-secondary-700">
                                                {{ $form->uid }}
                                            </code>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4">
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $published ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">
                                                <span
                                                    class="h-2 w-2 rounded-full {{ $published ? 'bg-success-500' : 'bg-warning-500' }}"></span>
                                                {{ $published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-primary-50 text-sm font-semibold text-primary-700">
                                                {{ $form->responses_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-secondary-600">
                                            {{ $form->created_at->timezone(config('app.timezone'))->format('d M Y') }}
                                            <span class="text-xs text-secondary-400 block mt-0.5">
                                                {{ $form->created_at->timezone(config('app.timezone'))->format('H:i') }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-3 pr-6">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.forms.preview', $form) }}"
                                                    class="inline-flex items-center gap-1.5 rounded-2xl bg-secondary-100 px-3 py-1.5 text-xs font-medium text-secondary-700 hover:bg-secondary-200 transition-colors"
                                                    title="Preview">
                                                    <i class="bi bi-eye"></i>
                                                    <span class="hidden lg:inline">Preview</span>
                                                </a>
                                                <div class="relative" x-data="actionMenu()"
                                                    @keydown.escape.window="close()">
                                                    <button type="button" @click="toggle()"
                                                        class="inline-flex items-center justify-center rounded-2xl border border-primary-200 bg-white p-2 text-secondary-600 hover:bg-primary-50 hover:text-primary-700 hover:border-primary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all"
                                                        :aria-expanded="open"
                                                        :aria-label="open ? 'Tutup menu aksi form' : 'Buka menu aksi form'">
                                                        <i class="bi bi-three-dots-vertical text-lg"></i>
                                                    </button>
                                                    <div x-cloak x-show="open"
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                        class="absolute right-0 z-50 mt-2 w-60 origin-top-right rounded-2xl border border-primary-100 bg-white shadow-soft-lg ring-1 ring-black/5 focus:outline-none"
                                                        @click.outside="close()" role="menu">
                                                        <div class="p-1.5 space-y-0.5">
                                                            <a href="{{ route('admin.forms.edit', $form) }}"
                                                                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-pencil-square"></i>
                                                                <span>Edit Form</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.sections.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-layout-text-sidebar"></i>
                                                                <span>Sections</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.questions.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-question-circle"></i>
                                                                <span>Questions</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.logic.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-diagram-3"></i>
                                                                <span>Logic</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.responses.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-chat-left-text"></i>
                                                                <span>Responses</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.settings.edit', $form) }}"
                                                                class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-gear"></i>
                                                                <span>Settings</span>
                                                            </a>

                                                            <div class="my-1 border-t border-primary-100"></div>

                                                            @if (!$form->is_published)
                                                                <form method="POST"
                                                                    action="{{ route('admin.forms.publish', $form) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-success-700 hover:bg-success-50 transition-colors">
                                                                        <i class="bi bi-send-check"></i>
                                                                        <span>Publish Form</span>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form method="POST"
                                                                    action="{{ route('admin.forms.unpublish', $form) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-warning-700 hover:bg-warning-50 transition-colors">
                                                                        <i class="bi bi-eye-slash"></i>
                                                                        <span>Unpublish Form</span>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            <form method="POST"
                                                                action="{{ route('admin.forms.destroy', $form) }}"
                                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus form ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-danger-700 hover:bg-danger-50 transition-colors">
                                                                    <i class="bi bi-trash"></i>
                                                                    <span>Hapus Form</span>
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
                </div>

                <div x-cloak x-show="view === 'cards'" class="space-y-4">
                    @foreach ($forms as $form)
                        @php
                            $published = (bool) $form->is_published;
                            $rowTitle = strtolower($form->title ?? '');
                            $rowUid = strtolower($form->uid ?? '');
                        @endphp
                        <div data-form-row data-status="{{ $published ? 'published' : 'draft' }}"
                            data-title="{{ e($rowTitle) }}" data-uid="{{ e($rowUid) }}" x-show="matches($el)"
                            x-transition.opacity.duration.150ms
                            class="bg-white rounded-2xl shadow-soft border border-primary-100 p-4 hover:shadow-soft-md transition-all duration-200">
                            <div class="flex items-start gap-3">
                                <div
                                    class="h-12 w-12 flex-shrink-0 rounded-xl bg-primary-600 flex items-center justify-center">
                                    <i class="bi bi-file-earmark-text text-white text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('admin.forms.edit', $form) }}"
                                        class="font-semibold text-secondary-900 hover:text-primary-600 transition-colors block truncate">
                                        {{ $form->title }}
                                    </a>
                                    <p class="text-xs text-secondary-500 mt-1">
                                        {{ $form->questions_count ?? 0 }} pertanyaan •
                                        {{ $form->responses_count ?? 0 }} respons
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $published ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">
                                    <span
                                        class="h-2 w-2 rounded-full {{ $published ? 'bg-success-500' : 'bg-warning-500' }}"></span>
                                    {{ $published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs text-secondary-500">
                                <code
                                    class="inline-flex items-center rounded-md bg-secondary-100 px-2 py-1 text-xs font-mono text-secondary-700">
                                    {{ $form->uid }}
                                </code>
                                <span>
                                    <i class="bi bi-calendar3"></i>
                                    {{ $form->created_at->timezone(config('app.timezone'))->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <a href="{{ route('admin.forms.preview', $form) }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-secondary-100 px-3 py-2 text-xs font-medium text-secondary-700 hover:bg-secondary-200 transition-colors">
                                    <i class="bi bi-eye"></i>
                                    Preview
                                </a>
                                <a href="{{ route('admin.forms.edit', $form) }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary-100 px-3 py-2 text-xs font-medium text-primary-700 hover:bg-primary-200 transition-colors">
                                    <i class="bi bi-pencil-square"></i>
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($forms->hasPages())
                    <div class="pt-4">
                        {{ $forms->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-3xl shadow-soft border border-primary-100 p-12 text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-600 text-white">
                        <i class="bi bi-inbox text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Belum ada form</h3>
                    <p class="text-sm text-secondary-600 mb-6">Mulai buat form pertama Anda untuk mengumpulkan data</p>
                    @if (Route::has('admin.forms.create'))
                        <a href="{{ route('admin.forms.create') }}"
                            class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:shadow-soft-md transition-all">
                            <i class="bi bi-plus-lg"></i>
                            Buat Form Baru
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('actionMenu', () => ({
                open: false,
                toggle() {
                    this.open = !this.open;
                },
                close() {
                    this.open = false;
                },
            }));

            Alpine.data('formListing', () => ({
                search: '',
                status: 'all',
                view: window.matchMedia('(max-width: 767px)').matches ? 'cards' : 'table',
                matches(el) {
                    const status = el.dataset.status || 'draft';
                    const query = this.search.trim().toLowerCase();
                    const textBucket = `${el.dataset.title || ''} ${el.dataset.uid || ''}`;
                    const statusMatch = this.status === 'all' || status === this.status;
                    const queryMatch = query === '' || textBucket.includes(query);
                    return statusMatch && queryMatch;
                },
                setStatus(value) {
                    this.status = value;
                },
                statusLabel() {
                    if (this.status === 'published') return 'Filter: Published';
                    if (this.status === 'draft') return 'Filter: Draft';
                    return 'Filter: Semua';
                },
                setView(mode) {
                    this.view = mode;
                },
            }));
        });
    </script>
</x-app-layout>
