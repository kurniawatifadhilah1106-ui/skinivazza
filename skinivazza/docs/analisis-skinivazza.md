# Analisis Web Skinivazza terhadap Standar Web Enterprise

Sumber: kode di zip awal (Laravel 11, awalnya dirancang untuk PostgreSQL (Supabase) dan sekarang dialihkan ke MySQL mengikuti kontrak). Kode awal belum pernah dijalankan (kata README awal), dan analisis ini juga dilakukan dengan membaca kode, bukan menjalankannya.

## Yang sudah baik
- Alur pembeli dan admin lengkap: katalog, keranjang, checkout, unggah bukti, chat per pesanan, push notifikasi, poin, voucher, hadiah.
- Semua formulir POST sudah memakai `@csrf`; tidak ada `{!! !!}` di Blade; tidak ada SQL mentah (semua lewat Eloquent atau query builder berparameter).
- 14 tabel dan 17 halaman, sudah memenuhi batas minimal kontrak (12 halaman dan 14 tabel).

## Temuan dan tindakan
| No | Temuan di kode awal | Dampak | Status |
|---|---|---|---|
| 1 | Admin masuk dengan satu kata sandi bersama dari `env('ADMIN_PASSWORD')`, dibandingkan teks biasa | Tidak ada identitas per admin, tidak ada jejak, kata sandi tersimpan terbuka | **Diperbaiki**: akun per admin di tabel `users`, bcrypt, `php artisan admin:buat` |
| 2 | Login admin tanpa pembatasan percobaan | Bisa ditebak berulang | **Diperbaiki sebagian**: `throttle:5,1` per alamat IP; penguncian akun menyusul (fitur 05, minggu 11) |
| 3 | Sesi tidak diperbarui setelah login | Rentan session fixation | **Diperbaiki**: `session()->regenerate()` |
| 4 | Semua admin punya hak penuh | Tidak ada pemisahan tugas | **Diperbaiki sebagian**: peran dan izin per rute; menu belum disembunyikan (fitur 15, minggu 11) |
| 5 | `env()` dipakai langsung di kode | Berhenti bekerja setelah `config:cache`, rahasia tersebar | **Diperbaiki**: `config/skinivazza.php` |
| 6 | Aksi admin `act` menerima status apa saja (resi pada pesanan belum bayar → poin diberikan) | Pesanan bisa selesai tanpa dibayar | **Diperbaiki**: tabel `FLOW` pada `AdminController` |
| 7 | Konfirmasi bayar: cek stok, kurangi stok, ubah status terpisah-pisah, tanpa transaksi | Stok bisa berkurang tanpa pesanan terkonfirmasi, dua admin bisa menimpa | **Diperbaiki awal**: `DB::transaction` + `lockForUpdate`; versi prosedur tersimpan menyusul (fitur 40 dan 64, minggu 12) |
| 8 | Produk preorder tetap dikurangi stoknya (bisa negatif) dan dikembalikan saat batal | Stok tidak akurat | **Diperbaiki**: preorder tidak mengubah stok; CHECK `stock >= 0` |
| 9 | Tabel tanpa kunci asing dan tanpa CHECK, kolom relasi boleh kosong | Data yatim, harga atau stok negatif | **Diperbaiki**: migrasi `004` |
| 10 | Status dan kurir teks bebas; `courier` tidak divalidasi | Nilai ngawur | **Diperbaiki**: tabel acuan dan `exists:couriers,name` |
| 11 | Menghapus voucher yang sudah dipakai | Riwayat pemakaian hilang atau galat | **Diperbaiki**: ditolak, sarankan nonaktifkan |
| 12 | Tidak ada header keamanan | Clickjacking, sniffing tipe berkas | **Diperbaiki**: `SecurityHeaders` (CSP masih Report-Only) |
| 13 | `GET /pesanan/{token}/klaim` mengubah data | Dapat dipicu dari tautan luar | **Belum**: ubah ke POST (fitur 140/147) |
| 14 | Kode pesanan 5 digit acak tanpa pengecekan bentrok; cek pesanan hanya kode + nomor WA | Bentrok jadi galat 500; kode bisa ditebak | **Belum**: nomor dokumen otomatis (fitur 35) dan enkripsi WA (fitur 143) |
| 15 | Pembatalan otomatis 24 jam hanya mengubah status, tidak mengembalikan voucher | Kuota voucher bocor | **Belum**: fitur 39 (minggu 14) |
| 16 | Klaim voucher tidak menegakkan `per_user`, `used` dihitung manual | Pemakaian ganda mungkin | **Belum**: fitur 41 dan 77 |
| 17 | Tidak ada audit, uji otomatis, indeks selain kunci utama | Tidak bisa ditelusuri dan diukur | **Direncanakan**: minggu 12–15 pada kontrak |

## Catatan keterbatasan
Perubahan kode ini belum dijalankan terhadap Laravel nyata karena lingkungan penyusun tidak bisa memasang Laravel (Packagist diblokir). Yang sudah diperiksa: sintaks PHP (`php -l`) dan migrasi `001`–`004` yang dijalankan sungguhan di MariaDB 10.11 (21 tabel, 23 kunci asing, 11 CHECK; harga negatif, status ngawur, dan hapus produk yang sudah dipakai ditolak). Pemecah berkas SQL juga diuji lewat PDO. Belum diuji di MySQL 8 asli dan belum lewat Laravel. Jalankan dulu di database cadangan.
