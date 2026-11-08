# Bahan Laporan Minggu 10: Skinivazza

Setiap fitur minggu 10 pada kontrak dan bukti pengerjaannya. Isi kolom "bukti" dengan tangkapan layar setelah kamu menjalankannya.

| Fitur | Rencana (minggu 10) | Status | Letak bukti |
|---|---|---|---|
| 01 | Tabel pengguna: email unik, status, waktu dibuat | Selesai (`004`: kolom `username`, `status`, `last_login_at`); pendaftaran member lewat Google, admin lewat `admin:buat` | `database/sql/004_*.sql` |
| 02 | Kata sandi di-hash | Selesai: `Hash::make` dan `Hash::check` (bcrypt) | `AdminController::login`, `BuatAdmin.php` |
| 13 | Tabel peran dan izin | Selesai: `roles`, `permissions`, `role_permission` beserta data awal | `004` |
| 14 | Penetapan peran pada pengguna | Selesai: `user_roles`, diisi oleh `admin:buat`; dipakai middleware `AdminOnly` | `004`, `AdminOnly.php`, `Access.php` |
| 23 | Rancangan skema dan diagram relasi | Selesai | `docs/erd.md` |
| 24 | Kamus data | Selesai (dihasilkan dari skema) | `docs/kamus-data.md` |
| 25 | Migrasi bernomor | Selesai: `001`–`004` dan `php artisan db:migrasi` dengan tabel `schema_migrations` | `database/sql`, `MigrasiSql.php` |
| 26 | Kunci asing beralasan | Selesai: 23 kunci asing dengan aturan ON DELETE | `004`, bagian akhir `erd.md` |
| 27 | Constraint penjaga data | Selesai: 11 CHECK dan NOT NULL pada kolom relasi | `004` |
| 28 | Normalisasi sampai 3NF | Selesai | `docs/normalisasi.md` |
| 29 | Tabel acuan | Selesai: `order_statuses`, `couriers` | `004` |
| 82 | Identitas eksternal | Selesai: `user_identities`, login Google memakainya | `004`, `AccountController::callback` |
| 140 | Perlindungan CSRF | Terverifikasi: semua formulir POST memakai `@csrf` | `docs/analisis-skinivazza.md` |
| 141 | Pencegahan XSS | Terverifikasi: tanpa `{!! !!}`; **tambahkan** uji dengan nama pelanggan `<script>` untuk bukti | uji manual |
| 142 | Kueri berparameter | Terverifikasi: tanpa SQL mentah | pencarian `whereRaw/DB::raw` = 0 |
| 145 | Header dan cookie aman | Selesai: `SecurityHeaders`, variabel `SESSION_*` | `SecurityHeaders.php`, `env.snippet.txt` |
| 146 | Rahasia di luar kode | Selesai: `config/skinivazza.php`, `ADMIN_PASSWORD` dihapus, `.gitignore` | `config/`, `.gitignore` |
| 166 | Git dan tag rilis | **Tugasmu**: `git init`, commit per fitur, `git tag v0.1-minggu10` | riwayat Git |

Fitur 30 (kelola data produk lengkap) dipindah ke minggu 11 pada kontrak karena belum dikerjakan.

## Urutan menjalankan untuk mengambil bukti
1. Buat database kosong, isi `.env`, lalu `php artisan db:migrasi` (di database cadangan dulu).
2. `php artisan admin:buat nama@email.com "Nama Admin" super_admin`
3. Buka `/admin/login`, masuk, ambil tangkapan layar; coba kata sandi salah 6 kali untuk melihat pembatasan.
4. Coba akun `admin_gudang` membuka `/admin/voucher` → harus 403.
5. Di phpMyAdmin atau klien MySQL: `select * from users;` (kolom `password` berisi hash `$2y$...`), lalu `insert into products(...) values(..., -5, ...)` → harus ditolak CHECK.
