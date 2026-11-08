<?php
namespace App\Support;
use Minishlink\WebPush\{WebPush,Subscription};
use Illuminate\Support\Facades\DB;
class Push{
 static function to($scope,$title,$body='',$url='/'){ try{
  $subs=DB::table('push_subscriptions')->where('scope',$scope)->get(); if(!$subs->count())return;
  $w=new WebPush(['VAPID'=>['subject'=>config('app.url'),'publicKey'=>config('skinivazza.vapid_public'),'privateKey'=>config('skinivazza.vapid_private')]]);
  foreach($subs as $s) $w->queueNotification(Subscription::create(['endpoint'=>$s->endpoint,'keys'=>['p256dh'=>$s->p256dh,'auth'=>$s->auth]]),json_encode(compact('title','body','url')));
  foreach($w->flush() as $r) if($r->isSubscriptionExpired()) DB::table('push_subscriptions')->where('endpoint',$r->getRequest()->getUri()->__toString())->delete();
 }catch(\Throwable $e){ report($e); } }
}
