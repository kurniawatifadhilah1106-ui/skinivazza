<?php
namespace App\Http\Controllers;
use App\Models\{Voucher,Reward,Redemption,RedemptionMessage};
use App\Support\{Points,Push};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class MemberController extends Controller{
 private function login($to){ session(['after'=>$to]); return redirect('/auth/google'); }
 function vouchers(){ $v=Voucher::where('active',true)->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>',now()))->get();
  $mine=session('uid')?DB::table('voucher_claims')->where('user_id',session('uid'))->pluck('voucher_id')->all():[]; return view('vouchers',compact('v','mine')); }
 function voucherClaim(Voucher $v){ if(!session('uid'))return $this->login('/voucher'); if($v->quota&&$v->used>=$v->quota)return back()->with('err','Kuota habis');
  DB::table('voucher_claims')->updateOrInsert(['voucher_id'=>$v->id,'user_id'=>session('uid')],['created_at'=>now()]); return back(); }
 function rewards(){ return view('rewards',['r'=>Reward::where('active',true)->get(),'bal'=>session('uid')?Points::balance(session('uid')):0]); }
 function redeem(Reward $r){ if(!session('uid'))return $this->login('/hadiah'); if(Points::balance(session('uid'))<$r->points)return back()->with('err','Poin belum cukup');
  $d=Redemption::create(['user_id'=>session('uid'),'reward_id'=>$r->id]); Push::to('admin','Permintaan tukar poin',$r->name,'/admin/penukaran/'.$d->id); return redirect('/penukaran/'.$d->id); }
 private function mine($id){ $d=Redemption::findOrFail($id); abort_unless($d->user_id==session('uid'),403); return $d; }
 function redemption($id){ return view('redemption',['d'=>$this->mine($id)->load('msgs','reward')]); }
 function rdMessage(Request $r,$id){ $d=$this->mine($id); RedemptionMessage::create(['redemption_id'=>$d->id,'sender'=>'buyer','body'=>$r->body]); Push::to('admin','Pesan penukaran #'.$d->id,(string)$r->body,'/admin/penukaran/'.$d->id); return back(); }
}
