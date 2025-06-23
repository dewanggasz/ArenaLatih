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

    <div x-data="{ 
            activeTab: 'latihan',
            categoryFilter: 'all', 
            statusFilter: 'all',
            isCategoryOpen: false,
            allTests: {{ Js::from($tests) }},
            allCategories: {{ Js::from($categories) }},
            userResults: {{ Js::from($userResults) }},

            get activeCategoryName() {
                if (this.categoryFilter === 'all') {
                    return 'Semua Kategori';
                }
                const category = this.allCategories.find(c => c.id == this.categoryFilter);
                return category ? category.name : 'Semua Kategori';
            },

            getTestStatus(testId) {
                const result = this.userResults.find(r => r.test_id === testId);
                if (!result) return 'not_started';
                return result.status;
            },

            get filteredTests() {
                return this.allTests.filter(test => {
                    // Pertama, cek filter kategori
                    const categoryMatch = this.categoryFilter === 'all' || (test.sub_category && test.sub_category.category_id == this.categoryFilter);
                    
                    // Jika kategori tidak cocok, langsung sembunyikan
                    if (!categoryMatch) {
                        return false;
                    }

                    // Kedua, cek filter status
                    const status = this.getTestStatus(test.id);

                    // Jika filter 'semua', tampilkan
                    if (this.statusFilter === 'all') {
                        return true;
                    }
                    
                    // Jika tidak, tampilkan HANYA jika status tes sama persis dengan filter yang dipilih
                    // Ini akan menangani 'not_started', 'in_progress', dan 'completed' dengan benar.
                    return status === this.statusFilter;
                });
            }
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
                    <button @click="activeTab = 'latihan'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'latihan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'latihan' }" class="flex-1 whitespace-nowrap text-center py-4 px-1 border-b-2 font-medium text-sm sm:text-base">
                        Paket Latihan
                    </button>
                    <button @click="activeTab = 'history'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'history' }" class="flex-1 whitespace-nowrap text-center py-4 px-1 border-b-2 font-medium text-sm">
                        Riwayat Latihan
                    </button>
                </nav>
            </div>

            <div>
                <div x-show="activeTab === 'latihan'" x-transition.opacity.duration.500ms>
                    
                    {{-- UI FILTER DIDESAIN ULANG --}}
                    <div class="mb-8 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="category-filter-button" class="text-sm font-semibold text-slate-600 block mb-2">Kategori Latihan</label>
                                <div class="relative" @click.away="isCategoryOpen = false">
                                    <button @click="isCategoryOpen = !isCategoryOpen" id="category-filter-button" class="w-full flex items-center justify-between text-left bg-white border-2 border-slate-200 rounded-lg shadow-sm text-slate-700 font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-150 py-2.5 px-4">
                                        <span x-text="activeCategoryName"></span>
                                        <svg class="w-5 h-5 text-slate-400 ml-2 transition-transform duration-200" :class="{ 'rotate-180': isCategoryOpen }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </button>
                                    <div x-show="isCategoryOpen" x-transition class="absolute z-10 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" x-cloak>
                                        <div class="py-1">
                                            <a href="#" @click.prevent="categoryFilter = 'all'; isCategoryOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Semua Kategori</a>
                                            @foreach ($categories as $category)
                                                <a href="#" @click.prevent="categoryFilter = {{ $category->id }}; isCategoryOpen = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ $category->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-600 block mb-2">Status Pengerjaan</label>
                                <div class="flex bg-slate-200 p-1 rounded-lg">
                                    <button @click="statusFilter = 'all'" :class="{ 'bg-white shadow': statusFilter === 'all', 'text-slate-600': statusFilter !== 'all' }" class="flex-1 px-4 py-2 text-xs font-semibold rounded-md transition-colors duration-200">
                                        Semua
                                    </button>
                                    <button @click="statusFilter = 'not_started'" :class="{ 'bg-white shadow': statusFilter === 'not_started', 'text-slate-600': statusFilter !== 'not_started' }" class="flex-1 px-4 py-2 text-xs font-semibold rounded-md transition-colors duration-200">
                                        Baru
                                    </button>
                                    <button @click="statusFilter = 'in_progress'" :class="{ 'bg-white shadow': statusFilter === 'in_progress', 'text-slate-600': statusFilter !== 'in_progress' }" class="flex-1 px-4 py-2 text-xs font-semibold rounded-md transition-colors duration-200">
                                        Tertunda
                                    </button>
                                    <button @click="statusFilter = 'completed'" :class="{ 'bg-white shadow': statusFilter === 'completed', 'text-slate-600': statusFilter !== 'completed' }" class="flex-1 px-4 py-2 text-xs font-semibold rounded-md transition-colors duration-200">
                                        Selesai
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Daftar Latihan dengan Desain Kartu Baru --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 px-2">
                        <template x-for="test in filteredTests" :key="test.id">
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-lg p-6 flex flex-col justify-between h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                                <div>
                                    <div class="flex justify-between items-start mb-4">
                                        <span x-text="test.sub_category ? test.sub_category.name : 'Umum'" class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full"></span>
                                        <span class="flex-shrink-0 text-xs font-bold px-2.5 py-1 rounded-full" 
                                              :class="{
                                                'bg-green-100 text-green-800': getTestStatus(test.id) === 'completed',
                                                'bg-amber-100 text-amber-800': getTestStatus(test.id) === 'in_progress',
                                                'bg-slate-100 text-slate-700': getTestStatus(test.id) === 'not_started'
                                              }"
                                              x-text="getTestStatus(test.id) === 'completed' ? 'Selesai' : (getTestStatus(test.id) === 'in_progress' ? 'On Progress' : 'Belum Dimulai')">
                                        </span>
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-800" x-text="test.title"></h4>
                                    <p class="text-sm text-slate-600 mt-2 line-clamp-2" x-text="test.description"></p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-200">
                                    <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 10.586V6z" clip-rule="evenodd" /></svg>
                                            <span x-text="`${test.duration_minutes} menit`"></span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
                                            <span class="font-medium" x-text="`${test.questions_count} Soal`"></span>
                                        </div>
                                    </div>
                                    <a :href="getTestStatus(test.id) === 'not_started' ? `{{ url('/test') }}/${test.id}/begin` : `{{ url('/test') }}/${test.id}`"
                                       class="w-full inline-block text-center text-white font-bold py-2.5 px-4 rounded-lg transition shadow-md hover:shadow-lg"
                                       :class="{
                                            'bg-blue-600 hover:bg-blue-700': getTestStatus(test.id) === 'completed',
                                            'bg-amber-500 hover:bg-amber-600': getTestStatus(test.id) === 'in_progress',
                                            'bg-indigo-600 hover:bg-indigo-700': getTestStatus(test.id) === 'not_started'
                                       }"
                                       x-text="getTestStatus(test.id) === 'completed' ? 'Lihat Pembahasan' : (getTestStatus(test.id) === 'in_progress' ? 'Lanjutkan' : 'Mulai Kerjakan')">
                                    </a>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredTests.length === 0">
                            <div class="md:col-span-2 xl:col-span-3 text-center text-slate-500 py-10">
                                <p>Tidak ada paket latihan yang sesuai dengan filter Anda.</p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- KONTEN UNTUK TAB "RIWAYAT LATIHAN" --}}
                <div x-show="activeTab === 'history'" x-transition.opacity.duration.500ms>
                    <div class="bg-white overflow-hidden shadow-xl shadow-slate-200/50 rounded-2xl">
                        <div class="p-6 md:p-8">
                            @if($completedResults->isEmpty())
                                <p class="text-center text-slate-500 py-10">Anda belum pernah menyelesaikan latihan.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($completedResults as $result)
                                        <div class="p-4 border border-slate-200 rounded-xl transition-shadow duration-300 hover:shadow-md">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $result->test->title }}</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">{{ $result->created_at->format('d M Y, H:i') }}</p>
                                                </div>
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
