self.addEventListener('push',e=>{const d=e.data?e.data.json():{};e.waitUntil(self.registration.showNotification(d.title||'Skinivazza',{body:d.body||'',data:{url:d.url||'/'},icon:'/images/products/dual-tone-up.jpg'}))});
self.addEventListener('notificationclick',e=>{e.notification.close();e.waitUntil(clients.openWindow(e.notification.data.url))});
