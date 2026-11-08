@extends('layout')@section('c')<h1 class="font-serif text-3xl mb-4">Checkout</h1><form method="post" action="/checkout" class="space-y-3 max-w-lg">@csrf
@foreach(['name'=>'Nama lengkap','wa'=>'Nomor WhatsApp','address'=>'Alamat lengkap','kecamatan'=>'Kecamatan *'] as $k=>$t)<input name="{{ $k }}" placeholder="{{ $t }}" value="{{ old($k) }}" required class="w-full border rounded-lg p-3">@endforeach
<div class="flex gap-4"><label><input type="radio" name="courier" value="J&T" checked> J&T</label><label><input type="radio" name="courier" value="MGM Express"> MGM Express</label></div>
<textarea name="note" placeholder="Catatan pesanan (opsional)" class="w-full border rounded-lg p-3"></textarea>
<div class="bg-amber-50 border border-amber-200 p-3 rounded text-sm">Ongkir dikonfirmasi admin. <a target="_blank" class="underline" href="https://www.jet.co.id/track">Kira-kira berapa? Cek sendiri di J&T</a></div>
@php($cl=session('uid')?\App\Models\Voucher::whereIn('id',\DB::table('voucher_claims')->where('user_id',session('uid'))->pluck('voucher_id'))->pluck('code'):collect())
<input name="voucher" list="vl" placeholder="Kode voucher (opsional)" class="w-full border rounded-lg p-3"><datalist id="vl">@foreach($cl as $c)<option value="{{ $c }}">@endforeach</datalist><a href="/voucher" class="text-xs underline text-rose">Lihat voucher tersedia</a>
<p>Subtotal: <b>Rp{{ number_format($sub,0,',','.') }}</b></p><button class="w-full bg-rose text-white py-3 rounded-full">Buat Pesanan</button></form>@endsection
