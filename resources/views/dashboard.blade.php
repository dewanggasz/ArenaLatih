<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-6 md:p-8 rounded-2xl shadow-lg">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-white">
                    <h2 class="text-3xl font-bold">
                        Selamat datang, {{ Auth::user()->name }}!
                    </h2>
                    <p class="mt-1 text-blue-100 text-lg">Siap untuk menguji kemampuan hari ini?</p>
                </div>
                <div class="hidden md:block">
                    <svg class="w-24 h-24 text-white opacity-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 22h20L12 2zm0 4.55L17.52 20H6.48L12 6.55z"></path></svg>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- PERUBAHAN BESAR DI SINI: x-data sekarang jauh lebih sederhana --}}
    <div x-data="{ 
            activeTab: 'latihan',
            activeFilter: 'all'
         }" 
         class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-r-lg" role="alert">
                    <p class="font-bold">Informasi</p>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            {{-- Navigasi Tab --}}
            <div class="border-b border-gray-200 mb-8">
                <nav class="flex" aria-label="Tabs">
                    <button @click="activeTab = 'latihan'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'latihan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'latihan' }" class="flex-1 ...">
                        Paket Latihan
                    </button>
                    <button @click="activeTab = 'history'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'history' }" class="flex-1 ...">
                        Riwayat Latihan
                    </button>
                </nav>
            </div>

            <div>
                {{-- KONTEN TAB "PAKET LATIHAN" --}}
                <div x-show="activeTab === 'latihan'" x-transition.opacity.duration.500ms>
                    
                    {{-- Tombol Filter Kategori --}}
                    <div class="mb-6 flex items-center gap-2 overflow-x-auto pb-2">
                        <button @click="activeFilter = 'all'" :class="{ 'bg-indigo-600 text-white': activeFilter === 'all', 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300': activeFilter !== 'all' }" class="flex-shrink-0 px-4 py-2 text-sm font-semibold rounded-full shadow-sm">
                            Semua
                        </button>
                        @foreach ($categories as $category)
                            <button @click="activeFilter = {{ $category->id }}" :class="{ 'bg-indigo-600 text-white': activeFilter == {{ $category->id }}, 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300': activeFilter != {{ $category->id }} }" class="flex-shrink-0 px-4 py-2 text-sm font-semibold rounded-full shadow-sm">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Daftar Latihan Sekarang Menggunakan Logika dari Server --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse ($tests as $test)
                            @php
                                $result = $userResults->firstWhere('test_id', $test->id);
                                $status = 'Belum Dimulai';
                                $buttonText = 'Mulai Kerjakan';
                                $buttonLink = route('test.start', $test);
                                $statusColor = 'bg-slate-100 text-slate-700';
                                $buttonColor = 'bg-indigo-600 hover:bg-indigo-700';

                                if ($result) {
                                    if ($result->status === 'completed') {
                                        $status = 'Selesai';
                                        $buttonText = 'Lihat Pembahasan';
                                        $buttonLink = route('test.show', $test);
                                        $statusColor = 'bg-green-100 text-green-800';
                                        $buttonColor = 'bg-blue-600 hover:bg-blue-700';
                                    } elseif ($result->status === 'in_progress') {
                                        $status = 'On Progress';
                                        $buttonText = 'Lanjutkan Pengerjaan';
                                        $buttonLink = route('test.show', $test);
                                        $statusColor = 'bg-amber-100 text-amber-800';
                                        $buttonColor = 'bg-amber-500 hover:bg-amber-600';
                                    }
                                }
                            @endphp
                            <div x-show="activeFilter === 'all' || ({{ $test->subCategory->category_id ?? 'null' }} == activeFilter)" x-transition.duration.300ms>
                                <div class="bg-white border border-slate-200 rounded-2xl shadow-lg p-6 flex flex-col justify-between h-full">
                                    <div>
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="text-lg font-bold text-slate-800">{{ $test->title }}</h4>
                                            <span class="flex-shrink-0 text-xs font-bold px-2.5 py-1 rounded-full {{ $statusColor }}">{{ $status }}</span>
                                        </div>
                                        <p class="text-sm text-slate-600 mb-5 line-clamp-2">{{ $test->description }}</p>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-slate-200">
                                        <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                                            {{-- ... (info durasi dan jumlah soal) ... --}}
                                        </div>
                                        <a href="{{ $buttonLink }}" class="w-full inline-block text-center text-white font-bold py-2.5 px-4 rounded-lg transition {{ $buttonColor }} shadow-md hover:shadow-lg">
                                            {{ $buttonText }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="md:col-span-2 xl:col-span-3 text-center text-slate-500 py-10">
                                <p>Tidak ada paket latihan yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div x-show="activeTab === 'history'" x-transition.opacity.duration.500ms>
                    <div class="bg-white overflow-hidden shadow-xl shadow-slate-200/50 rounded-2xl">
                        <div class="p-6 md:p-8">
                            @if($userResults->isEmpty())
                                <p class="text-center text-slate-500 py-10">Anda belum pernah mengerjakan latihan.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($userResults as $result)
                                        <div class="p-4 border border-slate-200 rounded-xl transition-shadow duration-300 hover:shadow-md">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $result->test->title }}</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">{{ $result->created_at->format('d M Y, H:i') }}</p>
                                                </div>
                                                {{-- PERBAIKAN LOGIKA DI SINI --}}
                                                <div class="text-right">
                                                    @if($result->test->result_type === 'numeric')
                                                        <p class="font-bold text-3xl text-indigo-600">{{ $result->score }}</p>
                                                        <p class="text-xs text-slate-500 -mt-1">Skor</p>
                                                    @else
                                                        <p class="font-bold text-xl text-indigo-600">{{ $result->descriptive_outcome }}</p>
                                                        <p class="text-xs text-slate-500">Hasil</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-slate-200">
                                                <a href="{{ route('test.result', $result) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Lihat Rincian Hasil &rarr;</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
