-- 002: akun member, poin, dan langganan push
create table users(
  id int auto_increment primary key,
  name varchar(150),
  email varchar(190) unique,
  google_id varchar(100),
  created_at datetime null,
  updated_at datetime null
);
alter table orders add column user_id int null, add column points_awarded boolean default false;
create table point_ledger(
  id int auto_increment primary key,
  user_id int,
  order_id int,
  points int,
  remaining int,
  expires_at datetime null,
  created_at datetime null
);
create table push_subscriptions(
  id int auto_increment primary key,
  scope varchar(100),
  endpoint varchar(700) unique,
  p256dh varchar(255),
  auth varchar(255)
);
