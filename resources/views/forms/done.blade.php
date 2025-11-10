<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-emerald-900">
            Terima kasih!
        </h2>
    </x-slot>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .print-card {
                box-shadow: none !important;
                border-color: #d1fae5 !important;
            }
        }
    </style>

    @php
        // Jadikan settings aman (array -> object)
        $s = (object) ($form->settings ?? []);
        $confirmMsg = $s->confirmation_message ?? null;

        $sentAt = optional($response->submitted_at ?? null)->format('Y-m-d H:i:s');
        $respEmail = $response->respondent_email ?? null;

        $canEditAfterSubmit = (bool) ($s->allow_edit_after_submit ?? false);
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
            <div class="print-card rounded-2xl border border-emerald-100 bg-white p-6 text-center shadow-sm">

                {{-- Ikon sukses --}}
                <div
                    class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0L3.293 9.957a1 1 0 111.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-emerald-900">Respons kamu sudah terkirim.</h3>

                <p class="mt-2 text-emerald-800/80">
                    {{ $confirmMsg ?: 'Kamu dapat menutup halaman ini atau kembali ke dashboard.' }}
                </p>

                {{-- Ringkasan respons (opsional) --}}
                @if (!empty($response))
                    <div class="mx-auto mt-6 inline-block text-left">
                        <div
                            class="rounded-xl border border-emerald-100 bg-emerald-50/40 px-4 py-3 text-sm text-emerald-900">
                            <div><span class="text-emerald-700/70">Form:</span> <strong>{{ $form->title }}</strong>
                            </div>
                            <div class="mt-0.5"><span class="text-emerald-700/70">Response UID:</span> <code
                                    class="text-xs">{{ $response->uid }}</code></div>
                            <div class="mt-0.5"><span class="text-emerald-700/70">Waktu kirim:</span>
                                {{ $sentAt ?? '-' }}</div>
                            @if ($respEmail)
                                <div class="mt-0.5"><span class="text-emerald-700/70">Email:</span> {{ $respEmail }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Tombol aksi --}}
                <div class="no-print mt-6 space-y-2">
                    @if ($canEditAfterSubmit)
                        <div>
                            <a href="{{ route('forms.section', ['form' => $form->uid, 'pos' => 1]) }}"
                                class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                                Edit Jawaban
                            </a>
                        </div>
                    @endif

                    @if (Route::has('forms.review'))
                        <div>
                            <a href="{{ route('forms.review', $form->uid) }}"
                                class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-sm text-emerald-800 hover:bg-emerald-50">
                                Lihat Ringkasan
                            </a>
                        </div>
                    @endif

                    <div class="mt-2 flex items-center justify-center gap-2">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-sm text-emerald-800 hover:bg-emerald-50">
                            Kembali ke Dashboard
                        </a>
                        <button onclick="window.print()"
                            class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm text-white shadow-sm hover:bg-emerald-700">
                            Cetak
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
