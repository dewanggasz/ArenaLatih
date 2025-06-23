<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-indigo-100 rounded-lg">
                 <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Profil Saya') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ activeTab: 'profile' }" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Kolom Kiri: Kartu Profil Pengguna -->
                <div class="md:col-span-1">
                    <div class="bg-white shadow-xl rounded-2xl p-6 text-center sticky top-24">
                        <div class="relative w-32 h-32 mx-auto mb-4">
                            <img class="w-full h-full rounded-full object-cover border-4 border-indigo-200 shadow-md" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EBF4FF&color=7F9CF5&size=128" alt="Profile Picture">
                            <div class="absolute bottom-1 right-1 bg-green-500 w-4 h-4 rounded-full border-2 border-white"></div>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">{{ $user->name }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $user->email }}</p>
                        <p class="text-xs text-slate-400 mt-4">Bergabung pada {{ $user->created_at->format('d F Y') }}</p>
                    </div>
                </div>

                <!-- Kolom Kanan: Konten dengan Tab -->
                <div class="md:col-span-2">
                    <!-- Navigasi Tab -->
                    <div class="mb-6 bg-slate-100 p-2 rounded-xl flex items-center justify-between gap-2">
                        <button @click="activeTab = 'profile'" :class="{ 'bg-white shadow-md text-indigo-700': activeTab === 'profile', 'text-slate-600': activeTab !== 'profile' }" class="w-full text-center font-semibold px-4 py-2.5 rounded-lg transition-all duration-300">Informasi Profil</button>
                        <button @click="activeTab = 'password'" :class="{ 'bg-white shadow-md text-indigo-700': activeTab === 'password', 'text-slate-600': activeTab !== 'password' }" class="w-full text-center font-semibold px-4 py-2.5 rounded-lg transition-all duration-300">Ubah Password</button>
                        <button @click="activeTab = 'delete'" :class="{ 'bg-white shadow-md text-indigo-700': activeTab === 'delete', 'text-slate-600': activeTab !== 'delete' }" class="w-full text-center font-semibold px-4 py-2.5 rounded-lg transition-all duration-300">Hapus Akun</button>
                    </div>

                    <!-- Konten Tab -->
                    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                        <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="p-6 sm:p-8">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                        <div x-show="activeTab === 'password'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                            <div class="p-6 sm:p-8">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                        <div x-show="activeTab === 'delete'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                            <div class="p-6 sm:p-8">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
