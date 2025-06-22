@props([
    'title' => 'ArenaLatih - Asah Kemampuan, Raih Kemajuan',
    'description' => 'Platform latihan soal modern untuk UKMPPG, tes kepribadian, kesiapan kerja, dan lainnya. Tingkatkan skormu dan kenali potensimu bersama kami.'
])

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

{{-- Meta Tags untuk Media Sosial (Open Graph) --}}
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ url()->current() }}" />
{{-- Anda bisa menambahkan og:image default di sini nanti --}}
