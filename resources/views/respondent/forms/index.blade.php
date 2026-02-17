<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-secondary-900 leading-tight">
                    Form Tersedia
                </h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Silakan pilih form yang ingin kamu isi.
                </p>
            </div>

            {{-- Search Desktop --}}
            <form method="GET" action="{{ route('respondent.forms.index') }}" class="hidden md:block">
                <div class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari form…"
                        class="w-64 rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm
                               placeholder:text-secondary-400
                               focus:border-primary-500 focus:outline-none
                               focus:ring-2 focus:ring-primary-200 transition" />
                    <button
                        class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-xl border border-success-200 bg-success-50 p-4 text-success-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('info'))
                <div class="mb-4 rounded-xl border border-primary-200 bg-primary-50 p-4 text-primary-800">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-primary-600 mt-0.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-soft">

                {{-- Search Mobile --}}
                <form method="GET" action="{{ route('respondent.forms.index') }}" class="mb-6 md:hidden">
                    <div class="flex items-center gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari form…"
                            class="w-full rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm
                                   placeholder:text-secondary-400
                                   focus:border-primary-500 focus:outline-none
                                   focus:ring-2 focus:ring-primary-200 transition" />
                        <button
                            class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2 text-sm text-white hover:bg-primary-700 transition">
                            Cari
                        </button>
                    </div>
                </form>

                @if ($forms->isEmpty())
                    <p class="text-secondary-600">
                        Belum ada form yang tersedia untuk diisi.
                    </p>
                @else
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        @foreach ($forms as $form)
                            @php
                                $settings = (object) ($form->settings ?? []);
                                $canEditAfterSubmit = !empty($settings->allow_edit_after_submit);
                                $limitOne = !empty($settings->limit_one_response);

                                $submittedCount = (int) ($form->my_submitted_count ?? 0);
                                $draftCount = (int) ($form->my_draft_count ?? 0);

                                $hasSubmitted = $submittedCount > 0;
                                $hasDraft = $draftCount > 0;

                                if ($hasSubmitted && !$canEditAfterSubmit && $limitOne) {
                                    $ctaLabel = 'Selesai';
                                    $ctaRoute = null;
                                } elseif ($hasDraft) {
                                    $ctaLabel = 'Lanjutkan';
                                    $ctaRoute = route('forms.section', ['form' => $form->uid, 'pos' => 1]);
                                } elseif ($hasSubmitted && $canEditAfterSubmit) {
                                    $ctaLabel = 'Edit Jawaban';
                                    $ctaRoute = route('forms.section', ['form' => $form->uid, 'pos' => 1]);
                                } else {
                                    $ctaLabel = 'Isi';
                                    $ctaRoute = route('forms.start', $form->uid);
                                }

                                $desc = \Illuminate\Support\Str::limit($form->description ?? '', 150);
                                $isPublished = (bool) ($form->is_published ?? 0);
                            @endphp

                            <div
                                class="group rounded-2xl border border-primary-100 bg-white p-6 shadow-soft transition hover:shadow-md">

                                {{-- ALERT jika sudah submit --}}
                                @if ($hasSubmitted)
                                    <div
                                        class="mb-4 rounded-xl border border-success-200 bg-success-50 p-3 text-sm text-success-800">
                                        Form ini sudah kamu isi sebelumnya.
                                    </div>
                                @endif

                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="truncate text-lg font-semibold text-secondary-900">
                                                {{ $form->title }}
                                            </h3>

                                            @if ($isPublished)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700">
                                                    Published
                                                </span>
                                            @endif

                                            @if ($limitOne)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-secondary-100 px-3 py-1 text-xs font-medium text-secondary-700">
                                                    1x respons
                                                </span>
                                            @endif

                                            @if ($canEditAfterSubmit)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-warning-100 px-3 py-1 text-xs font-medium text-warning-700">
                                                    Bisa edit
                                                </span>
                                            @endif
                                        </div>

                                        @if ($desc)
                                            <p class="mt-2 text-sm text-secondary-600 line-clamp-3">
                                                {{ $desc }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="text-right text-xs text-secondary-500">
                                        @if ($hasSubmitted)
                                            <div>Submitted: {{ $submittedCount }}</div>
                                        @endif
                                        @if ($hasDraft)
                                            <div>Draft: {{ $draftCount }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- CTA --}}
                                <div class="mt-6 flex items-center gap-3">

                                    @if ($ctaRoute)
                                        <a href="{{ $ctaRoute }}"
                                            class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                                            {{ $ctaLabel }}
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-xl bg-secondary-100 px-4 py-2 text-sm text-secondary-500 cursor-not-allowed">
                                            {{ $ctaLabel }}
                                        </span>
                                    @endif

                                    <a href="{{ route('forms.start', $form->uid) }}"
                                        class="inline-flex items-center rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 transition">
                                        Lihat
                                    </a>

                                </div>
                            </div>
                        @endforeach

                    </div>

                    @if (method_exists($forms, 'links'))
                        <div class="mt-8">
                            {{ $forms->links() }}
                        </div>
                    @endif

                @endif

            </div>
        </div>
    </div>
</x-app-layout>
