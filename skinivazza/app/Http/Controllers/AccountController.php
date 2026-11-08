<?php
namespace App\Http\Controllers;
use App\Models\{User,Order};
use App\Support\Points;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
class AccountController extends Controller{
 function redirect(){return Socialite::driver('google')->redirect();}
 function callback(){ $g=Socialite::driver('google')->user();
  // cari lewat identitas eksternal dulu, lalu lewat surel (akun lama), baru buat akun baru
  $idn=DB::table('user_identities')->where('provider','google')->where('provider_user_id',$g->getId())->first();
  $u=$idn?User::find($idn->user_id):User::firstOrCreate(['email'=>strtolower($g->getEmail())],['name'=>$g->getName()]);
  if(($u->status??'active')!=='active') abort(403,'Akun dinonaktifkan');
  DB::table('user_identities')->updateOrInsert(['provider'=>'google','provider_user_id'=>$g->getId()],['user_id'=>$u->id,'created_at'=>now()]);
  session()->regenerate(); session(['uid'=>$u->id]); return redirect(session()->pull('after','/akun')); }
 function claim($t){ $o=Order::where('token',$t)->firstOrFail(); if(!session('uid')){session(['after'=>'/pesanan/'.$t.'/klaim']);return redirect('/auth/google');}
  if(!$o->user_id){$o->update(['user_id'=>session('uid')]); if($o->status=='done') Points::award($o->fresh());} return redirect('/pesanan/'.$t); }
 function akun(){ if(!session('uid')){session(['after'=>'/akun']);return redirect('/auth/google');} $u=User::find(session('uid'));
  return view('akun',['u'=>$u,'bal'=>Points::balance($u->id),'led'=>Points::active($u->id),'orders'=>Order::where('user_id',$u->id)->latest()->get()]); }
 function logout(){session()->forget('uid');return redirect('/');}
}
