<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}

var_dump($name);
var_dump($db);
var_dump($user);
include_once '../view/login_view.php';
