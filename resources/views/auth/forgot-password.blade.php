<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Lupa password Anda? Tidak masalah.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- KONTEN BARU YANG DITAMBAHKAN --}}
    <div class="p-6 bg-yellow-50 border border-yellow-300 rounded-lg text-center">
        <h3 class="font-bold text-yellow-800">Hubungi Administrator</h3>
        <p class="mt-2 text-sm text-yellow-700">
            Untuk keamanan dan validasi, reset password hanya dapat dilakukan secara manual oleh admin. Silakan hubungi admin melalui salah satu platform di bawah ini.
        </p>

        {{-- IKON MEDIA SOSIAL (MENGGUNAKAN FONT AWESOME) --}}
        <div class="mt-6 flex justify-center items-center space-x-8">
            <!-- WhatsApp -->
            <a href="https://wa.me/62xxxxxxxxxx" target="_blank" class="text-gray-500 hover:text-green-500 transition-transform duration-300 transform hover:scale-110" title="Hubungi via WhatsApp">
                <i class="fab fa-whatsapp fa-3x"></i>
            </a>

            <!-- Telegram -->
            <a href="https://t.me/usernameanda" target="_blank" class="text-gray-500 hover:text-blue-500 transition-transform duration-300 transform hover:scale-110" title="Hubungi via Telegram">
                <i class="fab fa-telegram fa-3x"></i>
            </a>

            <!-- TikTok -->
            <a href="https://www.tiktok.com/@usernameanda" target="_blank" class="text-gray-500 hover:text-black transition-transform duration-300 transform hover:scale-110" title="Lihat profil TikTok">
                <i class="fab fa-tiktok fa-3x"></i>
            </a>

            <!-- Instagram -->
            <a href="https://www.instagram.com/gazeofdew" target="_blank" class="text-gray-500 hover:text-pink-500 transition-transform duration-300 transform hover:scale-110" title="Lihat profil Instagram">
                <i class="fab fa-instagram fa-3x"></i>
            </a>
        </div>
    </div>
</x-guest-layout>