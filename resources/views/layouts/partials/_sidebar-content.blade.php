{{-- resources/views/layouts/partials/_sidebar-content.blade.php --}}
@php
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $brandTitle = $isAdmin ? 'Form Admin' : 'Dashboard'; // << ganti sesuai selera, mis: 'Forms'
@endphp

<div class="flex h-full flex-col">
    {{-- Brand --}}
    <div class="flex h-16 items-center gap-3 border-b border-emerald-100 px-4">
        <a href="{{ $isAdmin ? route('admin.home') : route('dashboard') }}" class="inline-flex items-center gap-2">
            <x-application-logo class="h-8 w-auto fill-current text-emerald-700" />
            <div class="text-lg font-semibold text-emerald-900">{{ $brandTitle }}</div>
        </a>
    </div>

    {{-- Main nav --}}
    <nav class="flex-1 overflow-y-auto p-3 space-y-1">
        <a href="{{ route('dashboard') }}"
            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm
           {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">
            <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 group-[&.bg-emerald-600]:bg-white"></span>
            Dashboard
        </a>

        @if ($isAdmin)
            <a href="{{ route('admin.home') }}"
                class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm
               {{ request()->routeIs('admin.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 group-[&.bg-emerald-600]:bg-white"></span>
                Admin Home
            </a>
            <a href="{{ route('admin.forms.index') }}"
                class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm
               {{ request()->routeIs('admin.forms.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 group-[&.bg-emerald-600]:bg-white"></span>
                Forms
            </a>
        @else
            {{-- Menu khusus respondent --}}
            <a href="{{ route('respondent.forms.index') }}"
                class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm
               {{ request()->routeIs('respondent.forms.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 group-[&.bg-emerald-600]:bg-white"></span>
                Form Tersedia
            </a>
        @endif

        {{-- Form Tools hanya untuk admin --}}
        @php
            $formParam = request()->route('form');
            $sectionParam = request()->route('section');
            $questionParam = request()->route('question');
            $logicParam = request()->route('logicRule');
            $respParam = request()->route('response');
            $currentForm =
                $formParam ??
                (optional($sectionParam)->form ??
                    (optional(optional($questionParam)->section)->form ??
                        (optional($logicParam)->form ?? (optional($respParam)->form ?? null))));
        @endphp

        @if ($isAdmin && $currentForm)
            <div class="mt-3 rounded-xl border border-emerald-100 bg-emerald-50/60 p-3">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-emerald-700/80">Form Tools</div>
                <div class="text-sm text-emerald-900 font-medium line-clamp-2">
                    {{ $currentForm->title }} <span
                        class="text-xs text-emerald-700/70">({{ $currentForm->uid }})</span>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-1">
                    <a href="{{ route('admin.forms.sections.index', $currentForm) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.forms.sections.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">Sections</a>
                    <a href="{{ route('admin.forms.questions.index', $currentForm) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.forms.questions.*') || request()->routeIs('admin.questions.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">Questions</a>
                    <a href="{{ route('admin.forms.logic.index', $currentForm) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.forms.logic.*') || request()->routeIs('admin.logic.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">Logic</a>
                    <a href="{{ route('admin.forms.responses.index', $currentForm) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.forms.responses.*') || request()->routeIs('admin.responses.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">Responses</a>
                    <a href="{{ route('admin.forms.settings.edit', $currentForm) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.forms.settings.*') ? 'bg-emerald-600 text-white' : 'text-emerald-900 hover:bg-emerald-50' }}">Settings</a>
                    <a href="{{ route('admin.forms.preview', $currentForm) }}"
                        class="rounded-lg px-3 py-2 text-sm text-emerald-900 hover:bg-emerald-50">Preview</a>
                </div>
            </div>
        @endif
    </nav>

    {{-- User block tetap --}}
    @auth
        <div class="border-t border-emerald-100 p-3">
            <div class="mb-2 text-sm font-medium text-emerald-900">{{ Auth::user()->name }}</div>
            <div class="text-xs text-emerald-700/80">{{ Auth::user()->email }}</div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('profile.edit') }}"
                    class="rounded-lg border border-emerald-200 px-3 py-1.5 text-sm text-emerald-800 hover:bg-emerald-50">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm text-white hover:bg-emerald-700">Log
                        Out</button>
                </form>
            </div>
        </div>
    @endauth
</div>
