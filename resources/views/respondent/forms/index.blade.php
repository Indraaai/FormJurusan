<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-emerald-900">
                Form Tersedia
            </h2>

            {{-- Search (desktop) --}}
            <form method="GET" action="{{ route('respondent.forms.index') }}" class="hidden md:block">
                <div class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari form…"
                        class="w-64 rounded-xl border border-emerald-200 px-3 py-2 text-sm placeholder:text-emerald-900/40 focus-visible:ring-2 focus-visible:ring-emerald-300" />
                    <button
                        class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50/70 p-3 text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                {{-- Search (mobile) --}}
                <form method="GET" action="{{ route('respondent.forms.index') }}" class="mb-4 md:hidden">
                    <div class="flex items-center gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari form…"
                            class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-sm placeholder:text-emerald-900/40 focus-visible:ring-2 focus-visible:ring-emerald-300" />
                        <button
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                            Cari
                        </button>
                    </div>
                </form>

                @if ($forms->isEmpty())
                    <p class="text-emerald-800/70">Belum ada form yang tersedia untuk diisi.</p>
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

                                // window aktif (opsional) — terima string atau Carbon
                                $startRaw = $settings->start_at ?? null;
                                $endRaw = $settings->end_at ?? null;
                                $startAt = $startRaw ? \Carbon\Carbon::parse($startRaw) : null;
                                $endAt = $endRaw ? \Carbon\Carbon::parse($endRaw) : null;

                                $isPublished = (bool) ($form->is_published ?? 0);
                            @endphp

                            <div
                                class="group rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="truncate text-lg font-semibold text-emerald-900">
                                                {{ $form->title }}</h3>
                                            @if ($isPublished)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">Published</span>
                                            @endif
                                            @if ($limitOne)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">1x
                                                    respons</span>
                                            @endif
                                            @if ($canEditAfterSubmit)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-800">Bisa
                                                    edit</span>
                                            @endif
                                        </div>

                                        @if ($desc)
                                            <p class="mt-1 line-clamp-3 text-sm text-emerald-800/80">{{ $desc }}
                                            </p>
                                        @endif

                                        @if ($startAt || $endAt)
                                            <div class="mt-2 text-xs text-emerald-700/70">
                                                @if ($startAt)
                                                    Mulai: {{ $startAt->format('Y-m-d H:i') }}
                                                @endif
                                                @if ($startAt && $endAt)
                                                    •
                                                @endif
                                                @if ($endAt)
                                                    Tutup: {{ $endAt->format('Y-m-d H:i') }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-right">
                                        @if ($hasSubmitted)
                                            <div class="text-xs text-emerald-700/80">Submitted: {{ $submittedCount }}
                                            </div>
                                        @endif
                                        @if ($hasDraft)
                                            <div class="text-xs text-emerald-700/80">Draft: {{ $draftCount }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-2">
                                    @if ($ctaRoute)
                                        <a href="{{ $ctaRoute }}"
                                            class="inline-flex items-center rounded-xl bg-emerald-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                                            {{ $ctaLabel }}
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-xl bg-emerald-50 px-3 py-2 text-sm text-emerald-400 ring-1 ring-emerald-100 cursor-not-allowed">
                                            {{ $ctaLabel }}
                                        </span>
                                    @endif

                                    <a href="{{ route('forms.start', $form->uid) }}"
                                        class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm text-emerald-800 hover:bg-emerald-50">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- tampilkan pagination hanya jika tersedia --}}
                    @if (method_exists($forms, 'links'))
                        <div class="mt-6">
                            {{ $forms->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
