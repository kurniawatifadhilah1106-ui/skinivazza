<?php
namespace App\Http\Middleware;
use Closure;
// Fitur 145. CSP dipasang mode Report-Only dulu karena situs memakai Tailwind CDN dan skrip inline;
// setelah aman diuji, ganti nama header menjadi Content-Security-Policy.
class SecurityHeaders{
 function handle($r,Closure $n){
  $res=$n($r);
  $h=$res->headers;
  $h->set('X-Frame-Options','SAMEORIGIN');
  $h->set('X-Content-Type-Options','nosniff');
  $h->set('Referrer-Policy','strict-origin-when-cross-origin');
  $h->set('Permissions-Policy','camera=(), microphone=(), geolocation=()');
  if($r->isSecure()) $h->set('Strict-Transport-Security','max-age=31536000; includeSubDomains');
  $h->set('Content-Security-Policy-Report-Only',"default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: blob:; media-src 'self'; connect-src 'self'; frame-ancestors 'self'");
  return $res;
 }
}
