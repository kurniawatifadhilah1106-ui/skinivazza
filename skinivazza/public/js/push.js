function u8(b){const p='='.repeat((4-b.length%4)%4),s=(b+p).replace(/-/g,'+').replace(/_/g,'/'),r=atob(s);return Uint8Array.from([...r].map(c=>c.charCodeAt(0)))}
async function enablePush(scope){
 if(!('serviceWorker' in navigator)||!('PushManager' in window))return alert('Browser belum mendukung. Di iPhone, tambahkan web ke Layar Utama dulu.');
 const reg=await navigator.serviceWorker.register('/sw.js');
 if(await Notification.requestPermission()!=='granted')return;
 const sub=await reg.pushManager.subscribe({userVisibleOnly:true,applicationServerKey:u8(document.querySelector('meta[name=vapid]').content)});
 const j=sub.toJSON();
 const r=await fetch('/push/subscribe',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf]').content},body:JSON.stringify({endpoint:j.endpoint,p256dh:j.keys.p256dh,auth:j.keys.auth,scope})});
 alert(r.ok?'Notifikasi aktif ✓':'Gagal mengaktifkan');
}
