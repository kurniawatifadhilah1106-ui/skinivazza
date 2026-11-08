-- 003: hadiah poin, voucher, dan pengaturan toko
create table rewards(
  id int auto_increment primary key,
  name varchar(255),
  image varchar(255),
  points int,
  description text,
  kind varchar(30),
  region varchar(100),
  stock int default 0,
  terms text,
  active boolean default true,
  created_at datetime null,
  updated_at datetime null
);
create table redemptions(
  id int auto_increment primary key,
  user_id int,
  reward_id int,
  status varchar(20) default 'pending',
  created_at datetime null,
  updated_at datetime null
);
create table redemption_messages(
  id int auto_increment primary key,
  redemption_id int,
  sender varchar(20),
  body text,
  created_at datetime null,
  updated_at datetime null
);
create table vouchers(
  id int auto_increment primary key,
  title varchar(255),
  description text,
  code varchar(50) not null unique,
  type varchar(20),
  value int,
  max_discount int,
  min_purchase int default 0,
  members_only boolean default false,
  product_id int,
  starts_at datetime null,
  ends_at datetime null,
  quota int,
  per_user int default 1,
  used int default 0,
  active boolean default true,
  created_at datetime null,
  updated_at datetime null
);
create table voucher_claims(
  id int auto_increment primary key,
  voucher_id int,
  user_id int,
  created_at datetime null,
  unique(voucher_id,user_id)
);
create table voucher_uses(
  id int auto_increment primary key,
  voucher_id int,
  user_id int,
  order_id int,
  created_at datetime null
);
alter table orders add column voucher_code varchar(50) null;
create table settings(k varchar(50) primary key, v varchar(255));
insert into settings values('open','08.00 - 22.00 WIB'),('rest','0');
