{{-- File: resources/views/test/share-image-template.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    {{-- Kita gunakan CDN Tailwind CSS di sini agar mandiri --}}
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        /* Pastikan font dimuat dengan benar */
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-inter">
    <div class="w-[1200px] h-[630px] bg-white p-16 flex flex-col justify-between">
        
        {{-- BAGIAN ATAS: LOGO & JUDUL TES --}}
        <div>
            <div class="flex items-center gap-4">
                <svg class="w-12 h-12 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 22h20L12 2zm0 4.55L17.52 20H6.48L12 6.55z"></path>
                </svg>
                <span class="font-black text-4xl text-gray-800 tracking-tight">ArenaLatih</span>
            </div>
            <p class="text-gray-500 mt-2 text-2xl">Hasil Latihan: {{ $result->test->title }}</p>
        </div>

        {{-- BAGIAN TENGAH: HASIL DINAMIS --}}
        <div class="flex items-end justify-between">
            <div>
                <p class="text-gray-500 text-2xl">Selamat kepada:</p>
                <p class="text-5xl font-bold text-gray-900 mt-2">{{ $result->user->name }}</p>
            </div>
            <div class="text-right">
                {{-- LOGIKA BARU DI SINI --}}
                @if($result->test->result_type === 'numeric')
                    {{-- Tampilan untuk hasil berbasis skor --}}
                    <p class="text-gray-500 text-2xl">Skor Akhir:</p>
                    <p class="text-9xl font-black text-indigo-600">{{ $result->score }}</p>
                @else
                    {{-- Tampilan untuk hasil berbasis deskriptif --}}
                    <p class="text-gray-500 text-2xl">Tipe Anda:</p>
                    <p class="text-8xl font-black text-indigo-600">{{ $result->descriptive_outcome }}</p>
                @endif
            </div>
        </div>

        {{-- BAGIAN BAWAH: TANGGAL --}}
        <div class="text-gray-400 text-xl">
            Diselesaikan pada {{ $result->created_at->format('d F Y') }}
        </div>
    </div>
</body>
</html>
