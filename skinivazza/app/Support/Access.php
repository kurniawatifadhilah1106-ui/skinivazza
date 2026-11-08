<?php
namespace App\Support;
use Illuminate\Support\Facades\DB;

// Pemeriksaan izin berdasarkan tabel roles, permissions, role_permission, dan user_roles (hak yang sudah lewat expires_at diabaikan).
class Access{
 private static array $cache=[];
 static function permissions(int $uid): array{
  return self::$cache[$uid]??=DB::table('user_roles as ur')
   ->join('role_permission as rp','rp.role_id','=','ur.role_id')
   ->join('permissions as p','p.id','=','rp.permission_id')
   ->where('ur.user_id',$uid)
   ->where(fn($q)=>$q->whereNull('ur.expires_at')->orWhere('ur.expires_at','>',now()))
   ->pluck('p.code')->unique()->values()->all();
 }
 static function can(int $uid,string $perm): bool{ return in_array($perm,self::permissions($uid),true); }
 static function isStaff(int $uid): bool{ return count(self::permissions($uid))>0; }
}
