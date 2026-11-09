# Skinivazza V2 — tahap fondasi

Proyek e-commerce Skinivazza yang dibangun ulang dengan Laravel 12 + MySQL.
Paket ini adalah fondasi awal untuk dimasukkan ke dalam proyek Laravel 12 baru.
Pengunjung, pembeli, dan dosen nantinya cukup membuka tautan website melalui browser.

## Prinsip proyek
- Satu aplikasi dan satu database untuk kebutuhan PM dan PBD.
- Repository lama tidak diubah.
- Database menggunakan satu jalur migration Laravel sebagai sumber skema utama.
- Tidak menggunakan Supabase.
- Hak admin utama dan admin biasa dibedakan.
- Fitur dikembangkan bertahap dan diuji per tahap.

## Cara memasang fondasi
1. Buat proyek Laravel 12 baru: `composer create-project laravel/laravel skinivazza-v2`.
2. Salin folder `app/`, `database/`, `routes/`, dan `docs/` dari paket ini ke proyek baru.
3. Periksa folder `database/migrations/`. Hapus migration bawaan Laravel yang membuat tabel `users`, karena migration fondasi Skinivazza membuat tabel `users` sendiri.
4. Buat database MySQL, lalu sesuaikan `.env` dengan `.env.example`.
5. Jalankan `php artisan migrate`.
6. Jalankan aplikasi untuk pengembangan dengan `php artisan serve`.

Catatan: paket ini merupakan tahap fondasi, bukan website siap produksi. Fitur katalog, keranjang, checkout, pembayaran, pengiriman, komplain, retur/refund, poin, voucher, notifikasi, dan panel admin akan dibangun berurutan setelah fondasi lolos pengujian.

## Struktur
- `app/Models`: model inti.
- `app/Http/Controllers`: controller dasar.
- `database/migrations`: skema database fondasi.
- `routes/web.php`: rute awal.
- `docs/`: keputusan arsitektur dan daftar tahap.

## Catatan keamanan
Jangan memasukkan file `.env`, password database, token, atau kredensial asli ke GitHub.
