<?php
use App\Http\Controllers\{ShopController as S, AdminController as A, AccountController as C, PushController as P, MemberController as M, AdminExtraController as X};
use App\Http\Middleware\{AdminOnly, SecurityHeaders};
use Illuminate\Support\Facades\Route;

Route::middleware(SecurityHeaders::class)->group(function(){
 // ---- pembeli dan member
 Route::get('/',[S::class,'home']);
 Route::get('/katalog',[S::class,'catalog']);
 Route::get('/produk/{slug}',[S::class,'show']);
 Route::get('/keranjang',[S::class,'cart']);
 Route::post('/keranjang',[S::class,'cartSet']);
 Route::get('/checkout',[S::class,'checkout']);
 Route::post('/checkout',[S::class,'place']);
 Route::get('/cek-pesanan',[S::class,'lookup'])->middleware('throttle:20,1');
 Route::get('/pesanan/{token}',[S::class,'order']);
 Route::post('/pesanan/{token}/bukti',[S::class,'proof']);
 Route::post('/pesanan/{token}/pesan',[S::class,'message']);
 Route::post('/push/subscribe',[P::class,'store']);
 Route::get('/auth/google',[C::class,'redirect']);
 Route::get('/auth/google/callback',[C::class,'callback']);
 Route::get('/pesanan/{token}/klaim',[C::class,'claim']);
 Route::get('/akun',[C::class,'akun']);
 Route::get('/keluar',[C::class,'logout']);
 Route::get('/voucher',[M::class,'vouchers']); Route::post('/voucher/{v}/klaim',[M::class,'voucherClaim']);
 Route::get('/hadiah',[M::class,'rewards']); Route::post('/hadiah/{r}/tukar',[M::class,'redeem']);
 Route::get('/penukaran/{id}',[M::class,'redemption']); Route::post('/penukaran/{id}/pesan',[M::class,'rdMessage']);

 // ---- admin: masuk dibatasi 5 percobaan per menit, selebihnya wajib login dan izin sesuai peran
 Route::get('/admin/login',fn()=>view('admin.login'));
 Route::post('/admin/login',[A::class,'login'])->middleware('throttle:5,1');
 Route::prefix('admin')->group(function(){
  Route::post('/keluar',[A::class,'logout']);
  Route::middleware(AdminOnly::class.':pesanan.lihat')->group(function(){
   Route::get('/',[A::class,'index']);
   Route::get('/pesanan/{o}',[A::class,'show']);
  });
  Route::post('/pesanan/{o}/pesan',[A::class,'reply'])->middleware(AdminOnly::class.':chat.balas');
  Route::post('/pesanan/{o}/{act}',[A::class,'act'])->middleware(AdminOnly::class.':pesanan.kelola');
  Route::post('/produk/{p}',[A::class,'stock'])->middleware(AdminOnly::class.':stok.ubah');
  Route::middleware(AdminOnly::class.':promo.kelola')->group(function(){
   Route::get('/voucher',[X::class,'vouchers']); Route::post('/voucher',[X::class,'voucherSave']); Route::post('/voucher/{v}/{act}',[X::class,'voucherAct']);
  });
  Route::middleware(AdminOnly::class.':hadiah.kelola')->group(function(){
   Route::get('/hadiah',[X::class,'rewards']); Route::post('/hadiah',[X::class,'rewardSave']); Route::post('/hadiah/{r}/toggle',[X::class,'rewardToggle']);
   Route::get('/penukaran/{id}',[X::class,'redemption']); Route::post('/penukaran/{id}/pesan',[X::class,'rdReply']); Route::post('/penukaran/{id}/{act}',[X::class,'rdAct']);
  });
  Route::post('/pengaturan',[X::class,'settings'])->middleware(AdminOnly::class.':toko.atur');
 });
});
