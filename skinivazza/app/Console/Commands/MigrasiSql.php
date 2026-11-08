<?php
namespace App\Console\Commands;
use App\Support\SqlFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Menjalankan database/sql/NNN_*.sql berurutan, sekali per berkas (dicatat di tabel schema_migrations). Fitur 25.
class MigrasiSql extends Command{
 protected $signature='db:migrasi';
 protected $description='Menjalankan migrasi SQL bernomor yang belum dijalankan';
 function handle(){
  DB::unprepared('create table if not exists schema_migrations(version varchar(10) primary key, filename varchar(190) not null, applied_at datetime not null default current_timestamp)');
  $files=glob(database_path('sql/[0-9][0-9][0-9]_*.sql')); sort($files);
  foreach($files as $f){
   $v=substr(basename($f),0,3);
   if(DB::table('schema_migrations')->where('version',$v)->exists()){ $this->line('lewati   '.basename($f)); continue; }
   $this->info('jalankan '.basename($f));
   foreach(SqlFile::statements(file_get_contents($f)) as $i=>$s){
    try{ DB::unprepared($s); }catch(\Throwable $e){ $this->error('Gagal di pernyataan #'.($i+1).' pada '.basename($f).': '.$e->getMessage()); $this->line($s); return 1; }
   }
   DB::table('schema_migrations')->insert(['version'=>$v,'filename'=>basename($f)]);
  }
  $this->info('selesai'); return 0;
 }
}
