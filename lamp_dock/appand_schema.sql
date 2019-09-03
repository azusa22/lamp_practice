create table historys (
  order_number int auto_increment,
  user_id int,
  cart_id int,
  date datetime,
  primary key(order_number)
);

create table details (
  order_number int,
  item_id int,
  name varchar(100),
  price int,
  amount int
);
