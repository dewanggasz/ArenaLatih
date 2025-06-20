<x-app-layout>
    {{-- Style untuk notifikasi kustom dan styling lainnya --}}
    <style>
        html { scroll-behavior: smooth; }
        .exam-choice-label:has(input:checked) {
            border-color: #4338ca;
            background-color: #e0e7ff;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        .prose { max-width: 100% !important; }

        /* Style untuk notifikasi "Copy-Paste Diblokir" */
        #copy-paste-alert {
            position: fixed;
            bottom: -100px; /* Mulai dari luar layar */
            left: 50%;
            transform: translateX(-50%);
            background-color: #ef4444; /* red-500 */
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 9999px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            transition: bottom 0.5s ease-in-out, opacity 0.5s ease-in-out;
            opacity: 0;
        }
        #copy-paste-alert.show {
            bottom: 2rem; /* Muncul ke atas */
            opacity: 1;
        }
    </style>

    {{-- Kita kosongkan header bawaan dan akan membuat header kustom kita sendiri di bawah --}}
    <x-slot name="header">
    </x-slot>

    {{-- PANEL STATUS YANG MENEMPEL DI ATAS (STICKY) --}}
    @if(!$isReviewMode)
    <div id="sticky-status-panel" class="sticky top-0 z-20 bg-white/95 backdrop-blur-sm border-b border-slate-200 transition-transform duration-300 ease-in-out">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap sm:flex-nowrap justify-between items-center py-3 gap-x-4 gap-y-2">
                <div class="flex items-center gap-4 flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-800" title="Kembali ke Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>
                    <h1 class="text-lg font-semibold text-slate-800 hidden md:block">{{ $test->title }}</h1>
                </div>
                <div class="w-full sm:w-auto sm:flex-1 sm:max-w-sm order-3 sm:order-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-medium text-slate-600">Progress</span>
                        <span id="progress-text" class="text-xs font-bold text-indigo-600">0 / {{ $test->questions->count() }}</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2.5">
                        <div id="progress-bar" class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
                <div id="timer-container" class="text-xl font-bold bg-slate-800 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 flex-shrink-0 order-2 sm:order-3 transition-colors duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="timer">{{ sprintf('%02d', $test->duration_minutes) }}:00</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <main id="exam-content-area" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        @if($isReviewMode)
            <div class="mb-8 text-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-slate-600 hover:text-indigo-600 font-medium inline-flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    Kembali ke Dashboard
                </a>
                <h2 class="text-3xl font-bold text-slate-800 leading-tight">
                    {{ $test->title }}
                </h2>
                <p class="text-center text-lg mt-2 text-blue-600 font-semibold">Mode Pembahasan</p>
            </div>
        @endif

        <form id="exam-form" method="POST" action="{{ route('test.submit', $test) }}">
            @csrf
            <div class="bg-white overflow-hidden shadow-2xl shadow-slate-200 sm:rounded-2xl p-6 md:p-10 space-y-12">
                @foreach($test->questions as $index => $question)
                    <div class="pb-10 border-b border-slate-200 last:border-b-0">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-lg">{{ $index + 1 }}</div>
                            <div class="text-lg font-medium text-slate-800 flex-1 pt-1.5">{!! $question->question_text !!}</div>
                        </div>
                        
                        <div class="ml-14">
                            @if ($question->type === 'pilihan_ganda')
                                <div class="space-y-3">
                                    @foreach($question->choices as $choice)
                                        @php
                                            $labelClass = 'border-slate-200 hover:border-indigo-400 hover:bg-indigo-50';
                                            if($isReviewMode) {
                                                $userAnswerId = $userAnswers->get($question->id);
                                                if ($choice->is_correct) $labelClass = 'bg-green-100 border-green-500 text-green-900 font-semibold';
                                                elseif ($choice->id == $userAnswerId) $labelClass = 'bg-red-100 border-red-500 text-red-900';
                                                else $labelClass = 'bg-slate-50 border-slate-200 opacity-70';
                                            }
                                        @endphp
                                        <label for="choice-{{ $choice->id }}" class="exam-choice-label flex items-center p-4 rounded-xl border-2 {{ $labelClass }} cursor-pointer transition-all duration-200">
                                            <input type="radio" id="choice-{{ $choice->id }}" name="answers[{{ $question->id }}]" value="{{ $choice->id }}" class="h-5 w-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 focus:ring-2 exam-input" data-question-id="{{ $question->id }}"
                                                @if($isReviewMode) disabled @if($choice->id == $userAnswers->get($question->id)) checked @endif @endif>
                                            <span class="ml-4 text-base">{{ $choice->choice_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @if($isReviewMode && $question->explanation)
                                    <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                                        <h4 class="font-bold text-blue-800 mb-2">Pembahasan:</h4>
                                        <div class="prose text-slate-700">{!! $question->explanation !!}</div>
                                    </div>
                                @endif

                            @elseif ($question->type === 'esai')
                                <div>
                                    <label class="font-semibold text-slate-700 mb-2 block">Jawaban Anda:</label>
                                    <textarea name="answers[{{ $question->id }}]" class="mt-1 w-full h-48 p-3 border-2 border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 exam-input @if($isReviewMode) bg-slate-100 @endif" placeholder="Ketik jawaban esai Anda di sini..." data-question-id="{{ $question->id }}" @if($isReviewMode) readonly @endif>{{ $isReviewMode ? $userEssayAnswers->get($question->id) : '' }}</textarea>
                                </div>
                                @if($isReviewMode && $aiFeedbacks->has($question->id))
                                    <div class="mt-6 p-4 bg-purple-50 border-l-4 border-purple-400 rounded-r-lg">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-bold text-purple-800">Pembahasan</h4>
                                            <span class="font-bold text-lg text-purple-800 bg-purple-200 px-3 py-1 rounded-full">{{ $aiScores->get($question->id) ?? 'N/A' }} / 10</span>
                                        </div>
                                        <div class="prose text-slate-700 mt-2">{!! nl2br(e($aiFeedbacks->get($question->id))) !!}</div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="mt-10 flex justify-end">
                     @if(!$isReviewMode)
                        <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            Selesai & Kirim Jawaban
                        </button>
                    @else
                         <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5">
                            Kembali ke Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </main>

    <div id="copy-paste-alert" class="hidden">Aktivitas menyalin/menempel tidak diizinkan.</div>

    @if(!$isReviewMode)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const testId = {{ $test->id }};
                const totalQuestions = {{ $test->questions->count() }};
                const durationInMinutes = {{ $test->duration_minutes }};
                const timerDisplay = document.getElementById('timer');
                const examForm = document.getElementById('exam-form');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                const timerContainer = document.getElementById('timer-container');
                const examArea = document.getElementById('exam-content-area');
                const notification = document.getElementById('copy-paste-alert');
                let notificationTimeout;
                
                const timeKey = `exam_time_user_{{ Auth::id() }}_${testId}`;
                const answersKey = `exam_answers_user_{{ Auth::id() }}_${testId}`;

                function updateProgress() {
                    const savedAnswers = JSON.parse(localStorage.getItem(answersKey) || '{}');
                    const answeredCount = Object.keys(savedAnswers).filter(key => savedAnswers[key] && savedAnswers[key].trim() !== '').length;
                    const percentage = totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;
                    if(progressBar) progressBar.style.width = `${percentage}%`;
                    if(progressText) progressText.textContent = `${answeredCount} / ${totalQuestions} Soal`;
                }

                function loadProgress() {
                    const savedTime = localStorage.getItem(timeKey);
                    let initialTime = savedTime ? parseInt(savedTime, 10) : durationInMinutes * 60;
                    const savedAnswers = JSON.parse(localStorage.getItem(answersKey) || '{}');
                    for (const questionId in savedAnswers) {
                        const answerValue = savedAnswers[questionId];
                        const radioInput = document.querySelector(`input[name="answers[${questionId}]"][value="${answerValue}"]`);
                        const textareaInput = document.querySelector(`textarea[name="answers[${questionId}]"]`);
                        if (radioInput) radioInput.checked = true;
                        else if (textareaInput) textareaInput.value = answerValue;
                    }
                    updateProgress();
                    return initialTime;
                }
                
                let timer = loadProgress();

                const interval = setInterval(function () {
                    if (timer <= 0) {
                        clearInterval(interval);
                        alert('Waktu habis! Jawaban Anda akan dikirim secara otomatis.');
                        examForm.submit();
                        return;
                    }
                    timer--;
                    const minutes = Math.floor(timer / 60);
                    const seconds = timer % 60;
                    if(timerDisplay) timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    localStorage.setItem(timeKey, timer);

                    if (timerContainer) {
                        if (timer <= 300) {
                            if (!timerContainer.classList.contains('bg-red-600')) {
                                timerContainer.classList.remove('bg-slate-800');
                                timerContainer.classList.add('bg-red-600', 'animate-pulse');
                            }
                        } else {
                            if (timerContainer.classList.contains('bg-red-600')) {
                                timerContainer.classList.remove('bg-red-600', 'animate-pulse');
                                timerContainer.classList.add('bg-slate-800');
                            }
                        }
                    }
                }, 1000);

                document.querySelectorAll('.exam-input').forEach(input => {
                    const eventType = input.type === 'radio' ? 'change' : 'input';
                    input.addEventListener(eventType, function() {
                        const savedAnswers = JSON.parse(localStorage.getItem(answersKey) || '{}');
                        if (this.value && this.value.trim() !== '') {
                            savedAnswers[this.dataset.questionId] = this.value;
                        } else {
                            delete savedAnswers[this.dataset.questionId];
                        }
                        localStorage.setItem(answersKey, JSON.stringify(savedAnswers));
                        updateProgress();
                    });
                });

                examForm.addEventListener('submit', function() {
                    localStorage.removeItem(timeKey);
                    localStorage.removeItem(answersKey);
                });

                const stickyPanel = document.getElementById('sticky-status-panel');
                if (stickyPanel) {
                    let lastScrollTop = 0;
                    window.addEventListener('scroll', function() {
                        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        if (scrollTop > lastScrollTop && scrollTop > stickyPanel.offsetHeight) { 
                            stickyPanel.classList.add('-translate-y-full');
                        } else {
                            stickyPanel.classList.remove('-translate-y-full');
                        }
                        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                    }, false);
                }

                function showCopyPasteNotification() {
                    notification.classList.remove('hidden');
                    notification.classList.add('show');
                    clearTimeout(notificationTimeout);
                    notificationTimeout = setTimeout(() => {
                        notification.classList.remove('show');
                         setTimeout(() => notification.classList.add('hidden'), 500);
                    }, 3000);
                }

                if (examArea) {
                    examArea.addEventListener('contextmenu', function (e) {
                        e.preventDefault();
                        showCopyPasteNotification();
                    });
                    examArea.addEventListener('copy', function (e) {
                        e.preventDefault();
                        showCopyPasteNotification();
                    });
                    examArea.addEventListener('paste', function (e) {
                        e.preventDefault();
                        showCopyPasteNotification();
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
