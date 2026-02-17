{{-- resources/views/forms/start.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-secondary-900 leading-tight">
                {{ $form->title ?? 'Form' }}
            </h2>
            <p class="text-sm text-secondary-600 mt-1">
                Silakan baca informasi di bawah sebelum memulai.
            </p>
        </div>
    </x-slot>

    @php
        $s = (object) ($form->settings ?? []);
        $startAt = !empty($s->start_at) ? \Carbon\Carbon::parse($s->start_at) : null;
        $endAt = !empty($s->end_at) ? \Carbon\Carbon::parse($s->end_at) : null;

        $allowEditAfterSubmit = (bool) ($s->allow_edit_after_submit ?? false);
        $limitOne = (bool) ($s->limit_one_response ?? false);

        $desc = trim((string) ($form->description ?? ''));
    @endphp

    <div class="py-10">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-xl border border-success-200 bg-success-50 p-4 text-success-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-primary-100 bg-white p-8 shadow-soft">

                {{-- DESKRIPSI --}}
                <div class="prose max-w-none text-secondary-800 prose-p:my-3">
                    {{ $desc !== '' ? $desc : 'Form ini siap untuk kamu isi. Klik tombol mulai untuk melanjutkan.' }}
                </div>

                {{-- WINDOW AKTIF --}}
                @if ($startAt || $endAt)
                    <div
                        class="mt-5 rounded-xl bg-secondary-50 border border-secondary-200 p-4 text-sm text-secondary-700">
                        <div class="font-medium mb-1">Waktu Aktif</div>
                        <div>
                            @if ($startAt)
                                Mulai: <strong>{{ $startAt->format('Y-m-d H:i') }}</strong>
                            @endif
                            @if ($startAt && $endAt)
                                •
                            @endif
                            @if ($endAt)
                                Tutup: <strong>{{ $endAt->format('Y-m-d H:i') }}</strong>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="my-8 border-t border-secondary-200"></div>

                {{-- STATUS & CTA --}}
                @if (!empty($submitted) && $submitted->exists)

                    <div class="mb-6 rounded-xl border border-success-200 bg-success-50 p-4">
                        <div class="text-sm text-success-800">
                            Kamu sudah mengirim respons pada
                            <strong>{{ optional($submitted->submitted_at)->format('Y-m-d H:i') ?? '-' }}</strong>.
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">

                        @if ($allowEditAfterSubmit)
                            <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => 1]) }}"
                                class="inline-flex items-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition">
                                Edit Jawaban
                            </a>
                        @elseif($limitOne)
                            <a href="{{ route('forms.done', $form->uid) }}"
                                class="inline-flex items-center rounded-xl border border-secondary-300 bg-white px-5 py-2.5 text-sm font-medium text-secondary-700 hover:bg-secondary-50 transition">
                                Lihat Konfirmasi
                            </a>
                        @else
                            <form method="POST" action="{{ route('forms.begin', $form) }}">
                                @csrf
                                <button
                                    class="inline-flex items-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition">
                                    Isi Lagi
                                </button>
                            </form>
                        @endif

                    </div>
                @elseif (!empty($draft) && $draft->exists)
                    <div class="mb-6 rounded-xl border border-warning-200 bg-warning-50 p-4">
                        <div class="text-sm text-warning-800">
                            Kamu memiliki draft sejak
                            <strong>{{ optional($draft->started_at)->format('Y-m-d H:i') ?? '-' }}</strong>.
                        </div>
                    </div>

                    <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => 1]) }}"
                        class="inline-flex items-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition">
                        Lanjutkan
                    </a>
                @else
                    <form method="POST" action="{{ route('forms.begin', $form) }}">
                        @csrf
                        <button
                            class="inline-flex items-center rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition">
                            Mulai
                        </button>
                    </form>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>
