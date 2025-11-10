<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">Sections</h2>
                <p class="text-sm text-secondary-600 mt-1">Form: <span
                        class="font-semibold text-primary-600">{{ $form->title }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.edit', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 transition-all">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <a href="{{ route('admin.forms.sections.create', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:shadow-soft-md transition-all">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Section</span>
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
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            @if (session('status'))
                <div
                    class="mb-6 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-success-800 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($sections->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-soft border border-primary-100 p-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-layout-text-sidebar text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">Belum ada Section</h3>
                    <p class="text-sm text-secondary-600 mb-6">Sections membantu mengorganisir pertanyaan dalam form
                        Anda</p>
                    <a href="{{ route('admin.forms.sections.create', $form) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:shadow-soft-md transition-all">
                        <i class="bi bi-plus-lg"></i>
                        <span>Buat Section Pertama</span>
                    </a>
                </div>
            @else
                {{-- DESKTOP TABLE --}}
                <div class="hidden md:block bg-white rounded-xl shadow-soft border border-primary-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-primary-100">
                            <thead class="bg-gradient-to-r from-primary-50 to-primary-100/50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        #</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Judul</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Deskripsi</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-center text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Posisi</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-center text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Pertanyaan</th>
                                    <th scope="col"
                                        class="py-3.5 pl-3 pr-6 text-right text-xs font-semibold text-secondary-900 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary-100 bg-white">
                                @foreach ($sections as $sec)
                                    <tr class="hover:bg-primary-50/50 transition-colors">
                                        <td class="py-4 pl-6 pr-3">
                                            <span
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white text-sm font-semibold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4">
                                            <a href="{{ route('admin.sections.show', $sec) }}"
                                                class="font-semibold text-secondary-900 hover:text-primary-600 transition-colors">
                                                {{ $sec->title ?? 'Tanpa judul' }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-sm text-secondary-600 line-clamp-2">
                                                {{ $sec->description ? \Illuminate\Support\Str::limit($sec->description, 120) : '-' }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            <span
                                                class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-1 text-xs font-semibold text-primary-700">
                                                {{ $sec->position }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-secondary-100 text-secondary-700 text-sm font-semibold">
                                                {{ $sec->questions()->count() }}
                                            </span>
                                        </td>
                                        <td class="py-4 pl-3 pr-6">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.sections.edit', $sec) }}"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary-100 px-3 py-1.5 text-xs font-medium text-primary-700 hover:bg-primary-200 transition-colors">
                                                    <i class="bi bi-pencil-square"></i>
                                                    <span>Edit</span>
                                                </a>

                                                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                                    <button type="button" @click="open = !open"
                                                        class="inline-flex items-center justify-center rounded-lg border border-primary-200 bg-white p-2 text-secondary-600 hover:bg-primary-50 hover:text-primary-700 transition-all">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>

                                                    <div x-show="open"
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                        class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-xl border border-primary-100 bg-white shadow-soft-lg"
                                                        style="display: none;">
                                                        <div class="p-1.5 space-y-0.5">
                                                            <a href="{{ route('admin.sections.show', $sec) }}"
                                                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                                                <i class="bi bi-eye"></i>
                                                                <span>Lihat Detail</span>
                                                            </a>

                                                            <div class="my-1 border-t border-primary-100"></div>

                                                            <form method="POST"
                                                                action="{{ route('admin.sections.destroy', $sec) }}"
                                                                onsubmit="return confirm('Yakin ingin menghapus section ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-danger-700 hover:bg-danger-50 transition-colors">
                                                                    <i class="bi bi-trash"></i>
                                                                    <span>Hapus</span>
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

                {{-- MOBILE CARDS --}}
                <div class="md:hidden space-y-4">
                    @foreach ($sections as $sec)
                        <div
                            class="bg-white rounded-xl shadow-soft border border-primary-100 p-4 hover:shadow-soft-md transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white text-sm font-semibold">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <a href="{{ route('admin.sections.show', $sec) }}"
                                            class="font-semibold text-secondary-900 hover:text-primary-600 transition-colors block">
                                            {{ $sec->title ?? 'Tanpa judul' }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700">
                                                Pos: {{ $sec->position }}
                                            </span>
                                            <span class="text-xs text-secondary-500">
                                                {{ $sec->questions()->count() }} pertanyaan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($sec->description)
                                <p class="text-sm text-secondary-600 mb-3">
                                    {{ \Illuminate\Support\Str::limit($sec->description, 120) }}
                                </p>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('admin.sections.edit', $sec) }}"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary-100 px-3 py-2 text-xs font-medium text-primary-700 hover:bg-primary-200 transition-colors">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="{{ route('admin.sections.show', $sec) }}"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-secondary-100 px-3 py-2 text-xs font-medium text-secondary-700 hover:bg-secondary-200 transition-colors">
                                    <i class="bi bi-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
