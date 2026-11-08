<?php
namespace App\Http\Middleware;
use App\Models\User;
use App\Support\Access;
use Closure;
// Pemakaian di rute: AdminOnly::class.':pesanan.kelola'  (izin opsional)
class AdminOnly{
 function handle($r,Closure $n,?string $perm=null){
  $u=session('admin_id')?User::find(session('admin_id')):null;
  if(!$u||$u->status!=='active'||!Access::isStaff($u->id)){ session()->forget('admin_id'); return redirect('/admin/login'); }
  if($perm&&!Access::can($u->id,$perm)) abort(403,'Peran Anda tidak memiliki izin ini');
  return $n($r);
 }
}
