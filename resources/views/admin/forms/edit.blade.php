<x-app-layout>
    @php
        $descriptionValue = old('description', $form->description);
        $recommendedDescriptionLength = 500;
        $descriptionLength = \Illuminate\Support\Str::length($descriptionValue ?? '');
        $sectionsCount = $form->sections()->count();
        $questionsCount = $form->questions()->count();
        $responsesCount = $form->responses()->count();

        $formStats = [
            [
                'label' => 'Sections',
                'value' => $sectionsCount,
                'sub' => 'Atur alur pertanyaan',
                'icon' => 'bi bi-layers',
                'wrapper' => 'border-primary-100',
                'iconWrapper' => 'bg-primary-50 text-primary-600',
            ],
            [
                'label' => 'Pertanyaan',
                'value' => $questionsCount,
                'sub' => 'Total pertanyaan aktif',
                'icon' => 'bi bi-list-check',
                'wrapper' => 'border-secondary-100',
                'iconWrapper' => 'bg-secondary-50 text-secondary-600',
            ],
            [
                'label' => 'Respons',
                'value' => $responsesCount,
                'sub' => 'Jawaban terkumpul',
                'icon' => 'bi bi-chat-dots',
                'wrapper' => 'border-success-100',
                'iconWrapper' => 'bg-success-50 text-success-600',
            ],
        ];

        $workflowSteps = [
            [
                'title' => 'Informasi Form',
                'desc' => 'Judul, deskripsi, dan identitas form',
                'status' => 'current',
            ],
            [
                'title' => 'Konten & Logic',
                'desc' => 'Sections, pertanyaan, dan branching',
                'status' => $questionsCount > 0 ? 'complete' : 'upcoming',
            ],
            [
                'title' => 'Publikasi & Distribusi',
                'desc' => $form->is_published ? 'Form sudah live dan dapat diakses' : 'Atur jadwal publish dan akses',
                'status' => $form->is_published ? 'complete' : 'upcoming',
            ],
        ];

        $quickActions = [
            [
                'label' => 'Kelola Sections',
                'desc' => 'Susun struktur dan alur form',
                'href' => route('admin.forms.sections.index', $form),
                'icon' => 'bi bi-layout-three-columns',
                'accent' => 'primary',
            ],
            [
                'label' => 'Kelola Questions',
                'desc' => 'Tambah atau edit pertanyaan',
                'href' => route('admin.forms.questions.index', $form),
                'icon' => 'bi bi-list-ul',
                'accent' => 'secondary',
            ],
            [
                'label' => 'Kelola Logic',
                'desc' => 'Atur alur dan percabangan',
                'href' => route('admin.forms.logic.index', $form),
                'icon' => 'bi bi-diagram-3',
                'accent' => 'accent',
            ],
            [
                'label' => 'Lihat Responses',
                'desc' => 'Analisa jawaban responden',
                'href' => route('admin.forms.responses.index', $form),
                'icon' => 'bi bi-chat-square-text',
                'accent' => 'success',
            ],
            [
                'label' => 'Form Settings',
                'desc' => 'Atur akses dan preferensi',
                'href' => route('admin.forms.settings.edit', $form),
                'icon' => 'bi bi-gear-wide-connected',
                'accent' => 'warning',
            ],
        ];

        $statusBadgeClasses = $form->is_published
            ? 'bg-success-100 text-success-700 border-success-200'
            : 'bg-secondary-100 text-secondary-700 border-secondary-200';
    @endphp

    <x-slot name="header">
        <div class="space-y-4 animate-fade-in">
            <div class="relative overflow-hidden rounded-3xl bg-primary-600 text-white shadow-soft-lg">
                <div class="relative z-10 p-6 lg:p-8">
                    <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.35em] text-white/70 font-semibold">Form Builder</p>
                            <h2 class="mt-2 text-3xl font-bold flex flex-wrap items-center gap-3">
                                Edit Form
                                <span
                                    class="px-3 py-1 rounded-full border border-white/30 text-xs font-semibold backdrop-blur">
                                    {{ $form->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </h2>
                            <p class="mt-2 text-base text-white/80">
                                {{ $form->title ?: 'Tanpa Judul' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.forms.preview', $form) }}" rel="noopener"
                                class="inline-flex items-center gap-2 rounded-2xl border border-white/40 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Preview Admin
                            </a>
                            <a href="{{ route('forms.start', $form) }}" rel="noopener" target="_blank"
                                class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-primary-700 shadow-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Buka Link Publik
                            </a>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 gap-4 text-sm text-white/80 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-wide text-white/70">Terakhir diperbarui</p>
                            <p class="mt-1 text-lg font-semibold">
                                {{ $form->updated_at?->timezone(config('app.timezone'))->format('d M Y, H:i') ?? '-' }}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-wide text-white/70">Total Respons</p>
                            <p class="mt-1 text-lg font-semibold">{{ $responsesCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-wide text-white/70">Checklist Tahapan</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-success-300"></span>
                                <span>{{ collect($workflowSteps)->where('status', 'complete')->count() }}/3
                                    selesai</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <div class="relative py-8 sm:py-10">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-primary-50 via-white to-white"></div>
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div
                    class="flex items-center gap-3 rounded-2xl border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 shadow-soft">
                    <i class="bi bi-check-circle-fill text-success-500"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-danger-200 bg-danger-50 p-4 shadow-soft">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger-500 text-lg"></i>
                        <div>
                            <p class="text-sm font-semibold text-danger-700">Periksa kembali input berikut:</p>
                            <ul class="mt-2 space-y-1 text-sm text-danger-600 list-disc list-inside">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6">
                    <div class="rounded-3xl border border-primary-100 bg-white/90 shadow-soft overflow-hidden">
                        <div
                            class="flex items-center justify-between border-b border-primary-50 bg-primary-50 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-primary-100 text-primary-600">
                                    <i class="bi bi-clipboard-data text-xl"></i>
                                </span>
                                <div>
                                    <h3 class="text-base font-semibold text-secondary-900">Form Snapshot</h3>
                                    <p class="text-xs text-secondary-500">Pantau status dan kesehatan form</p>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses }}">
                                <span
                                    class="h-2 w-2 rounded-full {{ $form->is_published ? 'bg-success-500' : 'bg-secondary-400' }}"></span>
                                {{ $form->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <div class="p-5 space-y-5">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-secondary-500">Form UID</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <code id="form-uid"
                                        class="flex-1 rounded-2xl border border-secondary-100 bg-secondary-50 px-3 py-2 text-xs font-mono text-secondary-700">{{ $form->uid }}</code>
                                    <button type="button" id="copy-uid"
                                        class="inline-flex items-center gap-2 rounded-2xl border border-secondary-200 bg-white px-3 py-2 text-xs font-semibold text-secondary-700 transition-all hover:border-primary-200 hover:text-primary-700">
                                        <i class="bi bi-clipboard-check"></i>
                                        Salin
                                    </button>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 gap-4 text-sm text-secondary-600">
                                <div class="rounded-2xl border border-secondary-100 bg-secondary-50 px-3 py-2">
                                    <dt class="text-xs uppercase tracking-wide text-secondary-500">Dibuat</dt>
                                    <dd class="font-semibold text-secondary-900">
                                        {{ $form->created_at?->timezone(config('app.timezone'))->format('d M Y, H:i') ?? '-' }}
                                    </dd>
                                </div>
                                <div class="rounded-2xl border border-secondary-100 bg-secondary-50 px-3 py-2">
                                    <dt class="text-xs uppercase tracking-wide text-secondary-500">Diubah</dt>
                                    <dd class="font-semibold text-secondary-900">
                                        {{ $form->updated_at?->timezone(config('app.timezone'))->format('d M Y, H:i') ?? '-' }}
                                    </dd>
                                </div>
                            </dl>
                            <div class="grid grid-cols-3 gap-3 text-secondary-600">
                                @foreach ($formStats as $stat)
                                    <div @class([
                                        'rounded-2xl bg-white p-3 text-sm shadow-inner-soft border',
                                        $stat['wrapper'],
                                    ])>
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-semibold text-secondary-500">{{ $stat['label'] }}
                                            </p>
                                            <span
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ $stat['iconWrapper'] }}">
                                                <i class="{{ $stat['icon'] }} text-base"></i>
                                            </span>
                                        </div>
                                        <p class="mt-2 text-2xl font-bold text-secondary-900">{{ $stat['value'] }}</p>
                                        <p class="text-xs text-secondary-500">{{ $stat['sub'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-secondary-100 bg-white/90 p-5 shadow-soft">
                        <h3 class="flex items-center gap-3 text-base font-semibold text-secondary-900">
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-secondary-100 text-secondary-600">
                                <i class="bi bi-lightning-charge"></i>
                            </span>
                            Aksi Cepat
                        </h3>
                        <div class="mt-4 space-y-3">
                            @foreach ($quickActions as $action)
                                <a href="{{ $action['href'] }}" @class([
                                    'group flex items-center gap-4 rounded-2xl border bg-white px-4 py-3 transition-all shadow-soft hover:shadow-soft-md',
                                    'border-primary-100 hover:border-primary-200 hover:bg-primary-50' =>
                                        $action['accent'] === 'primary',
                                    'border-secondary-100 hover:border-secondary-200 hover:bg-secondary-50' =>
                                        $action['accent'] === 'secondary',
                                    'border-success-100 hover:border-success-200 hover:bg-success-50' =>
                                        $action['accent'] === 'success',
                                    'border-accent-100 hover:border-accent-200 hover:bg-accent-50' =>
                                        $action['accent'] === 'accent',
                                    'border-warning-100 hover:border-warning-200 hover:bg-warning-50' =>
                                        $action['accent'] === 'warning',
                                ])>
                                    <div
                                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-600 group-hover:scale-105 transition-transform">
                                        <i class="{{ $action['icon'] }} text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-secondary-900">{{ $action['label'] }}</p>
                                        <p class="text-sm text-secondary-500">{{ $action['desc'] }}</p>
                                    </div>
                                    <i
                                        class="bi bi-arrow-right text-secondary-400 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-3xl border border-secondary-100 bg-gradient-soft p-5 shadow-soft">
                        <h3 class="text-base font-semibold text-secondary-900">Checklist Tahapan</h3>
                        <p class="text-sm text-secondary-600">Ikuti langkah ini untuk memastikan form siap
                            dipublikasikan.</p>
                        <div class="mt-4 space-y-3">
                            @foreach ($workflowSteps as $index => $step)
                                @php
                                    $isComplete = $step['status'] === 'complete';
                                    $isCurrent = $step['status'] === 'current';
                                @endphp
                                <div @class([
                                    'flex items-center gap-4 rounded-2xl border bg-white/80 px-4 py-3 shadow-soft',
                                    'border-success-200' => $isComplete,
                                    'border-primary-200' => $isCurrent,
                                    'border-secondary-100' => !$isComplete && !$isCurrent,
                                ])>
                                    <div @class([
                                        'flex h-12 w-12 items-center justify-center rounded-2xl text-base font-semibold',
                                        'bg-success-100 text-success-600' => $isComplete,
                                        'bg-primary-100 text-primary-600' => $isCurrent,
                                        'bg-secondary-50 text-secondary-500' => !$isComplete && !$isCurrent,
                                    ])>
                                        @if ($isComplete)
                                            <i class="bi bi-check-lg text-xl"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-secondary-900">{{ $step['title'] }}</p>
                                        <p class="text-sm text-secondary-500">{{ $step['desc'] }}</p>
                                    </div>
                                    <span @class([
                                        'text-xs font-semibold uppercase tracking-wide',
                                        'text-success-600' => $isComplete,
                                        'text-primary-600' => $isCurrent,
                                        'text-secondary-400' => !$isComplete && !$isCurrent,
                                    ])>
                                        {{ $isComplete ? 'Selesai' : ($isCurrent ? 'Sedang Diedit' : 'Berikutnya') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-3xl border border-secondary-100 bg-white/95 shadow-soft overflow-hidden">
                        <div class="border-b border-secondary-100 bg-secondary-50 px-6 py-5">
                            <h3 class="flex items-center gap-3 text-lg font-semibold text-secondary-900">
                                <span
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-600/10 text-primary-600">
                                    <i class="bi bi-pencil-square text-xl"></i>
                                </span>
                                Edit Informasi Form
                            </h3>
                            <p class="text-sm text-secondary-600">Perbarui konten form agar konsisten dengan panduan
                                brand.</p>
                        </div>

                        <form id="update-form" method="POST" action="{{ route('admin.forms.update', $form) }}">
                            @csrf
                            @method('PUT')

                            <div class="p-6 space-y-6">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label for="title" class="text-sm font-semibold text-secondary-900">
                                            Judul Form <span class="text-danger-500">*</span>
                                        </label>
                                        <p class="text-xs text-secondary-500">Gunakan maksimal 80 karakter.</p>
                                    </div>
                                    <input type="text" id="title" name="title" required
                                        value="{{ old('title', $form->title) }}"
                                        placeholder="Contoh: Form Pendaftaran Program MBKM"
                                        class="form-input w-full rounded-2xl border-secondary-200 bg-white/80 px-4 py-3 text-secondary-900 shadow-inner-soft focus:border-primary-500 focus:ring-primary-500">
                                    @error('title')
                                        <p class="flex items-center gap-2 text-sm text-danger-600">
                                            <i class="bi bi-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label for="desc" class="text-sm font-semibold text-secondary-900">
                                            Deskripsi Form
                                        </label>
                                        <span id="desc-counter" data-limit="{{ $recommendedDescriptionLength }}"
                                            class="text-xs font-semibold text-secondary-500">
                                            {{ $descriptionLength }}/{{ $recommendedDescriptionLength }}
                                        </span>
                                    </div>
                                    <textarea id="desc" name="description" rows="5"
                                        placeholder="Jelaskan tujuan form atau instruksi singkat untuk responden"
                                        class="form-textarea w-full resize-none rounded-2xl border-secondary-200 bg-white/80 px-4 py-3 text-secondary-900 shadow-inner-soft focus:border-primary-500 focus:ring-primary-500">{{ $descriptionValue }}</textarea>
                                    @error('description')
                                        <p class="flex items-center gap-2 text-sm text-danger-600">
                                            <i class="bi bi-exclamation-circle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="flex items-start gap-2 text-xs text-secondary-500">
                                        <i class="bi bi-info-circle text-base text-secondary-400"></i>
                                        <span>Deskripsi muncul di halaman pembuka form dan membantu responden memahami
                                            konteks. Gunakan bahasa singkat dan ramah.</span>
                                    </p>
                                </div>

                                <div
                                    class="rounded-2xl border border-secondary-100 bg-secondary-50 p-4 text-sm text-secondary-600">
                                    <p class="font-semibold text-secondary-800">Tips tampilan</p>
                                    <ul class="mt-2 list-disc space-y-1 pl-5">
                                        <li>Pastikan judul menyebutkan aksi utama (mis. "Daftar", "Survey", "Evaluasi").
                                        </li>
                                        <li>Gunakan maksimal dua paragraf pendek agar mudah dibaca.</li>
                                        <li>Sertakan kontak atau SLA jika responden membutuhkan bantuan.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="sticky bottom-4 border-t border-secondary-100 bg-white/95 px-6 py-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <a href="{{ route('admin.forms.index') }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-secondary-200 px-5 py-2.5 text-sm font-semibold text-secondary-700 transition-all hover:bg-secondary-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        Kembali ke Daftar
                                    </a>

                                    <div class="flex flex-wrap items-center gap-3">
                                        @if (!$form->is_published)
                                            <button type="submit" form="publish-form"
                                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-success-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-success-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-success-400"
                                                onclick="return confirm('Apakah Anda yakin ingin mempublikasikan form ini?');">
                                                <i class="bi bi-send-check"></i>
                                                Publish Form
                                            </button>
                                        @else
                                            <button type="submit" form="unpublish-form"
                                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-warning-500 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-warning-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-warning-300"
                                                onclick="return confirm('Apakah Anda yakin ingin meng-unpublish form ini?');">
                                                <i class="bi bi-eye-slash"></i>
                                                Unpublish
                                            </button>
                                        @endif

                                        <button type="submit" form="update-form"
                                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300">
                                            <i class="bi bi-save"></i>
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if (!$form->is_published)
                <form id="publish-form" method="POST" action="{{ route('admin.forms.publish', $form) }}"
                    class="hidden">
                    @csrf
                    @method('PUT')
                </form>
            @else
                <form id="unpublish-form" method="POST" action="{{ route('admin.forms.unpublish', $form) }}"
                    class="hidden">
                    @csrf
                    @method('PUT')
                </form>
            @endif
        </div>
    </div>

    <script>
        (() => {
            const btn = document.getElementById('copy-uid');
            const uid = document.getElementById('form-uid')?.textContent?.trim();
            if (!btn || !uid) return;

            btn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(uid);
                    const originalContent = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check2"></i><span>Disalin</span>';
                    btn.classList.add('bg-success-50', 'text-success-700', 'border-success-200');

                    setTimeout(() => {
                        btn.innerHTML = originalContent;
                        btn.classList.remove('bg-success-50', 'text-success-700',
                            'border-success-200');
                    }, 1800);
                } catch (error) {
                    console.error('Failed to copy UID:', error);
                }
            });
        })();

        (() => {
            const textarea = document.getElementById('desc');
            if (!textarea) return;

            const resize = () => {
                textarea.style.height = 'auto';
                textarea.style.height = Math.min(textarea.scrollHeight, 480) + 'px';
            };

            const counter = document.getElementById('desc-counter');
            const limit = Number(counter?.dataset.limit || 500);
            const updateCounter = () => {
                if (!counter) return;
                const length = textarea.value.length;
                counter.textContent = `${length}/${limit}`;
                counter.classList.toggle('text-danger-600', length > limit);
            };

            textarea.addEventListener('input', () => {
                resize();
                updateCounter();
            });

            window.addEventListener('load', () => {
                resize();
                updateCounter();
            });

            resize();
            updateCounter();
        })();
    </script>
</x-app-layout>
