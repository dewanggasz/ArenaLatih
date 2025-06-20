{{-- File: resources/views/components/cookie-banner.blade.php --}}

{{-- 
    Komponen ini menggunakan Alpine.js untuk mengelola statusnya.
    1. x-data: Menginisialisasi komponen. 'showBanner' akan memeriksa localStorage.
    2. acceptCookies(): Fungsi yang akan dipanggil saat tombol diklik.
       Ini akan mengatur item 'cookie_consent' di localStorage dan menyembunyikan banner.
    3. x-show: Banner hanya akan muncul jika 'showBanner' bernilai true.
    4. x-transition: Menambahkan animasi fade-in-out yang halus.
--}}
<div x-data="{
        showBanner: localStorage.getItem('cookie_consent') !== 'true',
        acceptCookies() {
            localStorage.setItem('cookie_consent', 'true');
            this.showBanner = false;
        }
    }"
     x-show="showBanner"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-4"
     class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5 z-50"
     x-cloak
>
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="p-4 rounded-lg bg-slate-800 shadow-lg sm:p-6">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                    <span class="flex p-2 rounded-lg bg-slate-600">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <p class="ml-3 font-medium text-white truncate">
                        <span class="md:hidden"> Kami menggunakan cookie untuk pengalaman terbaik. </span>
                        <span class="hidden md:inline"> Website ini menggunakan cookie untuk memastikan Anda mendapatkan pengalaman terbaik. </span>
                    </p>
                </div>
                <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                    <button @click="acceptCookies()" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                        Saya Mengerti
                    </button>
                </div>
                <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
                    <a href="#" class="text-sm font-medium text-slate-300 hover:text-white underline">
                        Kebijakan Privasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
