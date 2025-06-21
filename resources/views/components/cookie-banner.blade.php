{{-- File: resources/views/components/cookie-banner.blade.php --}}

{{-- 
    Komponen ini menggunakan Alpine.js untuk mengelola statusnya.
--}}
<div x-data="{
        showBanner: localStorage.getItem('cookie_consent') !== 'true',
        readyToShow: false, // State baru untuk mengontrol kemunculan setelah jeda
        init() {
            // Memberi jeda 2 detik (2000 milidetik) sebelum banner siap ditampilkan
            setTimeout(() => { this.readyToShow = true; }, 2000);
        },
        acceptCookies() {
            localStorage.setItem('cookie_consent', 'true');
            this.showBanner = false;
        },
        rejectCookies() {
            // Hanya menyembunyikan banner untuk sesi ini, akan muncul lagi di kunjungan berikutnya
            this.showBanner = false;
        }
    }"
     x-init="init()"
     x-show="showBanner && readyToShow"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
     x-cloak
>
    {{-- Latar Belakang Gelap dengan Efek Blur --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    {{-- Kartu Modal Notifikasi --}}
    <div x-show="showBanner && readyToShow"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
         class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl p-6 sm:p-8 text-center"
    >
        {{-- Ikon Cookie Besar --}}
      

        <h3 class="mt-5 text-2xl font-bold text-slate-900">Kami Menghargai Privasi Anda</h3>
        <div class="mt-2 text-base text-slate-600">
            <p>
                Platform "ArenaLatih" menggunakan cookie untuk menyimpan progres latihan Anda dan memberikan pengalaman terbaik. Dengan melanjutkan, Anda menyetujui penggunaan cookie sesuai dengan <a href="{{ route('privacy.policy') }}" target="_blank" class="font-semibold text-indigo-600 hover:underline">kebijakan privasi</a> kami.
            </p>
        </div>
        {{-- PERUBAHAN DI SINI: Styling tombol disempurnakan --}}
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Tombol Tolak dibuat lebih subtle --}}
            {{-- PERBAIKAN: Menambahkan 'items-center' untuk menengahkan teks secara vertikal --}}
            <button @click="rejectCookies()" type="button" class="w-full inline-flex items-center justify-center rounded-lg px-6 py-3 text-base font-semibold text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 transition-colors">
                Tolak
            </button>
            <button @click="acceptCookies()" type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-transform hover:scale-105">
                Saya Mengerti & Setuju
            </button>
        </div>
    </div>
</div>
