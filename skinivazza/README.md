# Skinivazza

Aplikasi toko online Skinivazza berbasis **Laravel 12**, PHP, MySQL/MariaDB, Vite, dan Tailwind CSS. Repositori ini berisi source code aplikasi, aset gambar produk, dokumentasi skema database, serta SQL bernomor 001–004.

> **Status verifikasi:** paket ini dirapikan untuk publikasi GitHub, tetapi aplikasi belum dinyatakan berhasil dijalankan atau diuji end-to-end. Lakukan pengujian pada salinan database/development sebelum deployment.

## Persyaratan

- PHP **8.2 atau lebih baru**, dengan ekstensi yang dibutuhkan Laravel dan driver database yang dipilih (misalnya `pdo_mysql` untuk MySQL/MariaDB).
- Composer.
- Node.js dan npm untuk membangun aset frontend.
- MySQL 8+ atau MariaDB yang kompatibel jika menggunakan database MySQL.
- Kredensial OAuth Google dan kunci VAPID hanya jika fitur terkait akan digunakan.

Versi framework dan dependensi PHP mengacu pada `composer.json` dan `composer.lock`. Dependensi frontend tercantum di `package.json`. Saat ini repositori tidak menyertakan `package-lock.json`; `npm install` akan menyelesaikan dependensi dan membuat lockfile lokal.

## Instalasi development

1. Clone repositori, lalu masuk ke direktori proyek.
2. Pasang dependensi PHP:

   ```bash
   composer install
   ```

3. Buat file konfigurasi lokal. Salin `.env.example` menjadi `.env`, lalu sesuaikan konfigurasi database, URL aplikasi, mail, dan layanan eksternal. Jangan commit `.env`.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Pada Windows, salin file `.env.example` secara manual menjadi `.env`.

4. Siapkan database development kosong dan isi variabel `DB_*` pada `.env`.
5. Pasang dependensi frontend dan bangun aset:

   ```bash
   npm install
   npm run build
   ```

6. Buat symbolic link storage jika diperlukan:

   ```bash
   php artisan storage:link
   ```

7. Jalankan server development:

   ```bash
   php artisan serve
   ```

   Untuk perubahan frontend selama development, jalankan `npm run dev` di terminal terpisah. Scheduler dapat dijalankan secara lokal dengan `php artisan schedule:work` jika ingin menguji tugas terjadwal.

## Database: penting sebelum menjalankan

Proyek menyimpan SQL bertahap di `database/sql/`:

- `001_skema_dasar.sql`
- `002_fase2_akun_poin_push.sql`
- `003_fase2b_voucher_hadiah.sql`
- `004_minggu10_fondasi.sql`

Tersedia pula migrasi Laravel di `database/migrations/`. **Jangan menjalankan SQL bernomor dan seluruh migrasi Laravel secara membabi buta pada database yang sama.** Ada potensi konflik tabel `users`: migrasi bawaan Laravel `0001_01_01_000000_create_users_table.php` membuat tabel tersebut, sedangkan SQL `002_fase2_akun_poin_push.sql` juga membuat `users`.

Rekomendasi penyelesaian (belum diterapkan dalam paket ini):

1. Tentukan satu jalur provisioning database yang menjadi sumber kebenaran untuk instalasi baru.
2. Tinjau struktur, foreign key, indeks, dan data yang diharapkan oleh aplikasi pada SQL 001–004 serta seluruh migrasi Laravel.
3. Jika SQL 001–004 dipilih sebagai baseline, siapkan strategi agar migrasi bawaan yang membuat `users` tidak dijalankan pada database yang sama, tanpa kehilangan tabel Laravel lain yang memang diperlukan.
4. Jika migrasi Laravel dipilih, buat migrasi aplikasi yang setara dengan SQL 001–004 dan verifikasi kesetaraan skema terlebih dahulu.
5. Uji strategi pada database kosong sementara, bukan database produksi.

File SQL dan migrasi **dipertahankan tanpa perubahan** dalam paket GitHub-ready ini. Tidak ada database yang dibuat, diubah, atau dimigrasikan saat proses perapian.

## Konfigurasi fitur eksternal

- **Web Push:** isi konfigurasi VAPID sesuai konfigurasi aplikasi dan gunakan HTTPS pada lingkungan deployment (localhost dapat digunakan untuk development).
- **Google Login:** siapkan OAuth Client di Google Cloud dan isi konfigurasi Google yang digunakan aplikasi.
- **Admin awal:** periksa implementasi command `admin:buat` dan panduan konfigurasi sebelum membuat akun admin.
- **Data toko:** ganti placeholder nomor BPOM, rekening/QRIS, WhatsApp, tautan marketplace, kota asal, berat/harga produk, dan jam operasional dengan data resmi sebelum digunakan.

Jangan menaruh secret, token, password, private key, data pelanggan, atau dump database berisi data nyata di GitHub.

## Fitur dan aset

Source code, route, view, controller, test, aset gambar produk, service worker/web push, serta dokumentasi di `docs/` dipertahankan. Dokumentasi database meliputi ERD, kamus data, normalisasi, analisis, dan laporan proyek. `preview/preview.html` juga dipertahankan sebagai pratinjau statis lama.

Lihat `docs/` untuk dokumentasi lebih lanjut. Periksa kembali isi dokumen terhadap implementasi terkini sebelum menjadikannya spesifikasi final.

## Pemeriksaan sebelum deployment

- Pastikan `.env` lokal berisi konfigurasi yang benar dan tidak pernah masuk commit.
- Jalankan test suite yang sesuai, misalnya `php artisan test`.
- Verifikasi skema database menggunakan strategi provisioning yang sudah dipilih.
- Uji alur pembeli, admin, checkout/pesanan, chat, poin/voucher, login Google, dan web push sesuai fitur yang akan diaktifkan.
- Pastikan `npm run build` berhasil dan hasil build tersedia pada lingkungan deployment.
- Siapkan backup, HTTPS, konfigurasi scheduler/queue, serta penyimpanan file yang sesuai.

**Catatan:** belum ada klaim bahwa langkah instalasi, test, build, atau deployment di atas telah berhasil dijalankan untuk paket ini.
