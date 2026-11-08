<?php
namespace App\Http\Controllers;
use App\Models\{Product,Order,Voucher};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Support\Push;
use Illuminate\Support\{Str,Arr};
class ShopController extends Controller{
 function home(){return view('home',['p'=>Product::orderBy('id')->get()]);}
 function catalog(){return view('catalog',['p'=>Product::orderBy('id')->get()]);}
 function show($slug){return view('product',['p'=>Product::where('slug',$slug)->firstOrFail()]);}
 private function lines(){ $c=session('cart',[]); return Product::whereIn('slug',array_keys($c))->get()->map(fn($p)=>[$p,$c[$p->slug]]); }
 function cart(){$l=$this->lines();return view('cart',['l'=>$l,'sub'=>$l->sum(fn($x)=>$x[0]->price*$x[1])]);}
 function cartSet(Request $r){ $c=session('cart',[]); $p=Product::where('slug',$r->slug)->firstOrFail();
  if($r->mode=='add'&&$p->stock<1&&!$p->preorder) return back()->with('err','Produk habis');
  $q=$r->mode=='add'?($c[$p->slug]??0)+1:(int)$r->qty;
  if(!$p->preorder) $q=min($q,$p->stock);
  if($q>0) $c[$p->slug]=$q; else Arr::forget($c,$p->slug);
  session(['cart'=>$c]); return $r->mode=='add'?redirect('/keranjang'):back(); }
 function checkout(){$l=$this->lines(); if($l->isEmpty())return redirect('/katalog'); return view('checkout',['l'=>$l,'sub'=>$l->sum(fn($x)=>$x[0]->price*$x[1])]);}
 function place(Request $r){ $r->validate(['name'=>'required','wa'=>'required','address'=>'required','kecamatan'=>'required','courier'=>'required|exists:couriers,name']);
  $l=$this->lines(); $sub=$l->sum(fn($x)=>$x[0]->price*$x[1]);
  $disc=0;$vc=null; if($r->voucher){ [$v,$d]=Voucher::check($r->voucher,$l,session('uid')); if(!$v) return back()->withInput()->with('err',$d); $vc=$v; $disc=$d; }
  $o=Order::create(['code'=>'SV'.random_int(10000,99999),'token'=>Str::random(32),'name'=>$r->name,'wa'=>$r->wa,'address'=>$r->address.', Kec. '.$r->kecamatan,'courier'=>$r->courier,'note'=>$r->note,'subtotal'=>$sub,'total'=>$sub-$disc,'discount'=>$disc,'voucher_code'=>$vc?->code,'user_id'=>session('uid'),'deadline_at'=>now()->addDay()]);
  foreach($l as [$p,$q]) $o->items()->create(['product_id'=>$p->id,'name'=>$p->name,'price'=>$p->price,'qty'=>$q]);
  if($vc){ $vc->increment('used'); DB::table('voucher_uses')->insert(['voucher_id'=>$vc->id,'user_id'=>session('uid'),'order_id'=>$o->id,'created_at'=>now()]); } Push::to('admin','Pesanan baru #'.$o->code,'Menunggu ongkir','/admin/pesanan/'.$o->id); session()->forget('cart'); return redirect('/pesanan/'.$o->token); }
 function lookup(Request $r){ $o=$r->code?Order::where('code',$r->code)->where('wa',$r->wa)->first():null; return $o?redirect('/pesanan/'.$o->token):view('lookup'); }
 function order($t){return view('order',['o'=>Order::where('token',$t)->firstOrFail()->load('items','messages')]);}
 function proof(Request $r,$t){ $o=Order::where('token',$t)->firstOrFail(); abort_unless(in_array($o->status,['waiting_payment','rejected']),403);
  $r->validate(['proof'=>'required|image|max:5120']); $o->update(['proof'=>$r->file('proof')->store('proofs','public'),'status'=>'waiting_confirm','deadline_at'=>null]); Push::to('admin','Bukti baru #'.$o->code,'Cek pembayaran','/admin/pesanan/'.$o->id); return back(); }
 function message(Request $r,$t){ $o=Order::where('token',$t)->firstOrFail(); $r->validate(['body'=>'required_without:file','file'=>'nullable|file|max:512000|mimes:jpg,jpeg,png,mp4,mov']);
  $o->messages()->create(['sender'=>'buyer','body'=>$r->body,'file'=>$r->hasFile('file')?$r->file('file')->store('chat','public'):null]); Push::to('admin','Pesan baru #'.$o->code,(string)$r->body,'/admin/pesanan/'.$o->id); return back(); }
}
