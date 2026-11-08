<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB,Hash};

class BuatAdmin extends Command{
 protected $signature='admin:buat {email} {nama} {peran=super_admin : super_admin|admin_gudang|admin_cs} {--password=}';
 protected $description='Membuat akun admin dengan kata sandi ber-hash dan menetapkan perannya';
 function handle(){
  $role=DB::table('roles')->where('code',$this->argument('peran'))->first();
  if(!$role||$role->code==='member'){ $this->error('Peran tidak dikenal.'); return 1; }
  $pw=$this->option('password')?:$this->secret('Kata sandi (minimal 10 karakter)');
  if(strlen((string)$pw)<10){ $this->error('Kata sandi minimal 10 karakter.'); return 1; }
  $email=strtolower($this->argument('email'));
  $u=User::updateOrCreate(['email'=>$email],['name'=>$this->argument('nama'),'username'=>strstr($email,'@',true),'password'=>Hash::make($pw),'status'=>'active']);
  DB::table('user_roles')->updateOrInsert(['user_id'=>$u->id,'role_id'=>$role->id],['expires_at'=>null]);
  $this->info("Admin {$u->email} dibuat dengan peran {$role->name}.");
  return 0;
 }
}
