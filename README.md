# ArenaLatih: Platform Latihan Soal Cerdas
Selamat datang di **ArenaLatih**, sebuah platform web modern yang dirancang untuk menjadi pusat latihan soal yang fleksibel, interaktif, dan cerdas. Dibangun dengan Laravel dan Filament, platform ini tidak hanya berfungsi sebagai alat untuk mengerjakan ujian, tetapi juga sebagai partner belajar yang adaptif.

Proyek ini merupakan hasil kolaborasi intensif yang menggabungkan visi desain yang kuat dengan implementasi teknis yang canggih, mencakup integrasi AI, sistem penilaian dinamis, dan pengalaman pengguna yang premium.

## ✨ Fitur Utama
ArenaLatih dilengkapi dengan serangkaian fitur canggih yang membedakannya dari platform latihan biasa:

### Untuk Pengguna (Peserta Latihan)
- Halaman Sambutan Interaktif: Pengalaman pertama yang memukau dengan animasi latar belakang cairan (metaballs), kursor neon kustom, dan efek coretan yang merespons gerakan mouse.

- Dashboard Modern Berbasis Tab: Antarmuka yang bersih untuk beralih antara "Paket Latihan" dan "Riwayat Latihan".

- Filter Kategori Cerdas: Pengguna dapat dengan mudah memfilter latihan berdasarkan kategori utama (Pendidikan, Psikologi, dll.) tanpa perlu me-refresh halaman.

- Kartu Latihan Dinamis: Setiap kartu latihan menampilkan status yang relevan (Belum Dimulai, On Progress, Selesai) dan informasi penting seperti durasi dan jumlah soal.

- Halaman Konfirmasi Cerdas: Sebelum memulai, pengguna akan melihat halaman konfirmasi yang secara otomatis menyesuaikan informasinya berdasarkan tipe tes (tes skor atau tes deskriptif).

- Pengalaman Ujian Premium:

  - Antarmuka ujian yang bersih dan bebas gangguan.
  - Progress bar dan timer yang menempel di atas layar (sticky) dan dapat disembunyikan saat menggulir.
  - Peringatan visual saat waktu akan habis.
  - Fitur anti copy-paste untuk menjaga integritas ujian.
  - Penyimpanan progres otomatis di localStorage yang unik per pengguna, sehingga aman jika koneksi terputus.

- Sistem Penilaian & Hasil yang Canggih:
  - Mendukung tes berbasis skor numerik dengan bobot dinamis untuk Pilihan Ganda dan Esai.
  - Mendukung tes berbasis hasil deskriptif (seperti MBTI atau Gaya Belajar) dengan evaluasi otomatis.
  - Penilaian Esai oleh AI: Integrasi dengan Google Gemini untuk menilai jawaban esai dan memberikan umpan balik.
  - Pembahasan Otomatis dari AI: Jika soal esai tidak dijawab, AI akan memberikan contoh jawaban ideal sebagai materi pembelajaran.
  - Halaman Hasil yang Memukau: Desain yang berbeda untuk hasil skor dan hasil deskriptif, lengkap dengan animasi dan visualisasi data.

- Fitur Sosial & Komunitas:
  - Ruang Diskusi Global: Sebuah chat room real-time di mana semua pengguna bisa berinteraksi. Mendukung pesan teks dan upload gambar (disimpan secara lokal).
  - Fitur Balas Pesan: Pengguna dapat membalas pesan secara spesifik untuk percakapan yang lebih terstruktur.
  - Berbagi Hasil ke Media Sosial: Fitur untuk membuat dan membagikan "kartu skor" berupa gambar yang di-generate secara otomatis.
  - Manajemen Profil & Papan Peringkat:
  - Halaman profil di mana pengguna bisa mengubah nama dan password.
  - Halaman Papan Peringkat yang menampilkan skor tertinggi untuk setiap latihan, lengkap dengan navigasi dropdown yang elegan.

### Untuk Admin (Panel Filament)
- URL Panel Admin yang Unik: Keamanan ditingkatkan dengan mengubah alamat login standar.

- Manajemen Konten Terpadu:

  - CRUD penuh untuk Kategori Utama dan Sub-kategori.

  - Manajemen Paket Latihan yang terhubung dengan kategori.

  - Formulir cerdas yang menampilkan field yang relevan berdasarkan "Tipe Hasil" (Numerik atau Deskriptif).

- Manajemen Soal yang Efisien:

  - Mengelola soal langsung dari dalam halaman "Edit Latihan", menciptakan alur kerja yang intuitif.

  - Formulir terpadu untuk membuat soal PG (dengan pilihan jawaban) dan Esai (dengan rubrik AI) dalam satu modal.

- Impor Massal dari CSV:

  - Fitur impor terpisah untuk soal berbasis skor dan soal berbasis kepribadian (MBTI, dll.).

  - Fitur impor untuk mendaftarkan pengguna baru secara massal.

  - Fitur impor untuk mendefinisikan semua kemungkinan hasil akhir dari tes deskriptif.

- Manajemen Pengguna & Saran:

  - CRUD penuh untuk pengguna, lengkap dengan penetapan peran (admin/user).

  - Halaman khusus untuk melihat dan mengelola semua saran yang masuk dari pengguna.

### 🛠️ Teknologi yang Digunakan
- Backend: Laravel 12, PHP 8.2

- Frontend: Blade, Tailwind CSS, Alpine.js

- Panel Admin: Filament v3

- Database: MySQL

- Animasi: GSAP (GreenSock Animation Platform) & Lottie

- AI: Google Gemini API (google-gemini-php/laravel)

- Pustaka Pendukung:

  - maatwebsite/excel (untuk impor/ekspor CSV & Excel)

  - spatie/browsershot (untuk membuat gambar dari HTML)

### 🚀 Panduan Instalasi
Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal Anda.

**1. Clone Repositori**
  ```bash
  git clone [URL_REPOSITORY_ANDA]
  cd nama-folder-proyek
  ```
**2. Instal Dependensi**
  ```bash
  composer install
  npm install
  ```
**3. Konfigurasi Lingkungan**

- Salin file ```.env.example``` menjadi ```.env.```

  ```cp .env.example .env```

- Buat kunci aplikasi baru.

  ```php artisan key:generate```

- Buka file ```.env``` dan atur koneksi database Anda (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

- Tambahkan API Key Anda dari Google AI Studio ke dalam ```.env.```

  ```GEMINI_API_KEY=KUNCI_API_GEMINI_ANDA```

**4. Jalankan Migrasi Database**

Perintah ini akan membuat semua tabel yang dibutuhkan di database Anda.

  `php artisan migrate`

**5. Buat Storage Link**

Perintah ini wajib dijalankan untuk memastikan file yang diunggah (seperti gambar chat) dapat diakses dari web.

  `php artisan storage:link`

**6. Buat Akun Admin Pertama**

Jalankan perintah Filament ini dan ikuti petunjuknya.

  `php artisan make:filament-user`

- Penting: Setelah membuat user, kita perlu memberinya peran 'admin'. Jalankan `php artisan tinker`, lalu:
  ```bash
  $user = App\Models\User::where('email', 'emailadmin@anda.com')->first();
  $user->role = 'admin';
  $user->save();
  exit;
  ```
**7. Jalankan Aplikasi**

- Kompilasi aset frontend.

  ```npm run dev```

- Jalankan server development.

  `php artisan serve`

Aplikasi Anda sekarang akan berjalan di `http://127.0.0.1:8000`. Anda bisa mengakses Panel Admin melalui alamat URL yang telah Anda tentukan di `app/Providers/Filament/AdminPanelProvider.php`.
