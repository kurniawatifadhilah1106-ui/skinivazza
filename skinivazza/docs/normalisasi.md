# Normalisasi Skinivazza (fitur 28)

Dasar: skema awal `001`–`003`. Hasil akhirnya ada di `004_minggu10_fondasi.sql`.

## Bentuk normal pertama (1NF): nilai atomik, tanpa kelompok berulang
- `orders.address` menyimpan alamat dan kecamatan dalam satu teks (`"Jl. ..., Kec. X"`). Untuk keperluan pengiriman cukup satu teks, dan tidak ada laporan per kecamatan, jadi **dipertahankan**. Bila nanti perlu laporan per wilayah, pisahkan kolom `kecamatan`.
- Barang pada pesanan sudah berada di tabel terpisah `order_items` (tidak ada kolom `item1, item2`), jadi 1NF terpenuhi.

## Bentuk normal kedua (2NF): atribut bergantung pada seluruh kunci
Semua tabel memakai kunci utama tunggal (`id`), kecuali `role_permission` dan `user_roles` (kunci gabungan) yang hanya berisi kolom kunci dan `expires_at` (bergantung pada pasangan penuh). 2NF terpenuhi.

## Bentuk normal ketiga (3NF): tidak ada ketergantungan antaratribut non-kunci
| Temuan di skema awal | Masalah | Keputusan |
|---|---|---|
| `orders.status` dan `orders.courier` berupa teks bebas | Salah ketik tidak tertolak, label status tersebar di kode (`Order::LABEL`) | Dipindah ke tabel acuan `order_statuses` dan `couriers` dengan kunci asing (fitur 29) |
| `users.google_id` di tabel akun | Atribut milik identitas eksternal, bukan akun; tidak bisa menampung penyedia kedua | Dipindah ke `user_identities(provider, provider_user_id)` (fitur 82) |
| Peran admin tidak ada (satu kata sandi bersama) | Hak akses tertanam di kode | Tabel `roles`, `permissions`, `role_permission`, `user_roles` (fitur 13, 14) |
| `order_items.name` dan `order_items.price` menyalin data `products` | Tampak redundan | **Sengaja dipertahankan** sebagai salinan riwayat: harga dan nama pada saat membeli tidak boleh ikut berubah ketika produk diubah |
| `orders.subtotal`, `discount`, `total` dapat dihitung dari rincian | Data turunan | **Dipertahankan**: total harus tetap sama dengan yang dibayar pelanggan walau harga produk berubah; konsistensinya dijaga CHECK dan, pada minggu 12–13, prosedur tersimpan |
| `point_ledger.remaining` dan `vouchers.used` | Data turunan | **Dipertahankan** demi kecepatan dan penguncian; dijaga CHECK (`remaining <= points`, `used >= 0`) dan diuji pada fitur 77 |
| `rewards.kind` dan `rewards.region` teks bebas | Nilai terbatas | Dicatat sebagai pekerjaan lanjutan (tabel acuan), tidak mengganggu 3NF karena tidak ada ketergantungan antaratribut |

## Denormalisasi yang disengaja
`order_items.name/price`, `orders.subtotal/discount/total`, `point_ledger.remaining`, `vouchers.used`. Alasan: jejak riwayat dan kinerja. Setiap kolom diberi aturan CHECK atau uji agar tidak menyimpang.
