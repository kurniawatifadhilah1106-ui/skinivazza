<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PushController extends Controller{
 function store(Request $r){ $r->validate(['endpoint'=>'required','p256dh'=>'required','auth'=>'required','scope'=>'required']);
  abort_if($r->scope=='admin'&&!session('admin_id'),403);
  DB::table('push_subscriptions')->updateOrInsert(['endpoint'=>$r->endpoint],$r->only('scope','p256dh','auth')); return response()->json(['ok'=>1]); }
}
