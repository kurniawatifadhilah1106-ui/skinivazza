<?php
use App\Models\Order;
use Illuminate\Support\Facades\Schedule;
// Batalkan otomatis pesanan yang lewat batas 24 jam (ongkir / bayar / banding)
Schedule::call(fn()=>Order::whereIn('status',['waiting_shipping','waiting_payment','rejected'])->where('deadline_at','<',now())->update(['status'=>'cancelled']))->everyMinute();
// Pengingat: pesanan menunggu ongkir > 30 menit
Schedule::call(function(){ $n=Order::where('status','waiting_shipping')->where('created_at','<',now()->subMinutes(30))->count(); if($n) \App\Support\Push::to('admin','Pesanan belum diproses',$n.' pesanan menunggu ongkir >30 menit','/admin'); })->everyThirtyMinutes();
