{{-- resources/views/forms/start.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-emerald-900">
            {{ $form->title ?? 'Form' }}
        </h2>
    </x-slot>

    @php
        // Settings aman (array -> object)
        $s = (object) ($form->settings ?? []);
        $startAt = !empty($s->start_at) ? \Carbon\Carbon::parse($s->start_at) : null;
        $endAt = !empty($s->end_at) ? \Carbon\Carbon::parse($s->end_at) : null;

        $allowEditAfterSubmit = (bool) ($s->allow_edit_after_submit ?? false);
        $limitOne = (bool) ($s->limit_one_response ?? false);

        $desc = trim((string) ($form->description ?? ''));
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-3 text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                {{-- Deskripsi --}}
                <div class="prose max-w-none text-emerald-900 prose-p:my-2">
                    {{ $desc !== '' ? $desc : 'Deskripsi form akan tampil di sini.' }}
                </div>

                {{-- Window aktif (opsional) --}}
                @if ($startAt || $endAt)
                    <div class="mt-3 text-xs text-emerald-700/70">
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

                <hr class="my-6">

                {{-- Status + CTA --}}
                @if (!empty($submitted) && $submitted->exists)
                    <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50/40 p-3">
                        <div class="text-sm text-emerald-900">
                            Kamu sudah mengirim respons pada
                            <strong>{{ optional($submitted->submitted_at)->format('Y-m-d H:i') ?? '-' }}</strong>.
                        </div>
                    </div>

                    @if ($allowEditAfterSubmit)
                        {{-- Izinkan edit setelah submit --}}
                        <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => 1]) }}"
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                            Edit Jawaban
                        </a>
                    @elseif($limitOne)
                        {{-- Tidak boleh isi lagi --}}
                        <a href="{{ route('forms.done', $form->uid) }}"
                            class="inline-flex items-center rounded-xl bg-emerald-50 px-4 py-2 text-sm text-emerald-600 ring-1 ring-emerald-100">
                            Lihat Konfirmasi
                        </a>
                    @else
                        {{-- Boleh isi lagi (kebijakan fleksibel) --}}
                        <form method="POST" action="{{ route('forms.begin', $form) }}" class="inline-block">
                            @csrf
                            <button
                                class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                                Isi Lagi
                            </button>
                        </form>
                    @endif
                @elseif (!empty($draft) && $draft->exists)
                    {{-- Ada draft --}}
                    <div class="mb-3 text-sm text-emerald-700/80">
                        Kamu punya draft sejak {{ optional($draft->started_at)->format('Y-m-d H:i') ?? '-' }}.
                    </div>
                    <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => 1]) }}"
                        class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                        Lanjutkan
                    </a>
                @else
                    {{-- Belum pernah isi: mulai baru --}}
                    <form method="POST" action="{{ route('forms.begin', $form) }}">
                        @csrf
                        <button
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                            Mulai
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
