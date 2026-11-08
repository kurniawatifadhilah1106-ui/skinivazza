# Kamus Data Skinivazza

Dihasilkan dari database MySQL (MariaDB 10.11) hasil menjalankan `database/sql/001` sampai `004` (21 tabel). Kolom auto_increment terisi otomatis; kolom wajib tidak boleh kosong.

## couriers
Acuan kurir

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `name` | varchar(100) | ya |  | UNIQUE | Nama kurir (unik) |
| `active` | tinyint(1) | ya | `1` |  | Penanda aktif (true) atau nonaktif (false) |

## order_items
Rincian barang per pesanan

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `order_id` | int(11) | ya |  | FK→orders | Pesanan induk (orders.id), ikut terhapus bila pesanan dihapus |
| `product_id` | int(11) | ya |  | FK→products | Produk terkait (products.id) |
| `name` | varchar(255) | tidak |  |  | Nama produk saat dibeli (salinan riwayat) |
| `price` | int(11) | tidak |  |  | Harga satuan saat dibeli (salinan riwayat) |
| `qty` | int(11) | tidak |  |  | Jumlah barang |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |

Aturan nilai (CHECK):
- ``qty` > 0 and `price` >= 0`

Kunci asing dan aturan hapus:
- `order_id` → `orders` — ON DELETE CASCADE, ON UPDATE RESTRICT
- `product_id` → `products` — ON DELETE RESTRICT, ON UPDATE RESTRICT

## order_messages
Chat per pesanan

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `order_id` | int(11) | ya |  | FK→orders | Pesanan terkait (orders.id) |
| `sender` | varchar(20) | tidak |  |  | Pengirim pesan: buyer atau admin |
| `body` | text | tidak |  |  | Isi pesan |
| `file` | varchar(255) | tidak |  |  | Berkas foto atau video lampiran chat |
| `internal` | tinyint(1) | tidak | `0` |  | true bila catatan internal admin yang tidak terlihat pembeli |
| `created_at` | datetime | tidak | `current_timestamp()` |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |

Kunci asing dan aturan hapus:
- `order_id` → `orders` — ON DELETE CASCADE, ON UPDATE RESTRICT

## order_statuses
Acuan status pesanan

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `code` | varchar(30) | ya |  | PK | Kode status, kunci utama |
| `label` | varchar(100) | ya |  |  | Teks status untuk ditampilkan |
| `urutan` | int(11) | ya |  | UNIQUE | Urutan alur status |

## orders
Kepala pesanan

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `code` | varchar(20) | ya |  | UNIQUE | Kode pesanan untuk pelanggan, contoh SV12345 |
| `token` | varchar(64) | ya |  | UNIQUE | Token acak 32 karakter untuk tautan pesanan |
| `name` | varchar(150) | tidak |  |  | Nama penerima |
| `wa` | varchar(30) | tidak |  |  | Nomor WhatsApp penerima |
| `address` | text | tidak |  |  | Alamat lengkap beserta kecamatan |
| `courier` | varchar(100) | tidak |  | FK→couriers | Kurir terpilih (couriers.name) |
| `note` | text | tidak |  |  | Catatan pembeli |
| `subtotal` | int(11) | tidak |  |  | Jumlah harga barang sebelum diskon |
| `shipping` | int(11) | tidak |  |  | Ongkos kirim, diisi admin; kosong sampai ditetapkan |
| `discount` | int(11) | tidak | `0` |  | Potongan dari voucher |
| `total` | int(11) | tidak |  |  | Total bayar = subtotal - diskon + ongkir |
| `status` | varchar(30) | ya | `'waiting_shipping'` | FK→order_statuses | Status pesanan (order_statuses.code) |
| `proof` | varchar(255) | tidak |  |  | Berkas bukti pembayaran |
| `reject_reason` | text | tidak |  |  | Alasan bukti ditolak |
| `resi` | varchar(100) | tidak |  |  | Nomor resi pengiriman |
| `deadline_at` | datetime | tidak |  |  | Batas waktu tindakan sebelum dibatalkan otomatis (24 jam) |
| `created_at` | datetime | tidak | `current_timestamp()` |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |
| `user_id` | int(11) | tidak |  | FK→users | Akun member pemilik pesanan; kosong untuk pembeli tamu |
| `points_awarded` | tinyint(1) | tidak | `0` |  | true bila poin pesanan sudah diberikan |
| `voucher_code` | varchar(50) | tidak |  |  | Kode voucher yang dipakai |

Aturan nilai (CHECK):
- ``subtotal` >= 0 and coalesce(`shipping`,0) >= 0 and `discount` >= 0 and coalesce(`total`,0) >= 0`

Kunci asing dan aturan hapus:
- `courier` → `couriers` — ON DELETE RESTRICT, ON UPDATE CASCADE
- `status` → `order_statuses` — ON DELETE RESTRICT, ON UPDATE CASCADE
- `user_id` → `users` — ON DELETE SET NULL, ON UPDATE RESTRICT

## permissions
Izin

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `code` | varchar(100) | ya |  | UNIQUE | Kode izin, contoh pesanan.kelola |
| `description` | varchar(255) | ya |  |  | Deskripsi |

## point_ledger
Buku besar poin

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `user_id` | int(11) | ya |  | FK→users | Akun pemilik (users.id) |
| `order_id` | int(11) | tidak |  | FK→orders | Pesanan terkait (orders.id) |
| `points` | int(11) | tidak |  |  | Poin yang diberikan |
| `remaining` | int(11) | tidak |  |  | Sisa poin yang belum dipakai (0 sampai points) |
| `expires_at` | datetime | tidak |  |  | Tanggal poin kedaluwarsa (12 bulan) |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |

Aturan nilai (CHECK):
- ``points` > 0 and `remaining` >= 0 and `remaining` <= `points``

Kunci asing dan aturan hapus:
- `order_id` → `orders` — ON DELETE SET NULL, ON UPDATE RESTRICT
- `user_id` → `users` — ON DELETE CASCADE, ON UPDATE RESTRICT

## products
Master produk yang dijual

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `slug` | varchar(150) | ya |  | UNIQUE | Penanda URL unik produk, contoh salmon-soap |
| `name` | varchar(255) | ya |  |  | Nama |
| `price` | int(11) | ya |  |  | Harga jual (rupiah, tidak boleh negatif) |
| `description` | text | tidak |  |  | Deskripsi |
| `ingredients` | text | tidak |  |  | Daftar komposisi |
| `usage` | text | tidak |  |  | Cara pemakaian |
| `bpom` | varchar(100) | tidak |  |  | Nomor izin edar BPOM |
| `image` | varchar(255) | tidak |  |  | Nama berkas gambar |
| `stock` | int(11) | tidak | `10` |  | Stok tersedia, tidak boleh negatif |
| `preorder` | tinyint(1) | tidak | `0` |  | true bila produk dijual secara preorder (stok tidak dikurangi) |
| `created_at` | datetime | tidak | `current_timestamp()` |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |

Aturan nilai (CHECK):
- ``price` >= 0`
- ``stock` >= 0`

## push_subscriptions
Langganan notifikasi push

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `scope` | varchar(100) | tidak |  |  | Sasaran: admin atau order:{token} |
| `endpoint` | varchar(700) | tidak |  | UNIQUE | URL langganan push dari peramban (unik) |
| `p256dh` | varchar(255) | tidak |  |  | Kunci publik langganan |
| `auth` | varchar(255) | tidak |  |  | Rahasia autentikasi langganan |

## redemption_messages
Chat per penukaran

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `redemption_id` | int(11) | ya |  | FK→redemptions | Penukaran induk |
| `sender` | varchar(20) | tidak |  |  | Pengirim pesan: buyer atau admin |
| `body` | text | tidak |  |  | Isi pesan |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |

Kunci asing dan aturan hapus:
- `redemption_id` → `redemptions` — ON DELETE CASCADE, ON UPDATE RESTRICT

## redemptions
Permintaan penukaran poin

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `user_id` | int(11) | ya |  | FK→users | Akun pemilik (users.id) |
| `reward_id` | int(11) | ya |  | FK→rewards | Hadiah yang ditukar |
| `status` | varchar(20) | tidak | `'pending'` |  | pending, ready, sent, done, atau rejected |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |

Kunci asing dan aturan hapus:
- `reward_id` → `rewards` — ON DELETE RESTRICT, ON UPDATE RESTRICT
- `user_id` → `users` — ON DELETE CASCADE, ON UPDATE RESTRICT

## rewards
Hadiah penukaran poin

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `name` | varchar(255) | tidak |  |  | Nama |
| `image` | varchar(255) | tidak |  |  | Nama berkas gambar |
| `points` | int(11) | tidak |  |  | Poin yang dibutuhkan untuk menukar |
| `description` | text | tidak |  |  | Deskripsi |
| `kind` | varchar(30) | tidak |  |  | Jenis hadiah: produk, merch, berharga, dll. |
| `region` | varchar(100) | tidak |  |  | Wilayah berlaku hadiah |
| `stock` | int(11) | tidak | `0` |  | Stok hadiah |
| `terms` | text | tidak |  |  | Syarat dan ketentuan |
| `active` | tinyint(1) | tidak | `1` |  | Penanda aktif (true) atau nonaktif (false) |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |

Aturan nilai (CHECK):
- ``points` > 0 and `stock` >= 0`

## role_permission
Peran dan izinnya

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `role_id` | int(11) | ya |  | PK, FK→roles | Peran |
| `permission_id` | int(11) | ya |  | PK, FK→permissions | Izin yang dimiliki peran |

Kunci asing dan aturan hapus:
- `permission_id` → `permissions` — ON DELETE CASCADE, ON UPDATE RESTRICT
- `role_id` → `roles` — ON DELETE CASCADE, ON UPDATE RESTRICT

## roles
Peran

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `code` | varchar(50) | ya |  | UNIQUE | Kode peran: super_admin, admin_gudang, admin_cs, member |
| `name` | varchar(100) | ya |  |  | Nama |
| `parent_role_id` | int(11) | tidak |  | FK→roles | Peran induk untuk pewarisan hak (fitur 17) |

Kunci asing dan aturan hapus:
- `parent_role_id` → `roles` — ON DELETE SET NULL, ON UPDATE RESTRICT

## settings
Pengaturan toko

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `k` | varchar(50) | ya |  | PK | Nama pengaturan (open, rest) |
| `v` | varchar(255) | tidak |  |  | Nilai pengaturan |

## user_identities
Identitas eksternal akun

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `user_id` | int(11) | ya |  | FK→users | Akun pemilik identitas |
| `provider` | varchar(30) | ya |  |  | Penyedia identitas, contoh google |
| `provider_user_id` | varchar(190) | ya |  |  | ID akun pada penyedia |
| `created_at` | datetime | ya | `current_timestamp()` |  | Waktu baris dibuat |

Unik gabungan: (provider, provider_user_id); (user_id, provider)

Kunci asing dan aturan hapus:
- `user_id` → `users` — ON DELETE CASCADE, ON UPDATE RESTRICT

## user_roles
Peran milik akun

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `user_id` | int(11) | ya |  | PK, FK→users | Akun pemilik (users.id) |
| `role_id` | int(11) | ya |  | PK, FK→roles | Peran yang ditetapkan |
| `expires_at` | datetime | tidak |  |  | Batas berlaku peran; kosong berarti permanen (fitur 21) |

Kunci asing dan aturan hapus:
- `role_id` → `roles` — ON DELETE RESTRICT, ON UPDATE RESTRICT
- `user_id` → `users` — ON DELETE CASCADE, ON UPDATE RESTRICT

## users
Akun member dan admin

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `name` | varchar(150) | tidak |  |  | Nama |
| `email` | varchar(190) | ya |  | UNIQUE | Surel unik akun (huruf kecil) |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |
| `username` | varchar(100) | tidak |  | UNIQUE | Nama pengguna unik untuk masuk (opsional) |
| `password` | varchar(255) | tidak |  |  | Hash bcrypt kata sandi; kosong untuk akun yang hanya masuk lewat Google |
| `status` | varchar(20) | ya | `'active'` |  | active atau disabled |
| `last_login_at` | datetime | tidak |  |  | Waktu masuk terakhir |

Aturan nilai (CHECK):
- ``status` in ('active','disabled')`

## voucher_claims
Klaim voucher

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `voucher_id` | int(11) | ya |  | FK→vouchers | Voucher yang diklaim |
| `user_id` | int(11) | ya |  | FK→users | Akun pemilik (users.id) |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |

Unik gabungan: (voucher_id, user_id)

Kunci asing dan aturan hapus:
- `user_id` → `users` — ON DELETE CASCADE, ON UPDATE RESTRICT
- `voucher_id` → `vouchers` — ON DELETE CASCADE, ON UPDATE RESTRICT

## voucher_uses
Pemakaian voucher

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `voucher_id` | int(11) | ya |  | FK→vouchers | Voucher yang dipakai |
| `user_id` | int(11) | tidak |  | FK→users | Pemakai; kosong untuk tamu |
| `order_id` | int(11) | tidak |  | FK→orders | Pesanan terkait (orders.id) |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |

Kunci asing dan aturan hapus:
- `order_id` → `orders` — ON DELETE SET NULL, ON UPDATE RESTRICT
- `user_id` → `users` — ON DELETE SET NULL, ON UPDATE RESTRICT
- `voucher_id` → `vouchers` — ON DELETE RESTRICT, ON UPDATE RESTRICT

## vouchers
Voucher diskon

| Kolom | Tipe | Wajib | Bawaan | Kunci | Arti |
|---|---|---|---|---|---|
| `id` | int(11) | ya |  | PK | Kunci utama (nomor urut otomatis) |
| `title` | varchar(255) | tidak |  |  | Judul voucher |
| `description` | text | tidak |  |  | Deskripsi |
| `code` | varchar(50) | ya |  | UNIQUE | Kode unik |
| `type` | varchar(20) | tidak |  |  | percent (persen) atau fixed (nominal) |
| `value` | int(11) | tidak |  |  | Nilai potongan: persen atau rupiah |
| `max_discount` | int(11) | tidak |  |  | Batas potongan untuk tipe persen |
| `min_purchase` | int(11) | tidak | `0` |  | Minimal belanja |
| `members_only` | tinyint(1) | tidak | `0` |  | true bila khusus member |
| `product_id` | int(11) | tidak |  | FK→products | Produk khusus yang berlaku (kosong = semua) |
| `starts_at` | datetime | tidak |  |  | Mulai berlaku |
| `ends_at` | datetime | tidak |  |  | Berakhir |
| `quota` | int(11) | tidak |  |  | Kuota total pemakaian (kosong = tak terbatas) |
| `per_user` | int(11) | tidak | `1` |  | Batas pemakaian per pengguna |
| `used` | int(11) | tidak | `0` |  | Jumlah pemakaian sejauh ini |
| `active` | tinyint(1) | tidak | `1` |  | Penanda aktif (true) atau nonaktif (false) |
| `created_at` | datetime | tidak |  |  | Waktu baris dibuat |
| `updated_at` | datetime | tidak |  |  | Waktu baris terakhir diubah |

Aturan nilai (CHECK):
- ``used` >= 0 and (`quota` is null or `quota` > 0) and (`per_user` is null or `per_user` >= 1) and `min_purchase` >= 0`
- ``starts_at` is null or `ends_at` is null or `ends_at` > `starts_at``
- ``type` in ('percent','fixed')`
- ``value` > 0 and (`type` <> 'percent' or `value` <= 100)`

Kunci asing dan aturan hapus:
- `product_id` → `products` — ON DELETE SET NULL, ON UPDATE RESTRICT
