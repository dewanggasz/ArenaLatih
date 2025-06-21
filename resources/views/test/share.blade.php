<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Latihan: {{ $result->user->name }} - ArenaLatih</title>

    {{-- Meta Tag Open Graph untuk cuplikan di media sosial --}}
    <meta property="og:title" content="Skor Latihan: {{ $result->test->title }}" />
    <meta property="og:description" content="{{ $result->user->name }} mendapatkan skor {{ $result->score }} di ArenaLatih! Lihat hasilnya." />
    {{-- PERUBAHAN DI SINI: Menggunakan URL langsung dari database --}}
    <meta property="og:image" content="{{ $result->share_image_path }}" />
    <meta property="og:url" content="{{ route('test.share', $result) }}" />
    <meta property="og:type" content="website" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl shadow-slate-300/30 overflow-hidden">
        {{-- Header Kartu --}}
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-8 text-white text-center">
            <h1 class="text-2xl font-bold">Hasil Latihan</h1>
            <p class="text-blue-100 mt-1">{{ $result->test->title }}</p>
        </div>

        {{-- Konten Utama Kartu --}}
        <div class="p-8 text-center">
            <p class="text-slate-600">Selamat kepada:</p>
            <h2 class="text-3xl font-bold text-slate-800 mt-1">{{ $result->user->name }}</h2>

            <div class="mt-8">
                <p class="text-sm text-slate-500">Telah menyelesaikan latihan dengan skor akhir:</p>
                <p class="text-8xl font-bold text-indigo-600 my-2">{{ $result->score }}</p>
            </div>

            <div class="mt-8 text-sm text-slate-500">
                Latihan diselesaikan pada {{ $result->created_at->format('d F Y') }}
            </div>
        </div>
        
        {{-- Footer Kartu --}}
        <div class="bg-slate-50 border-t border-slate-200 p-6 text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <svg class="w-6 h-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 22h20L12 2zm0 4.55L17.52 20H6.48L12 6.55z"></path>
                </svg>
                <span class="font-bold text-lg text-slate-700">ArenaLatih</span>
            </div>
            <p class="text-xs text-slate-500">Platform latihan soal untuk mengasah kemampuan.</p>
            <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">Coba Latihan Sekarang &rarr;</a>
        </div>
    </div>

</body>
</html>