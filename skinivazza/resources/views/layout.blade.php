<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Skinivazza</title>
<meta name="csrf" content="{{ csrf_token() }}"><meta name="vapid" content="{{ config('skinivazza.vapid_public') }}">
<script src="https://cdn.tailwindcss.com"></script><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<script>tailwind.config={theme:{extend:{colors:{rose:{DEFAULT:'#b86b7a',soft:'#fbeef0',nude:'#f4dcdc'}},fontFamily:{serif:['Cormorant Garamond','serif'],sans:['Montserrat','sans-serif']}}}}</script>
<style>:root{--rg:linear-gradient(135deg,#ecc0c0,#c98390 55%,#a35a69)}body{background:#fffaf9}
.bg-rose{background:var(--rg)!important;box-shadow:0 10px 24px -10px #a35a6999}.bg-rose-soft{background:linear-gradient(180deg,#fff,#fbeef0)!important;border:1px solid #f4dcdc}
.btn{display:block;text-align:center;color:#fff;background:var(--rg);padding:.85rem 1.5rem;border-radius:999px;font-weight:500;letter-spacing:.05em;box-shadow:0 10px 24px -10px #a35a6999}
.btn-soft{border:1px solid #e4b1b4;color:#a35a69;padding:.75rem;border-radius:999px;background:#fff;font-weight:500}
.hero-card{background:var(--rg);box-shadow:0 20px 40px -20px #a35a69}.arch{border-radius:999px 999px 28px 28px;overflow:hidden}
a.block{transition:.25s}a.block:hover{transform:translateY(-4px);box-shadow:0 18px 30px -18px #a35a69}.rounded-2xl{border-radius:1.5rem!important}input,textarea{background:#fff}.eyebrow{letter-spacing:.35em;font-size:.65rem;text-transform:uppercase;color:#b86b7a}</style></head>
<body class="text-neutral-800 font-sans"><header class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-rose-nude z-10"><div class="max-w-5xl mx-auto flex items-center justify-between p-4">
<a href="/" class="font-serif text-2xl tracking-[.4em] text-neutral-700">SKINIVAZZA</a><nav class="flex gap-4 text-xs uppercase tracking-widest"><a href="/katalog">Katalog</a><a href="/cek-pesanan">Cek Pesanan</a><a href="/hadiah">Hadiah</a><a href="/voucher">Voucher</a><a href="/akun">Akun</a><a href="/keranjang" class="text-rose font-semibold">Keranjang ({{ count(session('cart',[])) }})</a></nav></div></header>
<main class="max-w-5xl mx-auto p-4">@if(session('err'))<p class="bg-red-50 text-red-700 p-3 rounded mb-4">{{ session('err') }}</p>@endif @yield('c')</main>
<footer class="text-center text-xs text-neutral-500 py-10 border-t border-rose-nude mt-12"><p class="font-serif text-2xl tracking-[.4em] text-neutral-600 mb-1">SKINIVAZZA</p>More than skincare, it's self love ♡<br><a class="underline" href="https://wa.me/62000000000">WhatsApp</a> · Shopee · TikTok</footer>
<a href="https://wa.me/62000000000" class="fixed bottom-4 right-4 bg-green-500 text-white rounded-full w-12 h-12 grid place-items-center shadow-lg">WA</a><script src="/js/push.js"></script></body></html>
