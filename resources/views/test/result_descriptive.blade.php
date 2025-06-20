<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Latihan Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl shadow-slate-200/50 sm:rounded-2xl">
                <div class="p-8 md:p-10 text-center">
                    <p class="text-indigo-600 font-semibold">Hasil Anda:</p>
                    <h3 class="mt-2 text-4xl font-extrabold text-slate-800 tracking-tight">{{ $outcome->title ?? 'Tipe Tidak Dikenal' }}</h3>
                    <p class="mt-1 text-2xl font-bold text-slate-500">({{ $testResult->descriptive_outcome }})</p>

                    <div class="mt-8 text-left prose max-w-none prose-indigo">
                        {!! $outcome->description ?? '<p>Deskripsi untuk tipe hasil ini belum tersedia.</p>' !!}
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-block text-center bg-slate-600 hover:bg-slate-700 text-white font-bold py-3 px-6 rounded-lg">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
