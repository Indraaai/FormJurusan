<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl leading-tight text-gray-900">
                    Pengaturan Form
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $form->title ?? 'Form' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.forms.edit', $form) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Edit
                </a>
                <a href="{{ route('admin.forms.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Daftar Form
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }

        /* Custom toggle switch styling */
        .toggle-checkbox:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .toggle-checkbox:checked+.toggle-label::after {
            transform: translateX(1.25rem);
        }
    </style>

    @php
        $startVal = old('start_at', optional($settings->start_at)->format('Y-m-d\TH:i'));
        $endVal = old('end_at', optional($settings->end_at)->format('Y-m-d\TH:i'));
        $themeStr = old(
            'theme',
            !empty($settings->theme) ? json_encode($settings->theme, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '',
        );
    @endphp

    <div class="py-6 sm:py-8" x-data="formSettings({
        requireSignIn: {{ (int) old('require_sign_in', (int) ($settings->require_sign_in ?? 1)) }},
        collectEmails: {{ (int) old('collect_emails', (int) ($settings->collect_emails ?? 1)) }},
        limitOne: {{ (int) old('limit_one_response', (int) ($settings->limit_one_response ?? 0)) }},
        allowEdit: {{ (int) old('allow_edit_after_submit', (int) ($settings->allow_edit_after_submit ?? 0)) }},
        showProgress: {{ (int) old('show_progress_bar', (int) ($settings->show_progress_bar ?? 1)) }},
        shuffleQ: {{ (int) old('shuffle_question_order', (int) ($settings->shuffle_question_order ?? 0)) }},
        sendReceipt: {{ (int) old('response_receipt_enabled', (int) ($settings->response_receipt_enabled ?? 0)) }},
        captcha: {{ (int) old('captcha_enabled', (int) ($settings->captcha_enabled ?? 0)) }},
        startAt: @js($startVal),
        endAt: @js($endVal),
        themeText: @js($themeStr),
    })" x-init="init()">

        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="rounded-lg border-l-4 border-emerald-500 bg-emerald-50 p-4 shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-emerald-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-emerald-800">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 shadow-sm">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 mb-2">Periksa kembali input kamu:</h3>
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form id="settings-form" method="POST" action="{{ route('admin.forms.settings.update', $form) }}"
                class="space-y-6">
                @csrf
                @method('PUT')
                @csrf
                @method('PUT')

                {{-- Section 1: Akses & Identitas --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Akses & Identitas</h3>
                                <p class="text-sm text-gray-600">Kontrol siapa yang bisa mengisi form</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Require Sign In --}}
                            <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                                :class="requireSignIn ? 'border-emerald-300 bg-emerald-50/50' :
                                    'border-gray-200 bg-white hover:border-gray-300'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                            :class="requireSignIn ? 'bg-emerald-600' : 'bg-gray-200'">
                                            <svg class="w-5 h-5" :class="requireSignIn ? 'text-white' : 'text-gray-500'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900 mb-1">Wajib Login</div>
                                            <p class="text-sm text-gray-600">Responden harus login untuk mengisi form
                                            </p>
                                            <p class="text-xs text-emerald-700 mt-1 font-medium">✓ Direkomendasikan
                                                untuk kontrol identitas</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="require_sign_in" value="0">
                                        <input type="checkbox" name="require_sign_in" value="1"
                                            x-model="requireSignIn" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Collect Emails --}}
                            <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                                :class="collectEmails ? 'border-emerald-300 bg-emerald-50/50' :
                                    'border-gray-200 bg-white hover:border-gray-300'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                            :class="collectEmails ? 'bg-emerald-600' : 'bg-gray-200'">
                                            <svg class="w-5 h-5" :class="collectEmails ? 'text-white' : 'text-gray-500'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900 mb-1">Kumpulkan Email</div>
                                            <p class="text-sm text-gray-600">Simpan email responden saat submit</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="collect_emails" value="0">
                                        <input type="checkbox" name="collect_emails" value="1"
                                            x-model="collectEmails" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Limit One Response --}}
                            <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                                :class="!requireSignIn ? 'opacity-50 cursor-not-allowed' : (limitOne ?
                                    'border-emerald-300 bg-emerald-50/50' :
                                    'border-gray-200 bg-white hover:border-gray-300')">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                            :class="limitOne && requireSignIn ? 'bg-emerald-600' : 'bg-gray-200'">
                                            <svg class="w-5 h-5"
                                                :class="limitOne && requireSignIn ? 'text-white' : 'text-gray-500'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900 mb-1">Batasi 1 Respons per User
                                            </div>
                                            <p class="text-sm text-gray-600">Setiap user hanya bisa submit sekali</p>
                                            <p class="text-xs text-amber-700 mt-1 font-medium"
                                                x-show="!requireSignIn">⚠ Memerlukan "Wajib Login" aktif</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="limit_one_response" value="0">
                                        <input type="checkbox" name="limit_one_response" value="1"
                                            x-model="limitOne" :disabled="!requireSignIn" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Allow Edit After Submit --}}
                            <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                                :class="allowEdit ? 'border-emerald-300 bg-emerald-50/50' :
                                    'border-gray-200 bg-white hover:border-gray-300'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                            :class="allowEdit ? 'bg-emerald-600' : 'bg-gray-200'">
                                            <svg class="w-5 h-5" :class="allowEdit ? 'text-white' : 'text-gray-500'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900 mb-1">Boleh Edit Setelah Submit
                                            </div>
                                            <p class="text-sm text-gray-600">Responden dapat memperbarui jawaban</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="allow_edit_after_submit" value="0">
                                        <input type="checkbox" name="allow_edit_after_submit" value="1"
                                            x-model="allowEdit" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>

    {{-- Section 2: Pengalaman Pengguna --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Pengalaman Pengguna</h3>
                    <p class="text-sm text-gray-600">Tingkatkan kenyamanan mengisi form</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Show Progress Bar --}}
                <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                    :class="showProgress ? 'border-emerald-300 bg-emerald-50/50' :
                        'border-gray-200 bg-white hover:border-gray-300'">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="showProgress ? 'bg-emerald-600' : 'bg-gray-200'">
                                <svg class="w-5 h-5" :class="showProgress ? 'text-white' : 'text-gray-500'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 mb-1">Tampilkan Progress Bar</div>
                                <p class="text-sm text-gray-600">Indikator progres per section</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="show_progress_bar" value="0">
                            <input type="checkbox" name="show_progress_bar" value="1" x-model="showProgress"
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Shuffle Question Order --}}
                <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                    :class="shuffleQ ? 'border-emerald-300 bg-emerald-50/50' : 'border-gray-200 bg-white hover:border-gray-300'">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="shuffleQ ? 'bg-emerald-600' : 'bg-gray-200'">
                                <svg class="w-5 h-5" :class="shuffleQ ? 'text-white' : 'text-gray-500'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 mb-1">Acak Urutan Pertanyaan</div>
                                <p class="text-sm text-gray-600">Random untuk setiap sesi pengisian</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="shuffle_question_order" value="0">
                            <input type="checkbox" name="shuffle_question_order" value="1" x-model="shuffleQ"
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 3: Keamanan & Notifikasi --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Keamanan & Notifikasi</h3>
                    <p class="text-sm text-gray-600">Lindungi form dari spam dan atur notifikasi</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Response Receipt --}}
                <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                    :class="!collectEmails ? 'opacity-50 cursor-not-allowed' : (sendReceipt ?
                        'border-emerald-300 bg-emerald-50/50' : 'border-gray-200 bg-white hover:border-gray-300')">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="sendReceipt && collectEmails ? 'bg-emerald-600' : 'bg-gray-200'">
                                <svg class="w-5 h-5"
                                    :class="sendReceipt && collectEmails ? 'text-white' : 'text-gray-500'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 mb-1">Kirim Bukti Respons (Email)</div>
                                <p class="text-sm text-gray-600">Email konfirmasi ke responden</p>
                                <p class="text-xs text-amber-700 mt-1 font-medium" x-show="!collectEmails">⚠
                                    Memerlukan "Kumpulkan Email" aktif</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="response_receipt_enabled" value="0">
                            <input type="checkbox" name="response_receipt_enabled" value="1"
                                x-model="sendReceipt" :disabled="!collectEmails" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Captcha --}}
                <div class="relative rounded-lg border-2 p-4 transition-all duration-200"
                    :class="captcha ? 'border-emerald-300 bg-emerald-50/50' : 'border-gray-200 bg-white hover:border-gray-300'">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="captcha ? 'bg-emerald-600' : 'bg-gray-200'">
                                <svg class="w-5 h-5" :class="captcha ? 'text-white' : 'text-gray-500'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 mb-1">Aktifkan Captcha</div>
                                <p class="text-sm text-gray-600">Cegah spam dan penyalahgunaan</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="captcha_enabled" value="0">
                            <input type="checkbox" name="captcha_enabled" value="1" x-model="captcha"
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600">
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 4: Periode Aktif --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-teal-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Periode Aktif</h3>
                    <p class="text-sm text-gray-600">Tentukan kapan form dapat diisi</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Mulai Aktif (Opsional)
                        </div>
                    </label>
                    <input type="datetime-local" name="start_at" x-model="startAt"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors duration-200 px-4 py-2.5">
                    @error('start_at')
                        <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Selesai Aktif (Opsional)
                        </div>
                    </label>
                    <input type="datetime-local" name="end_at" x-model="endAt"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors duration-200 px-4 py-2.5">
                    @error('end_at')
                        <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 rounded-lg border-l-4 border-teal-500 bg-teal-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-teal-900 mb-1">Ringkasan Periode:</p>
                        <p class="text-sm text-teal-800" x-text="periodSummary()"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 5: Pesan & Tema --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Pesan & Tampilan</h3>
                    <p class="text-sm text-gray-600">Kustomisasi pesan dan tema form</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Confirmation Message --}}
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pesan Konfirmasi
                    </div>
                </label>
                <textarea name="confirmation_message" rows="3" maxlength="500"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-colors duration-200 px-4 py-3"
                    placeholder="Terima kasih, respons kamu sudah kami terima.">{{ old('confirmation_message', $settings->confirmation_message ?? '') }}</textarea>
                @error('confirmation_message')
                    <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
                <p class="mt-2 flex items-start gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Maksimal 500 karakter. Pesan ini akan ditampilkan setelah responden submit form.</span>
                </p>
            </div>

            {{-- Theme JSON --}}
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                        Tema (JSON, Opsional)
                    </div>
                </label>
                <textarea name="theme" rows="5" x-model="themeText" @input="validateTheme()"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-colors duration-200 px-4 py-3 font-mono text-sm"
                    placeholder='{"primary":"#10b981","accent":"#047857"}'></textarea>
                @error('theme')
                    <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-medium"
                        :class="themeValid ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' :
                            'bg-red-100 text-red-800 border border-red-200'">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" x-show="themeValid">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" x-show="!themeValid">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span x-text="themeValid ? 'JSON Valid' : 'JSON Tidak Valid'"></span>
                    </span>

                    <template x-if="themePreview.primary || themePreview.accent">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2" x-show="themePreview.primary">
                                <span class="text-xs font-medium text-gray-600">Primary:</span>
                                <span class="inline-block h-6 w-12 rounded border border-gray-300 shadow-sm"
                                    :style="`background:${themePreview.primary}`"></span>
                                <span class="text-xs font-mono text-gray-500" x-text="themePreview.primary"></span>
                            </div>
                            <div class="flex items-center gap-2" x-show="themePreview.accent">
                                <span class="text-xs font-medium text-gray-600">Accent:</span>
                                <span class="inline-block h-6 w-12 rounded border border-gray-300 shadow-sm"
                                    :style="`background:${themePreview.accent}`"></span>
                                <span class="text-xs font-mono text-gray-500" x-text="themePreview.accent"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <p class="mt-2 flex items-start gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Biarkan kosong jika tidak diperlukan. Pastikan format JSON valid dengan property "primary"
                        dan/atau "accent".</span>
                </p>
            </div>
        </div>
    </div>

    </div>
    </div>

    {{-- Info & Note --}}
    <div class="bg-blue-50 rounded-lg border-l-4 border-blue-500 p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-900 mb-1">Catatan Penting</p>
                <p class="text-sm text-blue-800">
                    Untuk mempublikasikan atau menyembunyikan form, gunakan tombol Publish/Unpublish di halaman
                    <a href="{{ route('admin.forms.edit', $form) }}"
                        class="underline font-medium hover:text-blue-900">Edit Form</a>.
                </p>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky bottom-0 z-10">
        <div class="bg-gray-50 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <a href="{{ route('admin.forms.edit', $form) }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-emerald-600 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </div>
    </form>

    {{-- Form Link Info --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Tautan Form Publik</h3>
                    <p class="text-sm text-gray-600">Share link ini kepada responden</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Form UID</span>
                    </div>
                    <code
                        class="inline-block px-3 py-2 rounded-lg bg-gray-100 text-sm font-mono text-gray-900 border border-gray-200">{{ $form->uid }}</code>
                </div>
                @if (isset($form->uid))
                    <a href="{{ url('/forms/' . $form->uid) }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Buka Link Publik
                    </a>
                @endif
            </div>

            <div class="mt-4 p-4 rounded-lg bg-amber-50 border border-amber-200">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-900 mb-1">Persyaratan Akses</p>
                        <ul class="text-sm text-amber-800 space-y-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Responden harus login untuk mengisi form
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Email wajib menggunakan domain <strong class="font-semibold">@mhs.unimal.ac.id</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>

    <script>
        function formSettings(cfg) {
            return {
                // toggles
                requireSignIn: !!cfg.requireSignIn,
                collectEmails: !!cfg.collectEmails,
                limitOne: !!cfg.limitOne,
                allowEdit: !!cfg.allowEdit,
                showProgress: !!cfg.showProgress,
                shuffleQ: !!cfg.shuffleQ,
                sendReceipt: !!cfg.sendReceipt,
                captcha: !!cfg.captcha,

                // periode
                startAt: cfg.startAt || '',
                endAt: cfg.endAt || '',

                // theme
                themeText: cfg.themeText || '',
                themeValid: true,
                themePreview: {
                    primary: '',
                    accent: ''
                },

                init() {
                    this.syncDeps();
                    this.validateTheme();
                },

                syncDeps() {
                    if (!this.requireSignIn) this.limitOne = false;
                    if (!this.collectEmails) this.sendReceipt = false;
                },

                periodSummary() {
                    const s = this.startAt,
                        e = this.endAt;
                    if (!s && !e) return 'Tidak dibatasi waktu.';
                    if (s && !e) return `Aktif mulai ${s}`;
                    if (!s && e) return `Aktif hingga ${e}`;
                    return `Aktif ${s} — ${e}`;
                },

                validateTheme() {
                    const t = (this.themeText || '').trim();
                    if (!t) {
                        this.themeValid = true;
                        this.themePreview = {
                            primary: '',
                            accent: ''
                        };
                        return;
                    }
                    try {
                        const obj = JSON.parse(t);
                        this.themeValid = true;
                        this.themePreview = {
                            primary: obj.primary || '',
                            accent: obj.accent || ''
                        };
                    } catch (e) {
                        this.themeValid = false;
                        this.themePreview = {
                            primary: '',
                            accent: ''
                        };
                    }
                },
            }
        }
    </script>
</x-app-layout>
