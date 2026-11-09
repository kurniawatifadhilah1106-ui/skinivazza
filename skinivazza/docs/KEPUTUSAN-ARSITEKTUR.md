# Keputusan Arsitektur Skinivazza V2

## Teknologi
- Backend: Laravel 12.
- Database: MySQL/MariaDB.
- Frontend: Blade + CSS/JavaScript sederhana pada tahap awal agar mudah dirawat.
- Hosting: layanan hosting yang mendukung versi PHP Laravel, MySQL, HTTPS, dan penyimpanan file.
- Pengguna akhir: cukup membuka URL website melalui browser; tidak perlu memasang Laravel atau aplikasi tambahan.

## Pemisahan peran
- `admin_utama`: mengelola seluruh sistem, termasuk pengaturan toko (rekening, QRIS, BPOM, kontak).
- `admin_biasa`: mengelola operasional sesuai izin yang diberikan; tidak dapat mengubah pengaturan sensitif toko.
- `member`: pembeli dengan akun.
- `guest`: pembeli tanpa akun, dengan batasan fitur yang ditentukan pada tahap checkout.

## Aturan bisnis yang sudah disepakati
- Pesanan selesai ketika pembeli mengonfirmasi barang diterima.
- Jika pembeli tidak mengonfirmasi dan tidak ada komplain, pesanan otomatis selesai 7 hari setelah dikirim.
- Stok ditahan 24 jam setelah total akhir pesanan ditetapkan.
- Jika bukti pembayaran ditolak, masa penahanan stok tetap mengikuti masa banding 24 jam.
- Refund diproses manual oleh admin dan bukti transfer refund diunggah ke pesanan.
- Poin memiliki masa berlaku tepat 12 bulan.
- Video bukti maksimal 500 MB dan dihapus setelah 6 bulan sesuai kebijakan retensi.
- Perubahan poin dan voucher harus tercatat pada audit/ledger.
- Pengaturan rekening, QRIS, BPOM, dan kontak hanya dapat diubah admin utama.

## Hal yang harus dipastikan sebelum implementasi
Batas pengajuan retur/komplain pernah dibahas sebagai 3 hari setelah barang diterima dan juga 10 hari berdasarkan bukti penerimaan/pengiriman. Keduanya belum boleh digabung menjadi satu aturan. Aturan final perlu dipilih sebelum modul komplain dan retur dibuat.

## Satu sumber skema
Migration Laravel menjadi sumber skema database utama. Hindari menjalankan kumpulan SQL lama secara manual bersamaan dengan migration yang membuat tabel sama.
