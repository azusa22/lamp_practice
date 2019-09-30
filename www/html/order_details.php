<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'user.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$order_number = get_post('order_number');

$history = get_order_history($db, $order_number);
$details = get_order_details($db, $order_number);
$details_price = get_details_total_price($db, $order_number);
$total_price = 0;
for($i = 0; $i < count($details_price); $i++){
  $total_price += $details_price[$i]['price'] * $details_price[$i]['amount']; 
}

include_once '../view/order_details_view.php';