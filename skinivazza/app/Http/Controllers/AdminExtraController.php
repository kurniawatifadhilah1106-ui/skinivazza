<?php
namespace App\Http\Controllers;
use App\Models\{Voucher,Reward,Redemption,RedemptionMessage,Product};
use App\Support\Points;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class AdminExtraController extends Controller{
 function vouchers(){return view('admin.vouchers',['v'=>Voucher::latest()->get(),'p'=>Product::all()]);}
 function voucherSave(Request $r){ $d=array_map(fn($x)=>$x===''?null:$x,$r->only(['title','description','code','type','value','max_discount','min_purchase','product_id','starts_at','ends_at','quota','per_user']));
  $d['code']=strtoupper($d['code']??Str::random(8)); $d['min_purchase']??=0; $d['members_only']=$r->has('members_only'); Voucher::create($d); return back(); }
 function voucherAct(Voucher $v,$act){ if($act=='delete'){ if(DB::table('voucher_uses')->where('voucher_id',$v->id)->exists()) return back()->with('err','Voucher sudah pernah dipakai, nonaktifkan saja'); $v->delete(); } else $v->update(['active'=>!$v->active]); return back(); }
 function rewards(){return view('admin.rewards',['r'=>Reward::latest()->get(),'d'=>Redemption::with('reward','user')->latest()->get()]);}
 function rewardSave(Request $r){ $d=array_map(fn($x)=>$x===''?null:$x,$r->only(['name','points','description','kind','region','stock','terms'])); $d['stock']??=0;
  if($r->hasFile('image')) $d['image']=$r->file('image')->store('rewards','public'); Reward::create($d); return back(); }
 function rewardToggle(Reward $r){ $r->update(['active'=>!$r->active]); return back(); }
 function redemption($id){ return view('admin.redemption',['d'=>Redemption::with('reward','user','msgs')->findOrFail($id)]); }
 function rdReply(Request $r,$id){ RedemptionMessage::create(['redemption_id'=>$id,'sender'=>'admin','body'=>$r->body]); return back(); }
 function rdAct($id,$act){ $d=Redemption::with('reward')->findOrFail($id);
  if($act=='approve'){ $rw=$d->reward; if($rw->stock<1)return back()->with('err','Stok hadiah habis'); if(!Points::spend($d->user_id,$rw->points))return back()->with('err','Saldo poin tidak cukup');
   $rw->decrement('stock'); $d->update(['status'=>in_array($rw->kind,['produk','merch','berharga'])?'sent':'ready']); }
  if($act=='reject') $d->update(['status'=>'rejected']);
  if($act=='done') $d->update(['status'=>'done']); return back(); }
 function settings(Request $r){ DB::table('settings')->updateOrInsert(['k'=>'open'],['v'=>$r->open]); DB::table('settings')->updateOrInsert(['k'=>'rest'],['v'=>$r->has('rest')?'1':'0']); return back(); }
}
