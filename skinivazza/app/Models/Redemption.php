<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Redemption extends Model{ protected $guarded=[];
 const LABEL=['pending'=>'Menunggu','sent'=>'Dikirim','ready'=>'Siap Digunakan','done'=>'Selesai','rejected'=>'Ditolak'];
 function label(){return self::LABEL[$this->status];}
 function reward(){return $this->belongsTo(Reward::class);} function user(){return $this->belongsTo(User::class);} function msgs(){return $this->hasMany(RedemptionMessage::class);} }
