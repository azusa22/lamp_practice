<?php
require_once '../conf/const.php';
require_once '../model/functions.php';
require_once '../model/user.php';
require_once '../model/item.php';

session_start();

$sort_item = '';
$now_page = 1;
$page = 1;
$limit_page = 0;
$page_num = 1;

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$items = get_limit_items($db);
$page_num = ceil(count(get_open_items($db)) / 8);
$all_amount = count(all_item_amount($db));

if($_SERVER['REQUEST_METHOD'] === 'GET'){
	if(isset($_GET['sort']) === TRUE){
		$sort = $_GET['sort'];
		if($sort === 'sortItem'){
			if(page_get_check() !== 1){
				$page = page_get_check();
			}
			$limit_page = limit_page_calc($page);
			$items = get_sort_item($db, $limit_page);
			$page_num = ceil(count(get_open_items($db)) / 8);
			$now_page = intval($page);
		}
	}else if(isset($_GET['page']) === TRUE){
		$page = page_get_check();
		$limit_page = limit_page_calc($page);
		$items = page_item_read($db, $limit_page);
		$page_num = ceil(count(get_open_items($db)) / 8);
		$now_page = intval($page);
	}
}

include_once '../view/index_view.php';
