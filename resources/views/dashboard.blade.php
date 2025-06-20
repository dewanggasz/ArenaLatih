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
            activeFilter: 'all', 
            allTests: {{ Js::from($tests) }},
            completedTestIds: {{ Js::from($completedTestIds) }},
            progress: {},
            currentUserId: {{ Auth::id() }},

            init() {
                this.progress = {}; 
                this.allTests.forEach(test => {
                    try {
                        const savedTime = localStorage.getItem(`exam_time_user_${this.currentUserId}_${test.id}`);
                        
                        if (savedTime) {
                            const timeValue = parseInt(savedTime, 10);
                            if (!isNaN(timeValue) && timeValue > 0 && !this.isCompleted(test.id)) {
                                this.progress[test.id] = { timeLeft: timeValue };
                            }
                        }
                    } catch (e) {
                        console.error(`Gagal memproses progres untuk tes ID: ${test.id}`, e);
                    }
                });
            },

            isCompleted(testId) {
                return this.completedTestIds.includes(testId);
            },

            inProgress(testId) {
                return this.progress.hasOwnProperty(testId);
            },

            get filteredTests() {
                if (this.activeFilter === 'all') {
                    return this.allTests;
                }
                return this.allTests.filter(test => test.sub_category && test.sub_category.category_id == this.activeFilter);
            },

            formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
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

            <div class="border-b border-gray-200 mb-8">
                <nav class="flex" aria-label="Tabs">
                    <button @click="activeTab = 'latihan'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'latihan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'latihan' }" class="flex-1 whitespace-nowrap text-center py-4 px-1 border-b-2 font-medium text-sm sm:text-base transition-colors duration-200 focus:outline-none">
                        Paket Latihan
                    </button>
                    <button @click="activeTab = 'history'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'history' }" class="flex-1 whitespace-nowrap text-center py-4 px-1 border-b-2 font-medium text-sm sm:text-base transition-colors duration-200 focus:outline-none">
                        Riwayat Latihan
                    </button>
                </nav>
            </div>

            <div>
                <div x-show="activeTab === 'latihan'" x-transition.opacity.duration.500ms>
                    
                    <div class="mb-6 flex items-center gap-2 overflow-x-auto pb-2">
                        <button @click="activeFilter = 'all'" :class="{ 'bg-indigo-600 text-white': activeFilter === 'all', 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300': activeFilter !== 'all' }" class="flex-shrink-0 px-4 py-2 text-sm font-semibold rounded-full shadow-sm transition-colors duration-200">
                            Semua
                        </button>
                        @foreach ($categories as $category)
                            <button @click="activeFilter = {{ $category->id }}" :class="{ 'bg-indigo-600 text-white': activeFilter == {{ $category->id }}, 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300': activeFilter != {{ $category->id }} }" class="flex-shrink-0 px-4 py-2 text-sm font-semibold rounded-full shadow-sm transition-colors duration-200">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <template x-for="test in filteredTests" :key="test.id">
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-lg shadow-slate-200/50 p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:-translate-y-1 h-full">
                                <div>
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-lg font-bold text-slate-800" x-text="test.title"></h4>
                                        <span class="flex-shrink-0 text-xs font-bold px-2.5 py-1 rounded-full" 
                                              :class="{
                                                  'bg-green-100 text-green-800': isCompleted(test.id),
                                                  'bg-amber-100 text-amber-800': inProgress(test.id),
                                                  'bg-slate-100 text-slate-700': !isCompleted(test.id) && !inProgress(test.id)
                                              }"
                                              x-text="isCompleted(test.id) ? 'Selesai' : (inProgress(test.id) ? 'On Progress' : 'Belum Dimulai')">
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-5 line-clamp-2" x-text="test.description"></p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                                        <div class="flex items-center gap-1.5" :class="{'text-red-600 font-bold': inProgress(test.id)}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 10.586V6z" clip-rule="evenodd" /></svg>
                                            <span x-text="inProgress(test.id) ? `Sisa Waktu: ${formatTime(progress[test.id].timeLeft)}` : `Durasi: ${test.duration_minutes} menit`"></span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
                                            <span class="font-medium" x-text="`${test.questions_count} Soal`"></span>
                                        </div>
                                    </div>
                                    <a :href="isCompleted(test.id) ? `{{ url('/test') }}/${test.id}` : (inProgress(test.id) ? `{{ url('/test') }}/${test.id}` : `{{ url('/test') }}/${test.id}/start`)"
                                       class="w-full inline-block text-center text-white font-bold py-2.5 px-4 rounded-lg transition duration-300 shadow-md hover:shadow-lg"
                                       :class="{
                                            'bg-blue-600 hover:bg-blue-700': isCompleted(test.id),
                                            'bg-amber-500 hover:bg-amber-600': inProgress(test.id),
                                            'bg-indigo-600 hover:bg-indigo-700': !isCompleted(test.id) && !inProgress(test.id)
                                       }"
                                       x-text="isCompleted(test.id) ? 'Lihat Pembahasan' : (inProgress(test.id) ? 'Lanjutkan Pengerjaan' : 'Mulai Kerjakan')">
                                    </a>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredTests.length === 0">
                            <div class="md:col-span-2 xl:col-span-3 text-center text-slate-500 py-10">
                                <p>Tidak ada paket latihan yang tersedia untuk kategori ini.</p>
                            </div>
                        </template>
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
