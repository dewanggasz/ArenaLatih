{{-- File: resources/views/test/result_descriptive.blade.php --}}

<x-app-layout>
    {{-- PERUBAHAN DI SINI: Menambahkan styling khusus untuk konten deskripsi --}}
    <style>
        .prose h4 {
            font-size: 1.125rem; /* text-lg */
            font-weight: 700; /* font-bold */
            color: #1e293b; /* slate-800 */
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        .prose p, .prose ul {
            margin-top: 0.5em;
            margin-bottom: 1em;
            color: #475569; /* slate-600 */
        }
        .prose ul {
            list-style-type: dot;
            list-style-position: outside;
            padding-left: 3rem;
        }
        .prose li {
            margin-top: 0.5em;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Latihan Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl shadow-slate-200/50 sm:rounded-2xl">
                <div class="p-8 md:p-10 text-center">
                    <p class="text-indigo-600 font-semibold">Hasil Anda</p>
                    {{-- Tampilkan judul dan kode tipe, jika ada --}}
                    <h3 class="mt-2 text-4xl font-extrabold text-slate-800 tracking-tight">{{ $outcome->title ?? 'Tipe Tidak Dikenal' }}</h3>
                    <p class="mt-1 text-2xl font-bold text-slate-500">({{ $testResult->descriptive_outcome }})</p>
                    
                    {{-- Menggunakan {!! !!} dan kelas 'prose' untuk merender HTML yang rapi --}}
                    <div class="mt-8 text-left prose max-w-none prose-indigo">
                        {!! $outcome->description ?? '<p>Deskripsi untuk tipe hasil ini belum tersedia. Silakan hubungi admin untuk melengkapi data tes ini.</p>' !!}
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
