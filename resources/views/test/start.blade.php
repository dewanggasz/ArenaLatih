<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" xintegrity="sha512-jEnuDt6jfecCjUly/lM4PkvDrFGa3GV/2SglIaeHZGxAnDr_sNaF4FNNvEcJnioH3zwWMTGobNdOF5AcG/eRXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfirmasi Latihan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8 text-gray-900">
                    
                    <h3 class="text-3xl font-bold text-center mb-2">{{ $test->title }}</h3>
                    <p class="text-center text-slate-600 mb-8">Harap baca informasi di bawah ini dengan saksama sebelum memulai.</p>

                    {{-- Konten dinamis berdasarkan tipe tes --}}
                    @if ($test->result_type === 'numeric')
                        {{-- Tampilan untuk Tes Berbasis Skor --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Total Soal:</span>
                                <span class="font-bold text-lg">{{ $pgCount + $essayCount }} Soal</span>
                            </div>
                            <div class="flex justify-between items-center pl-4">
                                <span class="text-sm text-slate-600">Pilihan Ganda:</span>
                                <span class="text-sm font-medium">{{ $pgCount }} Soal</span>
                            </div>
                             <div class="flex justify-between items-center pl-4">
                                <span class="text-sm text-slate-600">Esai:</span>
                                <span class="text-sm font-medium">{{ $essayCount }} Soal</span>
                            </div>
                            <div class="flex justify-between items-center border-t pt-4">
                                <span class="font-semibold text-slate-700">Durasi Pengerjaan:</span>
                                <span class="font-bold text-lg">{{ $test->duration_minutes }} Menit</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-slate-700">Bobot Penilaian:</span>
                                <span class="font-bold text-lg">PG ({{ $test->pg_weight }}%) & Esai ({{ $test->essay_weight }}%)</span>
                            </div>
                        </div>
                        <div class="mt-8 text-center">
                            <p class="text-slate-700 font-semibold">Selamat Mengerjakan!</p>
                             <p class="text-sm text-slate-500 mt-1">Pastikan koneksi internet Anda stabil. Progres Anda akan tersimpan otomatis.</p>
                        </div>
                    @else
                        {{-- Tampilan untuk Tes Berbasis Kategori/Deskriptif (MBTI, Gaya Belajar, dll) --}}
                         <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 space-y-4 text-center">
                            <div class="flex justify-center">
                                <div id="lottie-animation-container" style="width: 150px; height: 150px;"></div>
                            </div>
                             {{-- PERUBAHAN DI SINI: Judul petunjuk sekarang dinamis --}}
                             <p class="font-semibold text-slate-700">Petunjuk Pengerjaan: {{ $test->subCategory->name ?? 'Tes Deskriptif' }}</p>
                             <p class="text-sm text-slate-600">Tidak ada jawaban benar atau salah dalam tes ini. Jawablah setiap pertanyaan dengan jujur sesuai dengan diri Anda yang sebenarnya untuk mendapatkan hasil yang paling akurat.</p>
                             <div class="pt-2 flex justify-between items-center text-sm text-slate-500">
                                <span >Jumlah Pertanyaan: {{ $test->questions->count() }}</span>
                                <span >Estimasi Waktu: {{ $test->duration_minutes }} menit</span>
                             </div>
                         </div>
                         <div class="mt-8 text-center">
                            <p class="text-slate-700 font-semibold">Siap untuk Mengenal Diri Anda Lebih Dalam?</p>
                        </div>
                    @endif
                    
                    {{-- Tombol Aksi --}}
                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('test.show', $test) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300 transform hover:scale-105">
                            Mulai Sekarang
                        </a>
                    </div>
                    <div class="mt-4 text-center">
                         <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700">Kembali ke Dashboard</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('lottie-animation-container');
            if(container) {
                const anim = lottie.loadAnimation({
                    container: container, // Elemen div yang menjadi wadah
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    // Ganti 'animations/nama-file-anda.json' dengan path yang benar
                    path: '{{ asset('animations/Man.json') }}' 
                });
            }
        });
    </script>
</x-app-layout>
