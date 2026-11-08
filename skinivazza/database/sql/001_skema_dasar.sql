-- 001: skema dasar toko (MySQL 8 / MariaDB 10.5+, InnoDB, utf8mb4)
create table products(
  id int auto_increment primary key,
  slug varchar(150) not null unique,
  name varchar(255) not null,
  price int not null,
  description text,
  ingredients text,
  `usage` text,
  bpom varchar(100),
  image varchar(255),
  stock int default 10,
  preorder boolean default false,
  created_at datetime default current_timestamp,
  updated_at datetime null
);
create table orders(
  id int auto_increment primary key,
  code varchar(20) not null unique,
  token varchar(64) not null unique,
  name varchar(150),
  wa varchar(30),
  address text,
  courier varchar(100),
  note text,
  subtotal int,
  shipping int,
  discount int default 0,
  total int,
  status varchar(30) default 'waiting_shipping',
  proof varchar(255),
  reject_reason text,
  resi varchar(100),
  deadline_at datetime null,
  created_at datetime default current_timestamp,
  updated_at datetime null
);
create table order_items(
  id int auto_increment primary key,
  order_id int,
  product_id int,
  name varchar(255),
  price int,
  qty int,
  created_at datetime null,
  updated_at datetime null
);
create table order_messages(
  id int auto_increment primary key,
  order_id int,
  sender varchar(20),
  body text,
  `file` varchar(255),
  `internal` boolean default false,
  created_at datetime default current_timestamp,
  updated_at datetime null
);
insert into products(slug,name,price,description,ingredients,`usage`,bpom,image,stock) values
('salmon-soap','Salmon-DNA + Niacinamide Brightening Soap',199000,'Sabun pencerah 2in1 wajah & tubuh.','Salmon DNA, Niacinamide, Vitamin C','Busakan, bilas.','NA-ISI-DULU','salmon-soap.jpg',20),
('pro-whitening','Pro-Whitening',249000,'Serum 10% Niacinamide, 3% Alpha Arbutin, 3% Tranexamic Acid, Bakuchiol.','Niacinamide, Alpha Arbutin, Tranexamic Acid, Chromabright Bakuchiol','Oleskan tipis 2x sehari.','NA-ISI-DULU','pro-whitening.jpg',20),
('black-caviar','Black Caviar Whitening Intense Body Serum',299000,'Body serum Niacinamide 5%, 10 mL.','Niacinamide 5%, Black Caviar','Oleskan merata ke tubuh.','NA-ISI-DULU','black-caviar.jpg',20),
('dual-tone-up','Dual Tone-Up Face & Body Toner',279000,'Toner wajah & tubuh: brightening, melembapkan, Vitamin E, antioksidan.','Vitamin E, antioksidan','Semprot/usap ke wajah & tubuh.','NA-ISI-DULU','dual-tone-up.jpg',20);
