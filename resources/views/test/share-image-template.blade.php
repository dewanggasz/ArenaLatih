<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-[600px] h-[315px] bg-white rounded-2xl shadow-2xl p-8 flex flex-col justify-between" style="font-family: 'Inter', sans-serif;">
        <div>
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 22h20L12 2zm0 4.55L17.52 20H6.48L12 6.55z"></path>
                </svg>
                <span class="font-bold text-2xl text-gray-800 tracking-tight">ArenaLatih</span>
            </div>
            <p class="text-gray-500 mt-2 text-lg">Hasil Latihan: {{ $result->test->title }}</p>
        </div>

        <div class="flex items-end justify-between">
            <div>
                <p class="text-gray-500 text-sm">Selamat kepada:</p>
                <p class="text-3xl font-bold text-gray-900">{{ $result->user->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-500 text-sm">Skor Akhir:</p>
                <p class="text-7xl font-bold text-indigo-600">{{ $result->score }}</p>
            </div>
        </div>
    </div>
</body>
</html>