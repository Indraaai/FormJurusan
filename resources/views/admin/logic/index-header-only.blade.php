<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">Logic Rules</h2>
                <p class="text-sm text-secondary-600 mt-1">Form: <span
                        class="font-semibold text-primary-600">{{ $form->title }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.questions.index', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 transition-all">
                    <i class="bi bi-question-circle"></i>
                    <span>Pertanyaan</span>
                </a>
                <a href="{{ route('admin.forms.logic.create', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:shadow-soft-md transition-all">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Rule</span>
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
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div
                    class="rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-success-800 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-soft border border-primary-100" x-data="rulesList()"
                x-init="init()">
                <!-- Toolbar filter/sort -->
                <div class="border-b border-primary-100 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="relative">
                            <input type="text" x-model="q" @input="apply()"
                                placeholder="Cari sumber/target/nilai..."
                                class="w-full rounded-lg border border-primary-200 bg-white pl-10 pr-4 py-2.5 text-secondary-900 placeholder:text-secondary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <i class="bi bi-search absolute left-3 top-3 text-secondary-400"></i>
                        </div>

                        <select x-model="status" @change="apply()"
                            class="w-full rounded-lg border border-primary-200 bg-white px-4 py-2.5 text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <option value="">Status: Semua</option>
                            <option value="enabled">Enabled</option>
                            <option value="disabled">Disabled</option>
                        </select>

                        <select x-model="operator" @change="apply()"
                            class="w-full rounded-lg border border-primary-200 bg-white px-4 py-2.5 text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <option value="">Operator: Semua</option>
                            @foreach (['=', '!=', '>', '<', '>=', '<=', 'contains', 'in', 'answered', 'not_answered'] as $op)
                                <option value="{{ $op }}">{{ $op }}</option>
                            @endforeach
                        </select>

                        <select x-model="sort" @change="apply()"
                            class="w-full rounded-lg border border-primary-200 bg-white px-4 py-2.5 text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <option value="priority_asc">Sort: Prioritas ↑</option>
                            <option value="priority_desc">Sort: Prioritas ↓</option>
                            <option value="status">Sort: Status</option>
                        </select>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-sm text-secondary-600">
                            <span x-text="displayedCount"></span> dari <span x-text="total"></span> rules
                        </p>
                        <button type="button" @click="resetAll()"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                            <i class="bi bi-arrow-clockwise"></i>
                            Reset Filter
                        </button>
                    </div>
                </div>

                <!-- Content continues from original file... -->
                <!-- Keep the rest of the original logic implementation -->
                @include('admin.logic._original_content')
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Include original Alpine.js logic here --}}
    @endpush
</x-app-layout>
