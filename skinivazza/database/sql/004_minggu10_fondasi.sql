-- 004: Fondasi minggu 10 (fitur 01, 02, 13, 14, 23-29, 82)
-- Jalankan setelah 003 (php artisan db:migrasi). MySQL tidak membatalkan DDL otomatis bila gagal di tengah,
-- jadi untuk database baru cukup DROP DATABASE lalu ulangi bila ada galat. Bila sudah ada data lama, bersihkan data yatim
-- lebih dulu (pesanan dengan kurir atau status di luar tabel acuan, baris dengan user_id yang sudah tidak ada).

-- ===== Fitur 29: tabel acuan menggantikan teks bebas =====
create table order_statuses(
  code varchar(30) primary key,
  label varchar(100) not null,
  urutan int not null unique
);
insert into order_statuses(code,label,urutan) values
 ('waiting_shipping','Menunggu Ongkir',1),
 ('waiting_payment','Menunggu Pembayaran',2),
 ('waiting_confirm','Menunggu Konfirmasi',3),
 ('rejected','Bukti Ditolak',4),
 ('packing','Dikemas',5),
 ('done','Selesai (sudah dikirim)',6),
 ('cancelled','Dibatalkan',7);

create table couriers(
  id int auto_increment primary key,
  name varchar(100) not null unique,
  active boolean not null default true
);
insert into couriers(name) values ('J&T'),('MGM Express');

-- ===== Fitur 01 dan 02: akun untuk admin dan member, kata sandi di-hash (bcrypt) =====
alter table users
  add column username varchar(100) null unique,
  add column password varchar(255) null,
  add column status varchar(20) not null default 'active',
  add column last_login_at datetime null,
  modify column email varchar(190) not null;
alter table users add constraint users_status_ck check (status in ('active','disabled'));

-- ===== Fitur 82: identitas eksternal (Google dan lainnya) dipisah dari users (normalisasi, bentuk normal ketiga) =====
create table user_identities(
  id int auto_increment primary key,
  user_id int not null,
  provider varchar(30) not null,
  provider_user_id varchar(190) not null,
  created_at datetime not null default current_timestamp,
  unique(provider,provider_user_id),
  unique(user_id,provider),
  constraint user_identities_user_fk foreign key(user_id) references users(id) on delete cascade
);
insert into user_identities(user_id,provider,provider_user_id)
  select id,'google',google_id from users where google_id is not null;
alter table users drop column google_id;

-- ===== Fitur 13 dan 14: peran, izin, dan penetapan peran =====
create table roles(
  id int auto_increment primary key,
  code varchar(50) not null unique,
  name varchar(100) not null,
  parent_role_id int null,
  constraint roles_parent_fk foreign key(parent_role_id) references roles(id) on delete set null
);
create table permissions(
  id int auto_increment primary key,
  code varchar(100) not null unique,
  description varchar(255) not null
);
create table role_permission(
  role_id int not null,
  permission_id int not null,
  primary key(role_id,permission_id),
  constraint role_permission_role_fk foreign key(role_id) references roles(id) on delete cascade,
  constraint role_permission_perm_fk foreign key(permission_id) references permissions(id) on delete cascade
);
create table user_roles(
  user_id int not null,
  role_id int not null,
  expires_at datetime null,
  primary key(user_id,role_id),
  constraint user_roles_user_fk foreign key(user_id) references users(id) on delete cascade,
  constraint user_roles_role_fk foreign key(role_id) references roles(id) on delete restrict
);

insert into roles(code,name) values
 ('super_admin','Super Admin'),('admin_gudang','Admin Gudang'),('admin_cs','Admin CS'),('member','Member');
insert into permissions(code,description) values
 ('pesanan.lihat','Melihat daftar dan detail pesanan'),
 ('pesanan.kelola','Mengatur ongkir, konfirmasi bayar, tolak bukti, resi, dan pembatalan'),
 ('chat.balas','Membalas chat pelanggan'),
 ('stok.ubah','Mengubah stok dan status preorder produk'),
 ('promo.kelola','Mengelola voucher'),
 ('hadiah.kelola','Mengelola hadiah dan memproses penukaran poin'),
 ('toko.atur','Mengatur jam operasional dan tombol istirahat');
insert into role_permission(role_id,permission_id)
  select r.id,p.id from roles r cross join permissions p where r.code='super_admin';
insert into role_permission(role_id,permission_id)
  select r.id,p.id from roles r join permissions p on p.code in ('pesanan.lihat','stok.ubah') where r.code='admin_gudang';
insert into role_permission(role_id,permission_id)
  select r.id,p.id from roles r join permissions p on p.code in ('pesanan.lihat','pesanan.kelola','chat.balas','hadiah.kelola') where r.code='admin_cs';

-- ===== Kolom relasi yang wajib terisi (sebelumnya boleh kosong sehingga memungkinkan baris yatim) =====
alter table orders modify column status varchar(30) not null default 'waiting_shipping';
alter table order_items modify column order_id int not null, modify column product_id int not null;
alter table order_messages modify column order_id int not null;
alter table point_ledger modify column user_id int not null;
alter table redemptions modify column user_id int not null, modify column reward_id int not null;
alter table redemption_messages modify column redemption_id int not null;
alter table voucher_claims modify column voucher_id int not null, modify column user_id int not null;
alter table voucher_uses modify column voucher_id int not null;

-- ===== Fitur 26: kunci asing dengan aturan hapus dan ubah beralasan =====
-- Riwayat transaksi tidak boleh hilang: produk, hadiah, dan voucher yang pernah dipakai tidak bisa dihapus (RESTRICT).
-- Data turunan murni ikut terhapus (CASCADE). Rujukan opsional dikosongkan (SET NULL), misalnya pesanan tamu.
alter table orders add constraint orders_user_fk    foreign key(user_id) references users(id) on delete set null;
alter table orders add constraint orders_status_fk  foreign key(status)  references order_statuses(code) on update cascade on delete restrict;
alter table orders add constraint orders_courier_fk foreign key(courier) references couriers(name)       on update cascade on delete restrict;
alter table order_items add constraint order_items_order_fk   foreign key(order_id)   references orders(id)   on delete cascade;
alter table order_items add constraint order_items_product_fk foreign key(product_id) references products(id) on delete restrict;
alter table order_messages add constraint order_messages_order_fk foreign key(order_id) references orders(id) on delete cascade;
alter table point_ledger add constraint point_ledger_user_fk  foreign key(user_id)  references users(id)  on delete cascade;
alter table point_ledger add constraint point_ledger_order_fk foreign key(order_id) references orders(id) on delete set null;
alter table redemptions add constraint redemptions_user_fk   foreign key(user_id)   references users(id)   on delete cascade;
alter table redemptions add constraint redemptions_reward_fk foreign key(reward_id) references rewards(id) on delete restrict;
alter table redemption_messages add constraint redemption_messages_fk foreign key(redemption_id) references redemptions(id) on delete cascade;
alter table vouchers add constraint vouchers_product_fk foreign key(product_id) references products(id) on delete set null;
alter table voucher_claims add constraint voucher_claims_voucher_fk foreign key(voucher_id) references vouchers(id) on delete cascade;
alter table voucher_claims add constraint voucher_claims_user_fk    foreign key(user_id)    references users(id)    on delete cascade;
alter table voucher_uses add constraint voucher_uses_voucher_fk foreign key(voucher_id) references vouchers(id) on delete restrict;
alter table voucher_uses add constraint voucher_uses_user_fk    foreign key(user_id)    references users(id)    on delete set null;
alter table voucher_uses add constraint voucher_uses_order_fk   foreign key(order_id)   references orders(id)   on delete set null;

-- ===== Fitur 27: constraint penjaga data (CHECK berlaku mulai MySQL 8.0.16 dan MariaDB 10.2.1) =====
alter table products add constraint products_price_ck check (price >= 0);
alter table products add constraint products_stock_ck check (stock >= 0);
alter table orders add constraint orders_amount_ck check (subtotal >= 0 and coalesce(shipping,0) >= 0 and discount >= 0 and coalesce(total,0) >= 0);
alter table order_items add constraint order_items_qty_ck check (qty > 0 and price >= 0);
alter table point_ledger add constraint point_ledger_ck check (points > 0 and remaining >= 0 and remaining <= points);
alter table rewards add constraint rewards_ck check (points > 0 and stock >= 0);
alter table vouchers add constraint vouchers_type_ck  check (type in ('percent','fixed'));
alter table vouchers add constraint vouchers_value_ck check (value > 0 and (type <> 'percent' or value <= 100));
alter table vouchers add constraint vouchers_limit_ck check (used >= 0 and (quota is null or quota > 0) and (per_user is null or per_user >= 1) and min_purchase >= 0);
alter table vouchers add constraint vouchers_period_ck check (starts_at is null or ends_at is null or ends_at > starts_at);
