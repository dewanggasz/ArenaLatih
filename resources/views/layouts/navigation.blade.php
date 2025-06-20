<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo Kustom Baru -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <div class="flex items-center gap-2">
                            {{-- Logo Baru: Simbol abstrak untuk pertumbuhan/puncak --}}
                            <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 22h20L12 2zm0 4.55L17.52 20H6.48L12 6.55z"></path>
                            </svg>
                            <span class="font-bold text-xl text-gray-800 tracking-tight">ArenaLatih</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                     <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.index')">
                        {{ __('Diskusi') }}
                    </x-nav-link>
                    <x-nav-link :href="route('leaderboard.index')" :active="request()->routeIs('leaderboard.index')">
                        {{ __('Peringkat') }}
                    </x-nav-link>
                    <x-nav-link :href="route('suggestions.create')" :active="request()->routeIs('suggestions.create')">
                        {{ __('Saran & Masukan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                        {{ __('Profile') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Off-canvas) -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform -translate-x-full"
        class="sm:hidden fixed inset-0 z-40"
        @click.away="open = false"
        x-cloak
    >
        {{-- Latar belakang gelap --}}
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"></div>

        {{-- Konten Menu --}}
        <div class="relative w-4/5 max-w-xs h-full bg-white shadow-xl">
            <div class="p-4">
                 <!-- Logo di dalam menu mobile -->
                <div class="shrink-0 flex items-center mb-6">
                    <a href="{{ route('dashboard') }}" @click="open = false">
                        <div class="flex items-center gap-2">
                            {{-- Logo Baru: Simbol abstrak untuk pertumbuhan/puncak --}}
                            <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 22h20L12 2zm0 4.55L17.52 20H6.48L12 6.55z"></path>
                            </svg>
                            <span class="font-bold text-xl text-gray-800 tracking-tight">ArenaLatih</span>
                        </div>
                    </a>
                </div>
                <!-- Menu Links -->
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" @click="open = false">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.index')" @click="open = false">
                        {{ __('Diskusi') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('leaderboard.index')" :active="request()->routeIs('leaderboard.index')">
                        {{ __('Peringkat') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('suggestions.create')" :active="request()->routeIs('suggestions.create')">
                        {{ __('Saran & Masukan') }}
                    </x-responsive-nav-link>
                     <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" @click="open = false">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- User Info & Logout -->
            <div class="absolute bottom-0 left-0 w-full p-4 border-t border-gray-200">
                <div class="flex items-center mb-3">
                     <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-500">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="ms-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-red-500 hover:bg-red-50">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
