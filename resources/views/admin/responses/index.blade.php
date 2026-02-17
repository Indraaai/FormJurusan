<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-secondary-900 leading-tight">Responses</h2>
                <p class="text-sm text-secondary-600 mt-1">
                    Form: <span class="font-semibold text-primary-600">{{ $form->title }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm font-medium text-secondary-700 hover:bg-secondary-50 focus:ring-2 focus:ring-primary-200 transition">
                    <i class="bi bi-arrow-left"></i>
                    <span>Daftar Form</span>
                </a>
                <a href="{{ route('admin.forms.responses.export', $form) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 transition">
                    <i class="bi bi-download"></i>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    @php
        $rows = $responses
            ->map(function ($resp) {
                $email = $resp->respondent_email ?? (optional($resp->respondent)->email ?? '-');
                $name = optional($resp->respondent)->name ?? '-';
                $dur = $resp->duration_seconds ? gmdate('H:i:s', $resp->duration_seconds) : '-';
                return [
                    'id' => $resp->id,
                    'seq' => null,
                    'uid' => $resp->uid,
                    'name' => $name,
                    'email' => $email,
                    'status' => $resp->status,
                    'submitted_at' => optional($resp->submitted_at)?->format('Y-m-d H:i:s'),
                    'duration' => $dur,
                    'show_url' => route('admin.responses.show', $resp),
                ];
            })
            ->values();
        $firstSeq = $responses->firstItem() ?? 1;
    @endphp

    <div class="py-8" x-data="respIndex({
        rows: @js($rows),
        firstSeq: {{ (int) $firstSeq }},
    })" x-init="init()">

        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-success-200 bg-success-50 p-3 text-success-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-primary-100 bg-white shadow-soft">

                <!-- Toolbar -->
                <div
                    class="flex flex-col gap-3 p-6 md:flex-row md:items-end md:justify-between border-b border-secondary-200">
                    <div class="grid w-full grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label class="block text-xs font-semibold text-secondary-700">Cari</label>
                            <input x-model.debounce.200ms="q" type="text" placeholder="Cari nama, email, UID…"
                                class="mt-1 w-full rounded-xl border border-secondary-300 bg-white px-4 py-2.5 text-sm placeholder:text-secondary-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-secondary-700">Status</label>
                            <select x-model="status"
                                class="mt-1 w-full rounded-xl border border-secondary-300 bg-white px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 transition">
                                <option value="">Semua</option>
                                <template x-for="s in statuses" :key="s">
                                    <option :value="s" x-text="s"></option>
                                </template>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-secondary-700">Dari (Submit)</label>
                                <input x-model="dFrom" type="date"
                                    class="mt-1 w-full rounded-xl border border-secondary-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 transition">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-secondary-700">Sampai</label>
                                <input x-model="dTo" type="date"
                                    class="mt-1 w-full rounded-xl border border-secondary-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 transition">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="resetFilters"
                            class="inline-flex items-center rounded-xl border border-secondary-300 bg-white px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-50 transition">
                            Reset
                        </button>
                        <div class="text-xs text-secondary-500" x-text="`${filtered.length} ditampilkan`"></div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-secondary-50 text-left text-secondary-700">
                            <tr class="border-y border-secondary-200">
                                <th class="sticky left-0 z-10 bg-secondary-50 py-3 pr-4 pl-6 font-semibold">#</th>
                                <th class="py-3 pr-4 font-semibold">UID</th>
                                <th class="py-3 pr-4 font-semibold">User</th>
                                <th class="hidden py-3 pr-4 font-semibold md:table-cell">Email</th>
                                <th class="py-3 pr-4 font-semibold">Status</th>
                                <th class="hidden py-3 pr-4 font-semibold lg:table-cell">Submitted At</th>
                                <th class="hidden py-3 pr-4 font-semibold md:table-cell">Duration</th>
                                <th class="py-3 pr-6 font-semibold">Tools</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-secondary-100">

                            <template x-if="filtered.length === 0">
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-secondary-500">
                                        Tidak ada data yang cocok dengan filter.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="row in filtered" :key="row.id">
                                <tr class="hover:bg-secondary-50/60 transition">
                                    <td class="sticky left-0 z-10 bg-white py-3 pr-4 pl-6" x-text="row.seq"></td>

                                    <td class="py-3 pr-4">
                                        <div class="flex items-center gap-2">
                                            <code class="text-xs text-secondary-700" x-text="row.uid"></code>
                                            <button @click="copy(row.uid)"
                                                class="rounded-lg border border-secondary-300 bg-white px-2 py-0.5 text-[11px] text-secondary-600 hover:bg-secondary-50">
                                                Copy
                                            </button>
                                        </div>
                                    </td>

                                    <td class="py-3 pr-4" x-text="row.name"></td>
                                    <td class="hidden py-3 pr-4 md:table-cell" x-text="row.email"></td>

                                    <td class="py-3 pr-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                            :class="row.status === 'submitted' ?
                                                'bg-success-100 text-success-700' :
                                                'bg-warning-100 text-warning-700'">
                                            <span x-text="row.status"></span>
                                        </span>
                                    </td>

                                    <td class="hidden py-3 pr-4 lg:table-cell" x-text="row.submitted_at || '-'"></td>
                                    <td class="hidden py-3 pr-4 md:table-cell" x-text="row.duration"></td>

                                    <td class="py-3 pr-6">
                                        <div class="flex items-center gap-2">
                                            <a :href="row.show_url"
                                                class="inline-flex items-center rounded-xl border border-secondary-300 bg-white px-3 py-1.5 text-sm font-medium text-secondary-700 hover:bg-secondary-50 transition">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-secondary-200">
                    {{ $responses->links() }}
                </div>

            </div>

            <div class="rounded-2xl border border-primary-100 bg-white shadow-soft p-6">
                <h3 class="mb-2 font-semibold text-secondary-900">Tips</h3>
                <ul class="list-inside list-disc space-y-1 text-sm text-secondary-600">
                    <li>Gunakan <strong>Export CSV</strong> untuk mengambil semua respons.</li>
                    <li>Durasi dihitung dari <em>started_at</em> ke <em>submitted_at</em> (jika tersedia).</li>
                    <li>Filter di atas bekerja untuk data pada halaman saat ini.</li>
                </ul>
            </div>

        </div>
    </div>

    <script>
        function respIndex(cfg) {
            return {
                rows: cfg.rows || [],
                firstSeq: cfg.firstSeq || 1,
                q: '',
                status: '',
                dFrom: '',
                dTo: '',
                statuses: [],
                init() {
                    this.rows.forEach((r, i) => r.seq = this.firstSeq + i);
                    const set = new Set(this.rows.map(r => r.status).filter(Boolean));
                    this.statuses = Array.from(set);
                },
                get filtered() {
                    let arr = this.rows.slice();
                    const q = (this.q || '').toLowerCase().trim();
                    if (q) {
                        arr = arr.filter(r =>
                            String(r.uid).toLowerCase().includes(q) ||
                            String(r.name).toLowerCase().includes(q) ||
                            String(r.email).toLowerCase().includes(q)
                        );
                    }
                    if (this.status) {
                        arr = arr.filter(r => r.status === this.status);
                    }
                    if (this.dFrom) {
                        const from = new Date(this.dFrom + 'T00:00:00');
                        arr = arr.filter(r => r.submitted_at ? (new Date(r.submitted_at) >= from) : false);
                    }
                    if (this.dTo) {
                        const to = new Date(this.dTo + 'T23:59:59');
                        arr = arr.filter(r => r.submitted_at ? (new Date(r.submitted_at) <= to) : false);
                    }
                    arr.sort((a, b) => {
                        const da = a.submitted_at ? new Date(a.submitted_at).getTime() : 0;
                        const db = b.submitted_at ? new Date(b.submitted_at).getTime() : 0;
                        return db - da;
                    });
                    arr.forEach((r, i) => r.seq = this.firstSeq + i);
                    return arr;
                },
                resetFilters() {
                    this.q = '';
                    this.status = '';
                    this.dFrom = '';
                    this.dTo = '';
                },
                async copy(text) {
                    try {
                        await navigator.clipboard.writeText(String(text || ''));
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>
