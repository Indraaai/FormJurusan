<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">
                    Pertanyaan
                </h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Form:
                    <span class="font-semibold text-primary-600">
                        {{ $form->title }}
                    </span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.sections.index', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white
                           px-4 py-2 text-sm font-medium text-secondary-700
                           hover:bg-secondary-50 transition">
                    <i class="bi bi-layout-text-sidebar"></i>
                    <span>Sections</span>
                </a>

                <a href="{{ route('admin.forms.questions.create', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600
                           px-4 py-2 text-sm font-semibold text-white
                           shadow-sm hover:bg-primary-700 transition">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Pertanyaan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-8">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">

            @forelse($form->sections as $section)

                {{-- SECTION CARD --}}
                <div class="bg-white rounded-xl shadow-soft border border-primary-100 overflow-hidden">

                    {{-- SECTION HEADER --}}
                    <div class="bg-primary-50 px-6 py-4 border-b border-primary-100 flex items-center justify-between">

                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8
                                       rounded-lg bg-primary-600 text-white
                                       text-sm font-semibold">
                                {{ $section->position }}
                            </span>

                            <div>
                                <h3 class="font-semibold text-secondary-900">
                                    {{ $section->title ?? 'Tanpa judul' }}
                                </h3>

                                @if ($section->description)
                                    <p class="text-sm text-secondary-600 mt-0.5">
                                        {{ $section->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('admin.sections.show', $section) }}"
                            class="inline-flex items-center gap-1.5 text-sm
                                   font-medium text-primary-700
                                   hover:text-primary-800 transition">
                            <span>Lihat Section</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    {{-- QUESTIONS --}}
                    <div class="p-6">

                        @if ($section->questions->isEmpty())
                            {{-- EMPTY QUESTIONS --}}
                            <div class="text-center py-10">
                                <div
                                    class="mx-auto w-14 h-14 bg-primary-100 rounded-full
                                           flex items-center justify-center mb-3">
                                    <i class="bi bi-question-circle text-2xl text-primary-600"></i>
                                </div>

                                <p class="text-sm text-secondary-600">
                                    Belum ada pertanyaan di section ini
                                </p>
                            </div>
                        @else
                            {{-- DESKTOP TABLE --}}
                            <div class="hidden md:block">
                                <div class="overflow-x-auto rounded-xl border border-primary-100">

                                    <table class="min-w-full divide-y divide-primary-100">

                                        <thead class="bg-primary-50">
                                            <tr>
                                                <th
                                                    class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold
                                                           text-secondary-900 uppercase tracking-wider">
                                                    Pos
                                                </th>

                                                <th
                                                    class="px-3 py-3.5 text-left text-xs font-semibold
                                                           text-secondary-900 uppercase tracking-wider">
                                                    Tipe
                                                </th>

                                                <th
                                                    class="px-3 py-3.5 text-left text-xs font-semibold
                                                           text-secondary-900 uppercase tracking-wider">
                                                    Pertanyaan
                                                </th>

                                                <th
                                                    class="py-3.5 pl-3 pr-6 text-right text-xs font-semibold
                                                           text-secondary-900 uppercase tracking-wider">
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-primary-100 bg-white">

                                            @foreach ($section->questions as $q)
                                                <tr class="hover:bg-primary-50/50 transition-colors">

                                                    <td class="py-4 pl-6 pr-3">
                                                        <span
                                                            class="inline-flex items-center justify-center
                                                                   w-8 h-8 rounded-lg
                                                                   bg-primary-100 text-primary-700
                                                                   text-sm font-semibold">
                                                            {{ $q->position }}
                                                        </span>
                                                    </td>

                                                    <td class="px-3 py-4">
                                                        <span
                                                            class="inline-flex items-center rounded-full
                                                                   bg-secondary-100
                                                                   px-2.5 py-1
                                                                   text-xs font-medium text-secondary-700">
                                                            {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                                        </span>
                                                    </td>

                                                    <td class="px-3 py-4">
                                                        <a href="{{ route('admin.questions.show', $q) }}"
                                                            class="font-medium text-secondary-900
                                                                   hover:text-primary-600
                                                                   transition-colors line-clamp-2">
                                                            {{ $q->title }}
                                                        </a>

                                                        @if ($q->is_required)
                                                            <div
                                                                class="mt-1 inline-flex items-center gap-1
                                                                       text-xs text-danger-600">
                                                                <i class="bi bi-asterisk"></i>
                                                                Required
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td class="py-4 pl-3 pr-6">
                                                        <div class="flex items-center justify-end gap-2">

                                                            <a href="{{ route('admin.questions.edit', $q) }}"
                                                                class="inline-flex items-center gap-1.5
                                                                       rounded-lg bg-primary-100
                                                                       px-3 py-1.5
                                                                       text-xs font-medium text-primary-700
                                                                       hover:bg-primary-200 transition">
                                                                <i class="bi bi-pencil-square"></i>
                                                                <span>Edit</span>
                                                            </a>

                                                            <div class="relative" x-data="{ open: false }"
                                                                @click.away="open = false">

                                                                <button type="button" @click="open = !open"
                                                                    class="inline-flex items-center justify-center
                                                                           rounded-lg border border-primary-200
                                                                           bg-white p-2
                                                                           text-secondary-600
                                                                           hover:bg-primary-50
                                                                           hover:text-primary-700 transition">

                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>

                                                                <div x-show="open" x-transition
                                                                    class="absolute right-0 z-50 mt-2 w-48
                                                                           origin-top-right rounded-xl
                                                                           border border-primary-100
                                                                           bg-white shadow-soft-lg"
                                                                    style="display:none">

                                                                    <div class="p-1.5 space-y-0.5">

                                                                        <a href="{{ route('admin.questions.show', $q) }}"
                                                                            class="flex items-center gap-3
                                                                                   rounded-lg px-3 py-2
                                                                                   text-sm text-secondary-700
                                                                                   hover:bg-primary-50
                                                                                   hover:text-primary-700">

                                                                            <i class="bi bi-eye"></i>
                                                                            <span>Lihat Detail</span>
                                                                        </a>

                                                                        <div
                                                                            class="my-1 border-t
                                                                                   border-primary-100">
                                                                        </div>

                                                                        <form method="POST"
                                                                            action="{{ route('admin.questions.destroy', $q) }}"
                                                                            onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?');">

                                                                            @csrf
                                                                            @method('DELETE')

                                                                            <button type="submit"
                                                                                class="flex w-full items-center gap-3
                                                                                       rounded-lg px-3 py-2
                                                                                       text-sm text-danger-700
                                                                                       hover:bg-danger-50 transition">

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

                                    {{-- FOOTER COUNT --}}
                                    <div
                                        class="flex items-center justify-between px-6 py-4
                                               border-t border-primary-100
                                               bg-primary-50/50">

                                        <span class="text-sm text-secondary-700">
                                            Total
                                            <span class="font-semibold text-secondary-900">
                                                {{ $section->questions->count() }}
                                            </span>
                                            Pertanyaan
                                        </span>
                                    </div>

                                </div>
                            </div>

                            {{-- MOBILE --}}
                            <div class="md:hidden space-y-4">

                                @foreach ($section->questions as $q)
                                    <div
                                        class="bg-white rounded-xl border border-primary-100
                                               p-4 shadow-soft">

                                        <div class="flex items-start justify-between mb-3">

                                            <div class="flex items-center gap-2">

                                                <span
                                                    class="inline-flex items-center justify-center
                                                           w-8 h-8 rounded-lg
                                                           bg-primary-100 text-primary-700
                                                           text-sm font-semibold">
                                                    {{ $q->position }}
                                                </span>

                                                <span
                                                    class="inline-flex items-center rounded-full
                                                           bg-secondary-100
                                                           px-2.5 py-1
                                                           text-xs font-medium text-secondary-700">
                                                    {{ strtoupper(str_replace('_', ' ', $q->type)) }}
                                                </span>
                                            </div>
                                        </div>

                                        <a href="{{ route('admin.questions.show', $q) }}"
                                            class="block mb-2 text-sm font-medium
                                                   text-secondary-900
                                                   hover:text-primary-600">
                                            {{ $q->title }}
                                        </a>

                                        @if ($q->is_required)
                                            <div
                                                class="mb-2 inline-flex items-center gap-1
                                                       text-xs text-danger-600">
                                                <i class="bi bi-asterisk"></i>
                                                Required
                                            </div>
                                        @endif

                                        <div
                                            class="flex gap-2 pt-3
                                                   border-t border-primary-100">

                                            <a href="{{ route('admin.questions.edit', $q) }}"
                                                class="flex-1 inline-flex items-center justify-center
                                                       gap-1.5 rounded-lg bg-primary-100
                                                       px-3 py-2 text-xs font-medium
                                                       text-primary-700 hover:bg-primary-200">

                                                <i class="bi bi-pencil-square"></i>
                                                <span>Edit</span>
                                            </a>

                                            <a href="{{ route('admin.questions.show', $q) }}"
                                                class="flex-1 inline-flex items-center justify-center
                                                       gap-1.5 rounded-lg bg-secondary-100
                                                       px-3 py-2 text-xs font-medium
                                                       text-secondary-700 hover:bg-secondary-200">

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

            @empty

                {{-- EMPTY STATE GLOBAL --}}
                <div
                    class="bg-white rounded-xl shadow-soft
                           border border-primary-100
                           p-12 text-center">

                    <div
                        class="mx-auto w-16 h-16 bg-primary-100 rounded-full
                               flex items-center justify-center mb-4">

                        <i
                            class="bi bi-layout-text-sidebar
                                   text-3xl text-primary-600"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-secondary-900 mb-2">
                        Belum ada Section
                    </h3>

                    <p class="text-sm text-secondary-600 mb-6">
                        Buat section terlebih dahulu sebelum menambahkan pertanyaan
                    </p>

                    <a href="{{ route('admin.forms.sections.create', $form) }}"
                        class="inline-flex items-center gap-2
                               rounded-lg bg-primary-600
                               px-5 py-2.5 text-sm
                               font-semibold text-white
                               hover:bg-primary-700 transition">

                        <i class="bi bi-plus-lg"></i>
                        <span>Buat Section</span>
                    </a>
                </div>

            @endforelse

        </div>
    </div>
</x-app-layout>
