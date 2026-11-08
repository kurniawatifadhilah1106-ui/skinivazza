<?php
namespace App\Http\Controllers;
use App\Models\{Order,Product,User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Hash};
use App\Support\{Push,Points,Access};
class AdminController extends Controller{
 // Aksi => status yang boleh menjadi asalnya (mencegah loncatan status, misalnya memberi resi pada pesanan yang belum dibayar)
 const FLOW=[
  'total'=>['waiting_shipping'],
  'confirm'=>['waiting_confirm'],
  'reject'=>['waiting_confirm'],
  'resi'=>['packing'],
  'cancel'=>['waiting_shipping','waiting_payment','waiting_confirm','rejected','packing'],
  'revoke'=>['done'],
 ];
 function login(Request $r){
  $r->validate(['email'=>'required|string|max:190','password'=>'required|string|max:200']);
  $id=strtolower(trim($r->email));
  $u=User::where('email',$id)->orWhere('username',$id)->first();
  // pesan galat sama untuk semua kegagalan agar tidak membocorkan akun mana yang ada
  if(!$u||!$u->password||$u->status!=='active'||!Hash::check($r->password,$u->password)||!Access::isStaff($u->id)) return back()->with('err','Email atau kata sandi salah');
  $r->session()->regenerate(); session(['admin_id'=>$u->id]); $u->update(['last_login_at'=>now()]);
  return redirect('/admin'); }
 function logout(Request $r){ session()->forget('admin_id'); $r->session()->regenerate(); return redirect('/admin/login'); }
 function index(){return view('admin.index',['o'=>Order::latest()->get(),'p'=>Product::orderBy('id')->get()]);}
 function show(Order $o){return view('admin.show',['o'=>$o->load('items','messages')]);}
 function reply(Request $r,Order $o){ $r->validate(['body'=>'required|string|max:2000']); $o->messages()->create(['sender'=>'admin','body'=>$r->body,'internal'=>$r->has('internal')]); if(!$r->has('internal')) Push::to('order:'.$o->token,'Balasan admin #'.$o->code,(string)$r->body,'/pesanan/'.$o->token); return back(); }
 function stock(Request $r,Product $p){ $r->validate(['stock'=>'required|integer|min:0']); $p->update(['stock'=>(int)$r->stock,'preorder'=>$r->has('preorder')]); return back(); }
 function act(Request $r,Order $o,$act){
  $allowed=self::FLOW[$act]??abort(404);
  if(!in_array($o->status,$allowed,true)) return back()->with('err','Aksi tidak bisa dilakukan pada status "'.$o->label().'"');
  if($act=='total'){ $r->validate(['shipping'=>'required|integer|min:0']); $o->update(['shipping'=>(int)$r->shipping,'total'=>$o->subtotal-$o->discount+(int)$r->shipping,'status'=>'waiting_payment','deadline_at'=>now()->addDay()]); }
  if($act=='confirm'){
   // satu transaksi: cek stok, kurangi stok, dan ubah status berhasil semua atau batal semua (produk preorder tidak mengurangi stok)
   $err=DB::transaction(function() use($o){
    $ord=Order::whereKey($o->id)->lockForUpdate()->first(); if($ord->status!=='waiting_confirm') return 'Status pesanan sudah berubah';
    $items=$ord->items()->get(); $prod=Product::whereIn('id',$items->pluck('product_id'))->lockForUpdate()->get()->keyBy('id');
    foreach($items as $i){ $p=$prod[$i->product_id]??null; if(!$p) return 'Produk '.$i->name.' sudah tidak ada'; if(!$p->preorder&&$p->stock<$i->qty) return 'Stok '.$p->name.' tidak cukup'; }
    foreach($items as $i){ $p=$prod[$i->product_id]; if(!$p->preorder) $p->decrement('stock',$i->qty); }
    $ord->update(['status'=>'packing']); return null; });
   if($err) return back()->with('err',$err); }
  if($act=='reject'){ $r->validate(['reason'=>'required|string|max:500']); $o->update(['status'=>'rejected','reject_reason'=>$r->reason,'deadline_at'=>now()->addDay()]); }
  if($act=='resi'){ $r->validate(['resi'=>'required|string|max:100']); $o->update(['status'=>'done','resi'=>$r->resi]); }
  if($act=='cancel'){
   DB::transaction(function() use($o){
    if($o->status=='packing') foreach($o->items as $i){ $p=Product::find($i->product_id); if($p&&!$p->preorder) $p->increment('stock',$i->qty); }
    $o->update(['status'=>'cancelled']);
    if($o->voucher_code) \App\Models\Voucher::where('code',$o->voucher_code)->where('used','>',0)->decrement('used'); }); }
  if($act=='revoke') DB::table('point_ledger')->where('order_id',$o->id)->update(['remaining'=>0]); // tarik poin manual saat retur/refund
  $m=['total'=>['Total pesanan siap','Silakan lakukan pembayaran'],'reject'=>['Bukti ditolak',(string)$o->reject_reason],'resi'=>['Pesanan dikirim','Resi '.$o->resi],'cancel'=>['Pesanan dibatalkan','']];
  if(isset($m[$act])) Push::to('order:'.$o->token,$m[$act][0].' #'.$o->code,$m[$act][1],'/pesanan/'.$o->token);
  if($act=='resi') Points::award($o->fresh());
  return back(); }
}
