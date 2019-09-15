<?php 
require_once 'functions.php';
require_once 'db.php';

function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  } catch (PDOException $e) {
    set_error('データ取得に失敗しました。');
  }
  return false;
}

function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindvalue(2, $item_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
  } catch (PDOException $e) {
    set_error('データ取得に失敗しました。');
  }
  return false;
}

function add_cart($db, $item_id, $user_id) {
  $cart = get_user_cart($db, $item_id, $user_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $item_id, $user_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $item_id, PDO::PARAM_INT);
    $stmt->bindvalue(2, $user_id, PDO::PARAM_INT);
    $stmt->bindvalue(3, $amount, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    set_error('更新に失敗しました。');
  }
  return false;
}

function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $amount, PDO::PARAM_INT);
    $stmt->bindvalue(2, $cart_id, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    set_error('更新に失敗しました。');
  }
  return false;
}

function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $cart_id, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    set_error('更新に失敗しました。');
  }
  return false;
}

function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  $db->beginTransaction();
  try{
    insert_into_historys(
      $db,
      $carts[0]['user_id']
    );
    foreach($carts as $cart){
      update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      );		
      insert_into_details(
        $db,
        $cart['item_id'],
        $cart['amount']
      );
    }
    delete_user_carts($db, $carts[0]['user_id']);
    $db->commit();
  }catch(PDOException $e){
    $db->rollback();
    throw $e;
  }
}

function insert_into_historys($db, $user_id){
  global $last_id;
  $sql = '
    INSERT INTO
      order_historys(
        user_id,
        date)
    VALUE (
      ?,
      now())
    ';
  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $user_id, PDO::PARAM_INT);
    $valid = $stmt->execute();
    $last_id = $db->lastinsertid();
    return $valid;
  }catch(PDOException $e){
    throw $e;
  }
}

function insert_into_details($db, $item_id, $amount){
  global $last_id;
  $sql = '
    INSERT INTO
      order_details(
        order_number,
        item_id,
        amount)
    VALUE (
      ?,
      ?,
      ?)
  ';
  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $last_id, PDO::PARAM_INT);
    $stmt->bindvalue(2, $item_id, PDO::PARAM_INT);
    $stmt->bindvalue(3, $amount, PDO::PARAM_INT);
    return $stmt->execute();
  }catch(PDOException $e){
    throw $e;
  }
}

function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $user_id, PDO::PARAM_INT);
    return $stmt->execute();
  }catch(PDOException $e){
    throw $e;
  }
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

