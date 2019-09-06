<?php
require_once '../conf/const.php';
require_once '../model/functions.php';
require_once '../model/user.php';
require_once '../model/item.php';

session_start();

$now_page = 1;
$page = 0;
$page_num = 1;

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$items = get_open_items($db);
$all_amount = count(all_item_amount($db));

if($_SERVER['REQUEST_METHOD'] === 'GET'){
	if(isset($_GET['sort']) === TRUE){
		if($_GET['sort'] === 'sortItem'){
			if(page_get_check() !== 0){
				$page = page_get_check();
			}
			$items = get_sort_item($db, $page);
			$now_page = intval($page);
		}
	}
}else if(isset($_GET['page']) === TRUE){
	$page = page_get_check();
	$items = page_item_read($db, $page);
	$now_page = intval($page);
}

var_dump($_GET['sort']);
include_once '../view/index_view.php';
