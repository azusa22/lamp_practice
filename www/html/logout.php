<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();

$token = get_post('token');

if(is_valid_csrf_token($token) === true) {
  $_SESSION = array();
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000,
    $params["path"], 
    $params["domain"],
    $params["secure"], 
    $params["httponly"]
  );
  session_destroy();

  redirect_to(LOGIN_URL);
} else {
  set_error('不正なリクエストです');
}

redirect_to(HOME_URL);

