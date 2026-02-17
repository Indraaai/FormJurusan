<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-secondary-900">Pengaturan Form</h2>
                <p class="mt-1 text-sm text-secondary-600">
                    Form: <span class="font-semibold text-primary-600">{{ $form->title ?? 'Form' }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.forms.edit', $form) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-secondary-200 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Edit
                </a>
                <a href="{{ route('admin.forms.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-primary-200 bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                    <i class="bi bi-list-ul"></i>
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

    <div class="py-6" x-data="formSettings({
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
                <div class="rounded-xl bg-success-50 border border-success-200 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-success-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-success-800">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl bg-danger-50 border border-danger-200 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle-fill text-danger-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-danger-800 mb-2">
                                Terdapat beberapa kesalahan:
                            </h3>
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-start gap-2 text-sm text-danger-700">
                                        <i class="bi bi-dot text-lg leading-none"></i>
                                        <span>{{ $error }}</span>
                                    </li>
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

                {{-- Section 1: Akses & Identitas --}}
                <div class="bg-white rounded-xl shadow-sm border border-secondary-200 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-primary-50 via-primary-100/50 to-secondary-50 px-6 py-5 border-b border-secondary-200">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-shield-lock-fill text-white text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-secondary-900">Akses & Identitas</h3>
                                <p class="text-sm text-secondary-600 mt-0.5">Kontrol siapa yang bisa mengisi form dan
                                    bagaimana mereka diidentifikasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Require Sign In --}}
                        <div class="group relative rounded-xl border-2 p-5 transition-all duration-200"
                            :class="requireSignIn ? 'border-primary-200 bg-primary-50/30 shadow-sm' :
                                'border-secondary-200 bg-white hover:border-secondary-300'">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                        :class="requireSignIn ? 'bg-primary-600 shadow-sm' : 'bg-secondary-200'">
                                        <i class="text-lg"
                                            :class="requireSignIn ? 'bi bi-person-check-fill text-white' :
                                                'bi bi-person text-secondary-500'"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-secondary-900">Wajib Login</h4>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium"
                                                :class="requireSignIn ? 'bg-primary-100 text-primary-700' :
                                                    'bg-secondary-100 text-secondary-600'">
                                                <i class="bi"
                                                    :class="requireSignIn ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                                                <span x-text="requireSignIn ? 'Aktif' : 'Nonaktif'"></span>
                                            </span>
                                        </div>
                                        <p class="text-sm text-secondary-600 leading-relaxed">User harus login terlebih
                                            dahulu sebelum bisa mengakses dan mengisi form</p>
                                        <div
                                            class="mt-2 flex items-start gap-2 text-xs text-info-700 bg-info-50 px-3 py-2 rounded-lg">
                                            <i class="bi bi-info-circle flex-shrink-0 mt-0.5"></i>
                                            <span>Direkomendasikan untuk form internal yang membutuhkan identitas
                                                user</span>
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="require_sign_in" value="0">
                                    <input type="checkbox" name="require_sign_in" value="1" x-model="requireSignIn"
                                        class="sr-only peer">
                                    <div
                                        class="w-12 h-6 bg-secondary-300 peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-primary-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 shadow-sm">
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Collect Emails --}}
                        <div class="group relative rounded-xl border-2 p-5 transition-all duration-200"
                            :class="collectEmails ? 'border-primary-200 bg-primary-50/30 shadow-sm' :
                                'border-secondary-200 bg-white hover:border-secondary-300'">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                        :class="collectEmails ? 'bg-primary-600 shadow-sm' : 'bg-secondary-200'">
                                        <i class="text-lg"
                                            :class="collectEmails ? 'bi bi-envelope-check-fill text-white' :
                                                'bi bi-envelope text-secondary-500'"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-secondary-900">Kumpulkan Email</h4>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium"
                                                :class="collectEmails ? 'bg-primary-100 text-primary-700' :
                                                    'bg-secondary-100 text-secondary-600'">
                                                <i class="bi"
                                                    :class="collectEmails ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                                                <span x-text="collectEmails ? 'Aktif' : 'Nonaktif'"></span>
                                            </span>
                                        </div>
                                        <p class="text-sm text-secondary-600 leading-relaxed">Otomatis menyimpan alamat
                                            email responden saat mereka submit form</p>
                                        <div
                                            class="mt-2 flex items-start gap-2 text-xs text-warning-700 bg-warning-50 px-3 py-2 rounded-lg">
                                            <i class="bi bi-shield-check flex-shrink-0 mt-0.5"></i>
                                            <span>Email akan disimpan dengan aman dan hanya bisa diakses oleh
                                                admin</span>
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="collect_emails" value="0">
                                    <input type="checkbox" name="collect_emails" value="1" x-model="collectEmails"
                                        class="sr-only peer">
                                    <div
                                        class="w-12 h-6 bg-secondary-300 peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-primary-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 shadow-sm">
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Limit One Response --}}
                        <div class="group relative rounded-xl border-2 p-5 transition-all duration-200"
                            :class="!requireSignIn ? 'opacity-60 cursor-not-allowed border-secondary-100 bg-secondary-50/30' : (
                                limitOne ? 'border-primary-200 bg-primary-50/30 shadow-sm' :
                                'border-secondary-200 bg-white hover:border-secondary-300')">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                        :class="limitOne && requireSignIn ? 'bg-primary-600 shadow-sm' : 'bg-secondary-200'">
                                        <i class="text-lg"
                                            :class="limitOne && requireSignIn ? 'bi bi-1-circle-fill text-white' :
                                                'bi bi-1-circle text-secondary-500'"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-secondary-900">Batasi 1 Respons per User</h4>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium"
                                                :class="limitOne && requireSignIn ? 'bg-primary-100 text-primary-700' :
                                                    'bg-secondary-100 text-secondary-600'">
                                                <i class="bi"
                                                    :class="limitOne && requireSignIn ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                                                <span x-text="limitOne && requireSignIn ? 'Aktif' : 'Nonaktif'"></span>
                                            </span>
                                        </div>
                                        <p class="text-sm text-secondary-600 leading-relaxed">Setiap user hanya bisa
                                            submit form maksimal 1 kali</p>
                                        <div class="mt-2 flex items-start gap-2 text-xs px-3 py-2 rounded-lg"
                                            :class="!requireSignIn ? 'text-danger-700 bg-danger-50' : 'text-info-700 bg-info-50'">
                                            <i class="bi flex-shrink-0 mt-0.5"
                                                :class="!requireSignIn ? 'bi-exclamation-triangle' : 'bi-info-circle'"></i>
                                            <span x-show="!requireSignIn">Fitur ini memerlukan "Wajib Login" untuk
                                                diaktifkan</span>
                                            <span x-show="requireSignIn" x-cloak>Berguna untuk survey atau voting yang
                                                membutuhkan validitas data</span>
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0"
                                    :class="!requireSignIn ? 'pointer-events-none' : ''">
                                    <input type="hidden" name="limit_one_response" value="0">
                                    <input type="checkbox" name="limit_one_response" value="1"
                                        x-model="limitOne" :disabled="!requireSignIn" class="sr-only peer">
                                    <div
                                        class="w-12 h-6 bg-secondary-300 peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-primary-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed shadow-sm">
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Allow Edit After Submit --}}
                        <div class="group relative rounded-xl border-2 p-5 transition-all duration-200"
                            :class="allowEdit ? 'border-primary-200 bg-primary-50/30 shadow-sm' :
                                'border-secondary-200 bg-white hover:border-secondary-300'">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                        :class="allowEdit ? 'bg-primary-600 shadow-sm' : 'bg-secondary-200'">
                                        <i class="text-lg"
                                            :class="allowEdit ? 'bi bi-pencil-square text-white' :
                                                'bi bi-pencil text-secondary-500'"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-secondary-900">Boleh Edit Setelah Submit</h4>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium"
                                                :class="allowEdit ? 'bg-primary-100 text-primary-700' :
                                                    'bg-secondary-100 text-secondary-600'">
                                                <i class="bi"
                                                    :class="allowEdit ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                                                <span x-text="allowEdit ? 'Aktif' : 'Nonaktif'"></span>
                                            </span>
                                        </div>
                                        <p class="text-sm text-secondary-600 leading-relaxed">Responden dapat
                                            memperbaiki atau memperbarui jawaban mereka setelah submit</p>
                                        <div
                                            class="mt-2 flex items-start gap-2 text-xs text-info-700 bg-info-50 px-3 py-2 rounded-lg">
                                            <i class="bi bi-info-circle flex-shrink-0 mt-0.5"></i>
                                            <span>Memudahkan user yang ingin mengkoreksi jawaban tanpa submit
                                                ulang</span>
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="allow_edit_after_submit" value="0">
                                    <input type="checkbox" name="allow_edit_after_submit" value="1"
                                        x-model="allowEdit" class="sr-only peer">
                                    <div
                                        class="w-12 h-6 bg-secondary-300 peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-primary-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 shadow-sm">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Pengalaman Pengguna --}}
                <div class="bg-white rounded-xl shadow-sm border border-secondary-200 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-purple-50 via-purple-100/50 to-secondary-50 px-6 py-5 border-b border-secondary-200">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-stars text-white text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-secondary-900">Pengalaman Pengguna</h3>
                                <p class="text-sm text-secondary-600 mt-0.5">Sesuaikan tampilan dan interaksi form
                                    dengan responden</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Show Progress Bar --}}
                        <div class="group relative rounded-xl border-2 p-5 transition-all duration-200"
                            :class="showProgress ? 'border-purple-200 bg-purple-50/30 shadow-sm' :
                                'border-secondary-200 bg-white hover:border-secondary-300'">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                        :class="showProgress ? 'bg-purple-600 shadow-sm' : 'bg-secondary-200'">
                                        <i class="text-lg"
                                            :class="showProgress ? 'bi bi-list-columns text-white' :
                                                'bi bi-list text-secondary-500'"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-secondary-900">Tampilkan Progress Bar</h4>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium"
                                                :class="showProgress ? 'bg-purple-100 text-purple-700' :
                                                    'bg-secondary-100 text-secondary-600'">
                                                <i class="bi"
                                                    :class="showProgress ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                                                <span x-text="showProgress ? 'Aktif' : 'Nonaktif'"></span>
                                            </span>
                                        </div>
                                        <p class="text-sm text-secondary-600 leading-relaxed">Menampilkan indikator
                                            kemajuan pengisian form di bagian atas halaman</p>
                                        <div
                                            class="mt-2 flex items-start gap-2 text-xs text-info-700 bg-info-50 px-3 py-2 rounded-lg">
                                            <i class="bi bi-info-circle flex-shrink-0 mt-0.5"></i>
                                            <span>Membantu user mengetahui sudah sejauh mana mereka mengisi form</span>
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="show_progress_bar" value="0">
                                    <input type="checkbox" name="show_progress_bar" value="1"
                                        x-model="showProgress" class="sr-only peer">
                                    <div
                                        class="w-12 h-6 bg-secondary-300 peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-purple-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 shadow-sm">
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Shuffle Question Order --}}
                        <div class="group relative rounded-xl border-2 p-5 transition-all duration-200"
                            :class="shuffleQ ? 'border-purple-200 bg-purple-50/30 shadow-sm' :
                                'border-secondary-200 bg-white hover:border-secondary-300'">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                        :class="shuffleQ ? 'bg-purple-600 shadow-sm' : 'bg-secondary-200'">
                                        <i class="text-lg"
                                            :class="shuffleQ ? 'bi bi-shuffle text-white' :
                                                'bi bi-arrow-down-up text-secondary-500'"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-secondary-900">Acak Urutan Pertanyaan</h4>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium"
                                                :class="shuffleQ ? 'bg-purple-100 text-purple-700' :
                                                    'bg-secondary-100 text-secondary-600'">
                                                <i class="bi"
                                                    :class="shuffleQ ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                                                <span x-text="shuffleQ ? 'Aktif' : 'Nonaktif'"></span>
                                            </span>
                                        </div>
                                        <p class="text-sm text-secondary-600 leading-relaxed">Menampilkan pertanyaan
                                            dalam urutan acak untuk setiap responden</p>
                                        <div
                                            class="mt-2 flex items-start gap-2 text-xs text-warning-700 bg-warning-50 px-3 py-2 rounded-lg">
                                            <i class="bi bi-lightbulb flex-shrink-0 mt-0.5"></i>
                                            <span>Berguna untuk ujian atau kuis agar mengurangi kemungkinan
                                                menyontek</span>
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="shuffle_question_order" value="0">
                                    <input type="checkbox" name="shuffle_question_order" value="1"
                                        x-model="shuffleQ" class="sr-only peer">
                                    <div
                                        class="w-12 h-6 bg-secondary-300 peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-purple-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 shadow-sm">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Keamanan & Notifikasi --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
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
                                    'border-emerald-300 bg-emerald-50/50' :
                                    'border-gray-200 bg-white hover:border-gray-300')">
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
                                            <div class="font-semibold text-gray-900 mb-1">Kirim Bukti Respons (Email)
                                            </div>
                                            <p class="text-sm text-gray-600">Email konfirmasi ke responden</p>
                                            <p class="text-xs text-amber-700 mt-1 font-medium"
                                                x-show="!collectEmails">⚠
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
                                :class="captcha ? 'border-emerald-300 bg-emerald-50/50' :
                                    'border-gray-200 bg-white hover:border-gray-300'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                            :class="captcha ? 'bg-emerald-600' : 'bg-gray-200'">
                                            <svg class="w-5 h-5" :class="captcha ? 'text-white' : 'text-gray-500'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                        <input type="checkbox" name="captcha_enabled" value="1"
                                            x-model="captcha" class="sr-only peer">
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
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-teal-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
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
                                <svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
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
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
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
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Maksimal 500 karakter. Pesan ini akan ditampilkan setelah responden submit
                                    form.</span>
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
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-medium"
                                    :class="themeValid ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' :
                                        'bg-red-100 text-red-800 border border-red-200'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                        x-show="themeValid">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                        x-show="!themeValid">
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
                                            <span
                                                class="inline-block h-6 w-12 rounded border border-gray-300 shadow-sm"
                                                :style="`background:${themePreview.primary}`"></span>
                                            <span class="text-xs font-mono text-gray-500"
                                                x-text="themePreview.primary"></span>
                                        </div>
                                        <div class="flex items-center gap-2" x-show="themePreview.accent">
                                            <span class="text-xs font-medium text-gray-600">Accent:</span>
                                            <span
                                                class="inline-block h-6 w-12 rounded border border-gray-300 shadow-sm"
                                                :style="`background:${themePreview.accent}`"></span>
                                            <span class="text-xs font-mono text-gray-500"
                                                x-text="themePreview.accent"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <p class="mt-2 flex items-start gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Biarkan kosong jika tidak diperlukan. Pastikan format JSON valid dengan property
                                    "primary"
                                    dan/atau "accent".</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Info & Note --}}
                <div class="bg-info-50 border border-info-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-11 h-11 rounded-xl bg-info-600 flex items-center justify-center shadow-sm">
                            <i class="bi bi-info-circle-fill text-white text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-bold text-info-900 mb-2">Catatan Penting</h4>
                            <p class="text-sm text-info-800 leading-relaxed">
                                Untuk mempublikasikan atau menyembunyikan form, gunakan tombol <span
                                    class="font-semibold">Publish/Unpublish</span> di halaman
                                <a href="{{ route('admin.forms.edit', $form) }}"
                                    class="inline-flex items-center gap-1 font-bold text-info-700 hover:text-info-900 underline decoration-2 underline-offset-2 transition">
                                    Edit Form
                                    <i class="bi bi-arrow-right text-sm"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="sticky bottom-6 bg-white rounded-xl shadow-lg border border-secondary-200 p-5">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-save text-success-600 text-xl flex-shrink-0"></i>
                            <div>
                                <p class="text-sm font-semibold text-secondary-900">Simpan Pengaturan</p>
                                <p class="text-xs text-secondary-600 mt-0.5">Perubahan akan diterapkan setelah Anda
                                    klik tombol simpan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.forms.edit', $form) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-secondary-300 bg-white px-5 py-2.5 text-sm font-semibold text-secondary-700 hover:bg-secondary-50 focus-visible:ring-2 focus-visible:ring-secondary-200 transition">
                                <i class="bi bi-x-lg"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-primary-600 bg-primary-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700 hover:border-primary-700 focus-visible:ring-2 focus-visible:ring-primary-200 transition">
                                <i class="bi bi-check-lg"></i>
                                Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            {{-- Form Link Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-secondary-200 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-success-50 via-success-100/50 to-secondary-50 px-6 py-5 border-b border-secondary-200">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-success-500 to-success-600 flex items-center justify-center shadow-sm">
                            <i class="bi bi-link-45deg text-white text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-secondary-900">Tautan Form Publik</h3>
                            <p class="text-sm text-secondary-600 mt-0.5">Share link ini kepada responden untuk mengisi
                                form</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <label class="flex items-center gap-2 text-xs font-semibold text-secondary-700 mb-2">
                                <i class="bi bi-tag"></i>
                                <span>Form UID</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <code
                                    class="flex-1 px-4 py-3 rounded-xl bg-secondary-50 border border-secondary-200 text-sm font-mono text-secondary-900 break-all">{{ $form->uid }}</code>
                                @if (isset($form->uid))
                                    <a href="{{ url('/forms/' . $form->uid) }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-success-600 px-5 py-3 text-sm font-semibold text-white hover:bg-success-700 focus-visible:ring-2 focus-visible:ring-success-200 transition shadow-sm flex-shrink-0">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                        <span class="hidden sm:inline">Buka Link</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-warning-50 border border-warning-200 p-5">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-xl bg-warning-600 flex items-center justify-center shadow-sm">
                                <i class="bi bi-shield-check text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-warning-900 mb-2">Persyaratan Akses Form</h4>
                                <ul class="space-y-2">
                                    <li class="flex items-start gap-3 text-sm text-warning-800">
                                        <i class="bi bi-check-circle-fill text-warning-600 flex-shrink-0 mt-0.5"></i>
                                        <span>Responden harus login terlebih dahulu untuk mengisi form</span>
                                    </li>
                                    <li class="flex items-start gap-3 text-sm text-warning-800">
                                        <i class="bi bi-check-circle-fill text-warning-600 flex-shrink-0 mt-0.5"></i>
                                        <span>Email wajib menggunakan domain <strong
                                                class="font-bold">@mhs.unimal.ac.id</strong></span>
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
