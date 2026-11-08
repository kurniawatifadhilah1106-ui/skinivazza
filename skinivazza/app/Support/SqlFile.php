<?php
namespace App\Support;
// Memecah isi berkas .sql menjadi pernyataan satu per satu (komentar -- dibuang, pemisah adalah titik koma di akhir baris).
// Berkas yang memuat prosedur/trigger (ada titik koma di dalam badan) memakai baris penanda "-- @@" sebagai pemisah antarpernyataan.
class SqlFile{
 static function statements(string $sql): array{
  $sql=preg_replace('/^\s*--(?!\s*@@).*$/m','',$sql);
  $sql=preg_replace('/\s--\s[^\n\']*$/m','',$sql);
  $parts=str_contains($sql,'-- @@')?preg_split('/^\s*--\s*@@\s*$/m',$sql):preg_split('/;[ \t]*\r?\n/',$sql);
  return array_values(array_filter(array_map(fn($s)=>trim(rtrim(trim($s),';')),$parts),fn($s)=>$s!==''));
 }
}
