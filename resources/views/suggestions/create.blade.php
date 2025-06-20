<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class=" bg-teal-100 rounded-lg">
                <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.375a6.002 6.002 0 004.243-1.757l3.256 3.256a.75.75 0 101.06-1.06l-3.256-3.256A6 6 0 1012 18.375zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v.01" />
                </svg>
            </div>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Kirim Saran & Masukan') }}
            </h2>
        </div>
    </x-slot>

    <div style="padding: 2rem;" class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- PERUBAHAN BESAR DI SINI: Notifikasi sekarang menjadi Overlay --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" @keydown.escape.window="show = false" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    {{-- Latar Belakang Gelap --}}
                    <div @click="show = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm" x-show="show"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                    {{-- Kartu Notifikasi --}}
                    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl text-center p-8" x-show="show"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                        {{-- Ikon Sukses --}}
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                            <svg class="h-10 w-10 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h3 class="mt-5 text-2xl font-bold text-slate-900">Terima Kasih!</h3>
                        <div class="mt-2 text-base text-slate-600">
                            <p>{{ session('success') }}</p>
                        </div>
                        <div class="mt-8">
                            <button @click="show = false" type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            {{-- AKHIR BAGIAN NOTIFIKASI BARU --}}

            <div class="bg-white overflow-hidden shadow-xl shadow-slate-200/50 sm:rounded-2xl rounded-lg p-4">
                <div class="p-8 md:p-8">

                    <p class="text-slate-600 mb-6">Punya ide untuk membuat ArenaLatih lebih baik, menemukan bug, atau ingin meminta fitur baru? Kami sangat menghargai semua masukan Anda. Silakan sampaikan melalui formulir di bawah ini.</p>

                    <form method="POST" action="{{ route('suggestions.store') }}" class="space-y-6">
                        @csrf

                        <!-- Subjek -->
                        <div>
                            <x-input-label for="subject" :value="__('Subjek Saran')" />
                            <x-text-input id="subject" class="block mt-1 w-full" type="text" name="subject" :value="old('subject')" required autofocus />
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </div>

                        <!-- Isi Pesan -->
                        <div>
                            <x-input-label for="message" :value="__('Isi Pesan / Saran Anda')" />
                            <textarea id="message" name="message" rows="8" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('message') }}</textarea>
                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Kirim Saran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>