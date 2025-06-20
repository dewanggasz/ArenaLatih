<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes progress-circle {
            from { stroke-dashoffset: 628; }
        }
        .progress-circle-bar {
            animation: progress-circle 1.5s ease-out forwards;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Latihan Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl shadow-slate-200/50 sm:rounded-2xl">
                <div class="p-8 md:p-10 text-center text-slate-800">
                    
                    @php
                        $congratsMessage = 'Terus Berlatih!';
                        $congratsColor = 'text-amber-600';
                        if ($testResult->score >= 80) {
                            $congratsMessage = 'Luar Biasa!';
                            $congratsColor = 'text-green-600';
                        } elseif ($testResult->score >= 60) {
                            $congratsMessage = 'Usaha yang Bagus!';
                            $congratsColor = 'text-blue-600';
                        }
                        
                        // PERUBAHAN DI SINI: Menggunakan rute 'test.share' yang benar
                        $shareText = rawurlencode("Saya baru saja menyelesaikan latihan \"{$testResult->test->title}\" di ArenaLatih dan mendapatkan skor {$testResult->score}! Yuk, asah kemampuanmu juga!");
                        $shareUrl = rawurlencode(route('test.share', $testResult));
                    @endphp

                    <h3 class="text-3xl font-bold {{ $congratsColor }} fade-in">{{ $congratsMessage }}</h3>
                    <p class="text-slate-600 mt-2 mb-8 fade-in" style="animation-delay: 100ms;">Anda telah menyelesaikan latihan ini dengan baik.</p>

                    <div class="relative inline-block mb-10 fade-in" style="animation-delay: 200ms;">
                        <svg class="w-52 h-52 transform -rotate-90" viewBox="0 0 220 220">
                            <circle cx="110" cy="110" r="100" stroke="#e5e7eb" stroke-width="15" fill="transparent" />
                            <circle
                                class="progress-circle-bar text-indigo-600"
                                cx="110" cy="110" r="100"
                                stroke="currentColor" stroke-width="15" fill="transparent" stroke-linecap="round"
                                stroke-dasharray="628.32"
                                stroke-dashoffset="{{ 628.32 - (628.32 * $testResult->score) / 100 }}"
                            />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-6xl font-bold text-indigo-600 tracking-tight">{{ $testResult->score }}</span>
                            <p class="text-sm font-semibold text-slate-500 -mt-1">SKOR AKHIR</p>
                        </div>
                    </div>

                    <div class="text-left max-w-md mx-auto bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4 fade-in" style="animation-delay: 300ms;">
                        <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                            <span class="text-slate-600 font-medium">Paket Latihan</span>
                            <span class="font-bold text-slate-800">{{ $testResult->test->title }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Total Soal</span>
                            <span class="font-semibold text-slate-700">{{ $testResult->questions_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Jawaban PG Benar</span>
                            <span class="font-semibold text-green-600">{{ $testResult->correct_answers_count }}</span>
                        </div>
                        @if ($essayCount > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Skor Rata-rata Esai</span>
                                <span class="font-semibold text-purple-600">{{ $averageEssayScore }} / 10</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4 fade-in" style="animation-delay: 400ms;">
                        <a href="{{ route('test.show', $testResult->test) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Lihat Pembahasan
                        </a>
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-block text-center text-slate-600 font-semibold py-3 px-6 rounded-lg hover:bg-slate-100 transition duration-300">
                            Kembali ke Dashboard
                        </a>
                    </div>
                    
                    <div class="mt-12 pt-8 border-t border-slate-200 fade-in" style="animation-delay: 500ms;">
                        <h4 class="font-semibold text-slate-700">Bagikan Hasil Anda!</h4>
                        <div class="mt-4 flex justify-center items-center space-x-6">
                            <a href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}" target="_blank" class="text-gray-400 hover:text-black transition-transform duration-300 transform hover:scale-110" title="Bagikan ke X/Twitter">
                                <i class="fab fa-twitter fa-2x"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" class="text-gray-400 hover:text-green-500 transition-transform duration-300 transform hover:scale-110" title="Bagikan ke WhatsApp">
                                <i class="fab fa-whatsapp fa-2x"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition-transform duration-300 transform hover:scale-110" title="Bagikan ke Facebook">
                                <i class="fab fa-facebook fa-2x"></i>
                            </a>
                             <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" class="text-gray-400 hover:text-blue-500 transition-transform duration-300 transform hover:scale-110" title="Bagikan ke Telegram">
                                <i class="fab fa-telegram fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
