<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model{
 protected $guarded=[]; protected $casts=['deadline_at'=>'datetime'];
 const LABEL=['waiting_shipping'=>'Menunggu Ongkir','waiting_payment'=>'Menunggu Pembayaran','waiting_confirm'=>'Menunggu Konfirmasi','rejected'=>'Bukti Ditolak','packing'=>'Dikemas','done'=>'Selesai (sudah dikirim)','cancelled'=>'Dibatalkan'];
 function items(){return $this->hasMany(OrderItem::class);}
 function messages(){return $this->hasMany(OrderMessage::class);}
 function label(){return self::LABEL[$this->status];}
}
