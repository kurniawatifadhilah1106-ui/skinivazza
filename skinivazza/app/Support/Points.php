<?php
namespace App\Support;
use Illuminate\Support\Facades\DB;
class Points{
 // Rp10.000 = 5 poin, tanpa ongkir, berlaku 12 bulan tepat
 static function award($o){ if(!$o->user_id||$o->points_awarded)return; $p=intdiv(max(0,$o->subtotal-$o->discount),10000)*5;
  if($p>0) DB::table('point_ledger')->insert(['user_id'=>$o->user_id,'order_id'=>$o->id,'points'=>$p,'remaining'=>$p,'expires_at'=>now()->addMonths(12),'created_at'=>now()]);
  $o->update(['points_awarded'=>true]); }
 static function active($u){return DB::table('point_ledger')->where('user_id',$u)->where('remaining','>',0)->where('expires_at','>',now())->orderBy('expires_at')->get();}
 static function spend($u,$n){ if(self::balance($u)<$n)return false; foreach(self::active($u) as $r){ if($n<=0)break; $t=min($n,$r->remaining); DB::table('point_ledger')->where('id',$r->id)->decrement('remaining',$t); $n-=$t; } return true; }
 static function balance($u){return self::active($u)->sum('remaining');}
}
