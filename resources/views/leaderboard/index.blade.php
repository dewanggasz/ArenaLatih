<x-app-layout>
    {{-- Kita akan membuat header kustom kita sendiri di dalam body untuk kontrol penuh --}}
    <x-slot name="header">
    </x-slot>

    <div class="py-12" style="padding: 2rem;">
        {{-- Satu x-data untuk mengontrol semua komponen di bawahnya --}}
        <div x-data="{ 
                open: false, 
                activeTab: '{{ array_key_first($leaderboards->all()) ?? '' }}',
                get activeTitle() { return this.activeTab; }
             }" 
             class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($leaderboards->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <p class="p-8 text-center text-slate-500">Belum ada data peringkat yang tersedia.</p>
                </div>
            @else
                {{-- HEADER KUSTOM BARU --}}
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8">
                    {{-- Judul di Kiri --}}
                    <div class="flex items-center gap-4">
                        <div class="p-2 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-9.375c0-1.036-.84-1.875-1.875-1.875h-5.25c-1.036 0-1.875.84-1.875 1.875v9.375m9 0a3 3 0 00-3-3h-3a3 3 0 00-3 3m.375 0h-.75M12 12.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                            {{ __('Papan Peringkat') }}
                        </h2>
                    </div>
                    
                    {{-- Dropdown Kustom di Kanan --}}
                    <div class="relative w-full sm:w-auto" @click.away="open = false">
                        {{-- Tombol Dropdown yang Terlihat --}}
                        <button @click="open = !open" class="w-full sm:w-72 flex items-center justify-between text-left bg-white border-2 border-slate-200 rounded-lg shadow-sm text-slate-700 font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-150 py-2.5 px-4">
                            <span x-text="activeTitle"></span>
                            <svg class="w-5 h-5 text-slate-400 ml-4 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        {{-- Daftar Opsi Dropdown yang Tersembunyi --}}
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute z-10 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                             x-cloak>
                            <div class="py-1">
                                @foreach($leaderboards as $testTitle => $results)
                                    <button @click="activeTab = '{{ $testTitle }}'; open = false" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        {{ $testTitle }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KONTEN PERINGKAT --}}
                @foreach($leaderboards as $testTitle => $results)
                    <div x-show="activeTab === '{{ $testTitle }}'" x-transition.opacity.duration.300ms>
                        <div class="bg-white overflow-hidden shadow-xl shadow-slate-200/50 rounded-2xl">
                            <table class="w-full table-auto">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">#</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Peserta</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 hidden md:table-cell">Waktu</th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Skor</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($results as $index => $result)
                                        <tr class="transition-colors duration-200 @if($result->user_id == Auth::id()) bg-indigo-50 @else hover:bg-slate-50 @endif">
                                            <td class="px-6 py-4 text-center">
                                                @if($index == 0)
                                                    <span class="text-3xl" title="Peringkat 1">🥇</span>
                                                @elseif($index == 1)
                                                    <span class="text-3xl" title="Peringkat 2">🥈</span>
                                                @elseif($index == 2)
                                                    <span class="text-3xl" title="Peringkat 3">🥉</span>
                                                @else
                                                    <span class="font-bold text-lg text-slate-500">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 bg-slate-200 rounded-full flex items-center justify-center">
                                                        <span class="font-bold text-slate-500">{{ strtoupper(substr($result->user->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-slate-900">{{ $result->user->name }}</div>
                                                        <div class="text-xs text-slate-500">{{ $result->user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm text-slate-500 hidden md:table-cell">
                                                {{ $result->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="text-xl font-extrabold text-indigo-600">{{ $result->score }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
