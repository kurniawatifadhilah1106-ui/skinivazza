<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Skinivazza</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; }
        body { margin: 0; background: #faf7f8; color: #30272c; }
        header { padding: 22px 6%; background: #fff; border-bottom: 1px solid #eee; }
        main { max-width: 1100px; margin: 32px auto; padding: 0 20px; }
        h1 { margin: 0; color: #8b526c; }
        .muted { color: #766b70; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(210px,1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 18px; }
        .price { color: #8b526c; font-weight: bold; }
    </style>
</head>
<body>
<header><h1>Skinivazza</h1><div class="muted">Beauty store</div></header>
<main>
    <h2>Produk pilihan</h2>
    <p class="muted">Fondasi etalase Skinivazza V2.</p>
    <section class="grid">
        @forelse ($products as $product)
            <article class="card">
                <strong>{{ $product->name }}</strong>
                <p class="muted">{{ $product->category?->name ?? 'Tanpa kategori' }}</p>
                <p class="price">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                <small>Stok: {{ $product->stock }}</small>
            </article>
        @empty
            <div class="card">Belum ada produk yang ditampilkan.</div>
        @endforelse
    </section>
    <div style="margin-top:24px">{{ $products->links() }}</div>
</main>
</body>
</html>
