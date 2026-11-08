<?php
// Semua rahasia dan pengaturan khusus Skinivazza dibaca di sini (bukan env() di kode aplikasi),
// supaya tetap jalan setelah `php artisan config:cache`. Nilai asli hanya ada di .env yang TIDAK masuk Git.
return [
    'vapid_public'  => env('VAPID_PUBLIC'),
    'vapid_private' => env('VAPID_PRIVATE'),
];
