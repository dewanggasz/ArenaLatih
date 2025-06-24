{{-- File: resources/views/auth/register.blade.php --}}
<x-guest-layout>
    <style>
        body {
            background-color: #f1f5f9; /* slate-100 */
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px); /* slate-200 */
            background-size: 20px 20px;
        }
    </style>

    <div class="w-full bg-white rounded-2xl shadow-2xl shadow-slate-300/50 my-8 sm:max-w-md">
        <div class="p-8 space-y-6 sm:p-10">
            <h1 class="text-2xl text-center font-bold leading-tight tracking-tight text-gray-900 md:text-3xl">
                Buat Akun Baru
            </h1>
            <p class="text-sm text-center text-slate-500 -mt-4">Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:underline">Masuk di sini</a></p>


            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <x-input-label for="name" value="Nama Lengkap" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Sesuai nama asli Anda" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                
                <!-- Username -->
                <div>
                    <x-input-label for="username" value="Username" />
                    <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autocomplete="username" placeholder="Tanpa spasi, contoh: budisantoso" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                
                {{-- Kita tidak lagi memerlukan Konfirmasi Password --}}

                <div class="pt-2">
                    <button type="submit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-lg text-base px-5 py-3 text-center transition duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                        Daftar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
