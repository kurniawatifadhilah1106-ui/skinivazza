<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Voucher extends Model{ protected $guarded=[]; protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime'];
 // $lines = koleksi [Product,qty]. Return [voucher|null, diskon|pesan error]
 static function check($code,$lines,$uid){
  $v=self::where('code',strtoupper(trim($code)))->where('active',true)->first(); if(!$v)return [null,'Kode voucher tidak ditemukan'];
  if(($v->starts_at&&now()<$v->starts_at)||($v->ends_at&&now()>$v->ends_at))return [null,'Voucher tidak berlaku saat ini'];
  if($v->quota&&$v->used>=$v->quota)return [null,'Kuota voucher habis'];
  if($v->members_only&&!$uid)return [null,'Voucher khusus member'];
  $sub=$lines->sum(fn($x)=>$x[0]->price*$x[1]); $base=$v->product_id?$lines->filter(fn($x)=>$x[0]->id==$v->product_id)->sum(fn($x)=>$x[0]->price*$x[1]):$sub;
  if($base<=0)return [null,'Voucher tidak berlaku untuk produk di keranjang'];
  if($sub<$v->min_purchase)return [null,'Minimal belanja Rp'.number_format($v->min_purchase,0,',','.')];
  if($uid&&$v->per_user&&DB::table('voucher_uses')->where('voucher_id',$v->id)->where('user_id',$uid)->count()>=$v->per_user)return [null,'Batas pemakaian voucher tercapai'];
  $d=$v->type=='percent'?intdiv($base*$v->value,100):$v->value; if($v->type=='percent'&&$v->max_discount)$d=min($d,$v->max_discount);
  return [$v,min($d,$base)]; } }
