<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Halo, {{ auth()->user()->name }}</h3>

                    @if (auth()->user()->isAdmin())
                        <p class="text-gray-600 mb-4">
                            Kamu admin. Pergi ke halaman admin untuk mengelola form.
                        </p>
                        <a href="{{ route('admin.home') }}"
                            class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded-lg">
                            Buka Halaman Admin
                        </a>
                    @else
                        <p class="text-gray-600 mb-4">
                            Kamu respondent. Ketika ada form dibagikan, kamu bisa mengisinya melalui tautan publik
                            (tetap harus login). Di bawah ini nantinya akan muncul daftar respons milikmu.
                        </p>

                        @if (Route::has('my.responses'))
                            <a href="{{ route('my.responses') }}"
                                class="inline-flex px-4 py-2 bg-gray-900 text-white rounded-lg">
                                Respons Saya
                            </a>
                        @else
                            <a href="{{ url('/') }}"
                                class="inline-flex px-4 py-2 bg-gray-100 text-gray-900 rounded-lg">
                                Kembali ke Beranda
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
