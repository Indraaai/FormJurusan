<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">Kelola Forms</h2>
                <p class="text-sm text-secondary-600 mt-1">Buat, edit, dan kelola semua formulir Anda</p>
            </div>

            @if (Route::has('admin.forms.create'))
                <a href="{{ route('admin.forms.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:shadow-soft-md hover:from-primary-700 hover:to-primary-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-all duration-200">
                    <i class="bi bi-plus-lg"></i>
                    <span>Buat Form Baru</span>
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
            @if ($forms->count())
                <!-- Desktop table -->
                <div class="hidden md:block bg-white rounded-xl shadow-soft border border-primary-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-primary-100">
                            <thead class="bg-gradient-to-r from-primary-50 to-primary-100/50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Judul Form</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        UID</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-center text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Respons</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Dibuat</th>
                                    <th scope="col"
                                        class="py-3.5 pl-3 pr-6 text-right text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary-100 bg-white">
                                @foreach ($forms as $form)
                                    <tr class="hover:bg-primary-50/50 transition-colors duration-150">
                                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-10 w-10 flex-shrink-0 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                                    <i class="bi bi-file-earmark-text text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.forms.edit', $form) }}"
                                                        class="font-semibold text-secondary-900 hover:text-primary-600 transition-colors">
                                                        {{ $form->title }}
                                                    </a>
                                                    <p class="text-xs text-secondary-500 mt-0.5">
                                                        {{ $form->questions()->count() }} pertanyaan
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4">
                                            <code
                                                class="inline-flex items-center rounded-md bg-secondary-100 px-2.5 py-1 text-xs font-mono text-secondary-700">
                                                {{ $form->uid }}
                                            </code>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4">
                                            @php $published = (bool) $form->is_published; @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $published ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">
                                                <span
                                                    class="h-2 w-2 rounded-full {{ $published ? 'bg-success-500' : 'bg-warning-500' }}"></span>
                                                {{ $published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-100 text-sm font-semibold text-primary-700">
                                                {{ $form->responses_count ?? $form->responses()->count() }}
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
                                                <!-- Preview Button -->
                                                <a href="{{ route('admin.forms.preview', $form) }}"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-secondary-100 px-3 py-1.5 text-xs font-medium text-secondary-700 hover:bg-secondary-200 transition-colors"
                                                    title="Preview">
                                                    <i class="bi bi-eye"></i>
                                                    <span class="hidden lg:inline">Preview</span>
                                                </a>

                                                <!-- Dropdown Menu -->
                                                <div class="relative" x-data="{ open: false }"
                                                    @click.away="open = false">
                                                    <button type="button" @click="open = !open"
                                                        class="inline-flex items-center justify-center rounded-lg border border-primary-200 bg-white p-2 text-secondary-600 hover:bg-primary-50 hover:text-primary-700 hover:border-primary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all"
                                                        :aria-expanded="open">
                                                        <i class="bi bi-three-dots-vertical text-lg"></i>
                                                    </button>

                                                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                        class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl border border-primary-100 bg-white shadow-soft-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                        role="menu" style="display: none;">
                                                        <div class="p-1.5 space-y-0.5">
                                                            <a href="{{ route('admin.forms.edit', $form) }}"
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-pencil-square"></i>
                                                                <span>Edit Form</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.sections.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-layout-text-sidebar"></i>
                                                                <span>Sections</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.questions.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-question-circle"></i>
                                                                <span>Questions</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.logic.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-diagram-3"></i>
                                                                <span>Logic</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.responses.index', $form) }}"
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-chat-left-text"></i>
                                                                <span>Responses</span>
                                                            </a>
                                                            <a href="{{ route('admin.forms.settings.edit', $form) }}"
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-gear"></i>
                                                                <span>Settings</span>
                                                            </a>

                                                            <div class="my-1 border-t border-primary-100"></div>

                                                            @if (!$form->is_published)
                                                                <form method="POST"
                                                                    action="{{ route('admin.forms.publish', $form) }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-success-700 hover:bg-success-50 transition-colors">
                                                                        <i class="bi bi-check-circle"></i>
                                                                        <span>Publish Form</span>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form method="POST"
                                                                    action="{{ route('admin.forms.unpublish', $form) }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-warning-700 hover:bg-warning-50 transition-colors">
                                                                        <i class="bi bi-x-circle"></i>
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
                                                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-danger-700 hover:bg-danger-50 transition-colors">
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

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4">
                    @foreach ($forms as $form)
                        <div
                            class="bg-white rounded-xl shadow-soft border border-primary-100 p-4 hover:shadow-soft-md transition-all duration-200">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div
                                        class="h-12 w-12 flex-shrink-0 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                        <i class="bi bi-file-earmark-text text-white text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('admin.forms.edit', $form) }}"
                                            class="font-semibold text-secondary-900 hover:text-primary-600 transition-colors block truncate">
                                            {{ $form->title }}
                                        </a>
                                        <p class="text-xs text-secondary-500 mt-1">
                                            {{ $form->questions()->count() }} pertanyaan •
                                            {{ $form->responses_count ?? $form->responses()->count() }} respons
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-3">
                                <code
                                    class="inline-flex items-center rounded-md bg-secondary-100 px-2 py-1 text-xs font-mono text-secondary-700">
                                    {{ $form->uid }}
                                </code>

                                @php $published = (bool) $form->is_published; @endphp
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $published ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">
                                    <span
                                        class="h-2 w-2 rounded-full {{ $published ? 'bg-success-500' : 'bg-warning-500' }}"></span>
                                    {{ $published ? 'Published' : 'Draft' }}
                                </span>
                            </div>

                            <div class="text-xs text-secondary-500 mb-3">
                                <i class="bi bi-calendar3"></i>
                                {{ $form->created_at->timezone(config('app.timezone'))->format('d M Y, H:i') }}
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('admin.forms.preview', $form) }}"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-secondary-100 px-3 py-2 text-xs font-medium text-secondary-700 hover:bg-secondary-200 transition-colors">
                                    <i class="bi bi-eye"></i>
                                    <span>Preview</span>
                                </a>

                                <a href="{{ route('admin.forms.edit', $form) }}"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary-100 px-3 py-2 text-xs font-medium text-primary-700 hover:bg-primary-200 transition-colors">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($forms->hasPages())
                    <div class="mt-6">
                        {{ $forms->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div
                    class="bg-white rounded-xl shadow-soft border border-primary-100 p-12 text-center">
                    <div
                        class="mx-auto w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-inbox text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Belum ada form</h3>
                    <p class="text-sm text-secondary-600 mb-6">Mulai buat form pertama Anda sekarang</p>
                    @if (Route::has('admin.forms.create'))
                        <a href="{{ route('admin.forms.create') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:shadow-soft-md hover:from-primary-700 hover:to-primary-600 transition-all duration-200">
                            <i class="bi bi-plus-lg"></i>
                            <span>Buat Form Baru</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
