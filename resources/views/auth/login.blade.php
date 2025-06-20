{{-- File: resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <style>
        body {
            background-color: #f1f5f9; /* slate-100 */
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px); /* slate-200 */
            background-size: 20px 20px;
            padding: 2rem 0;
            height: max-content;
        }
    </style>

    {{-- Card Form Login --}}
    {{-- PERUBAHAN DI SINI: Menambahkan margin vertikal (my-8) untuk memberikan jarak --}}
    <div class="w-full rounded-2xl shadow-2xl shadow-slate-300/50 my-8 sm:max-w-md">
        <div class="p-8 space-y-6 sm:p-10">
            <h1 class="text-2xl text-center font-bold leading-tight tracking-tight text-gray-900 md:text-3xl">
                Masuk ke Akun Anda
            </h1>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Login (Email or Username) -->
                <div>
                    <label for="login" class="block mb-2 text-sm font-medium text-gray-900">Email atau Username</label>
                    <input type="text" name="login" id="login" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3" placeholder="nama@email.com atau username" required autofocus>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3 transition duration-200" required="">
                </div>

                <div class="flex items-center justify-between">
                    <!-- Remember Me -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember_me" name="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-indigo-500 text-indigo-600">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="remember_me" class="text-gray-500">{{ __('Ingat saya') }}</label>
                        </div>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                            {{ __('Lupa password?') }}
                        </a>
                    @endif
                </div>

                <!-- Tombol Masuk -->
                <button type="submit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-lg text-base px-5 py-3 text-center transition duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">Masuk</button>
                
                {{-- PERUBAHAN DI SINI: Menghapus tautan "Daftar" untuk desain yang lebih bersih --}}
            </form>
        </div>
    </div>
</x-guest-layout>
