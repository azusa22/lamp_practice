<?php
require_once 'functions.php';
require_once 'db.php';

// DB利用

function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = ?
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $item_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
  } catch (PDOException $e) {
    set_error('データ取得に失敗しました。');
  }
  return false;
}

function get_items($db, $is_open = false, $is_limit = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }
	if($is_limit === true){
		$sql .= '
			LIMIT 0, 8
		';
	}

  return fetch_all_query($db, $sql);
}

function get_all_items($db){
  return get_items($db);
}

function get_open_items($db){
  return get_items($db, true, false);
}

function get_limit_items($db){
	return get_items($db, true, true);
}

function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}

function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(?, ?, ?, ?, ?);
  ";

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $name, PDO::PARAM_STR);
    $stmt->bindvalue(2, $price, PDO::PARAM_INT);
    $stmt->bindvalue(3, $stock, PDO::PARAM_INT);
    $stmt->bindvalue(4, $filename, PDO::PARAM_STR);
    $stmt->bindvalue(5, $status_value, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    set_error('更新に失敗しました。');
  }
  return false;
}

function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $status, PDO::PARAM_INT);
    $stmt->bindvalue(2, $item_id, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    set_error('更新に失敗しました。');
  }
  return false;
}

function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $stock, PDO::PARAM_INT);
    $stmt->bindvalue(2, $item_id, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    set_error('更新に失敗しました。');
  }
  return false;
}

function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $item_id, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    set_error('更新に失敗しました。');
  }
  return false;
}

function limit_page_calc($page){
	for($i = 0; $i < $page; $i++){
		$limit_page = $i * 8;
	};
	return $limit_page;
}

function get_sort_item($db, $limit_page){
	global $sort_item;
	if(isset($_GET['sort_item']) === TRUE){
		$sort_item = trim($_GET['sort_item']);
		if($sort_item === '新着順'){
			return new_data_read($db, $limit_page);
		}else if($sort_item === '価格の低い順'){
			return lowprice_data_read($db, $limit_page);
		}else{
			return highprice_data_read($db, $limit_page);
		}
	}
}

function new_data_read($db, $limit_page){
	$sql = '
		SELECT
			item_id,
			name,
			stock,
			price,
			image,
			status
		FROM
			items
		WHERE
			status = 1
		ORDER BY
			item_id desc
		LIMIT
			?, 8
	';

	try{
		$stmt = $db->prepare($sql);
		$stmt->bindvalue(1, intval($limit_page), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}catch(PDOException $e){
		set_error('データ取得に失敗しました');
	}
	return false;
}

function lowprice_data_read($db, $limit_page){
	$sql = '
    SELECT
      item_id,
      name,
      stock,
      price,
      image,
      status
    FROM    
      items
    WHERE
      status = 1
    ORDER BY
      price asc
    LIMIT
      ?, 8
   ';              
        
        try{
		$stmt = $db->prepare($sql);
		$stmt->bindvalue(1, intval($limit_page), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}catch(PDOException $e){
		set_error('データ取得に失敗しました');
	}
	return false; 
}

function highprice_data_read($db, $limit_page){
	$sql = '
    SELECT
      item_id,
      name,
      stock,
      price,
      image,
      status
    FROM    
      items
		WHERE
			status = 1
    ORDER BY
      price desc
		LIMIT
			?, 8
        ';              
        
        try{
		$stmt = $db->prepare($sql);
		$stmt->bindvalue(1, intval($limit_page), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}catch(PDOException $e){
		set_error('データ取得に失敗しました');
	}
	return false;
}

function page_get_check() {
	if(isset($_GET['page']) === TRUE){
		$page = trim($_GET['page']);
		return $page;
	}else{
		return 0;
	}
}

function page_item_read($db, $limit_page){
	$sql = '
    SELECT
      item_id,
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      status = 1
    ORDER BY
      item_id asc
    LIMIT
      ?, 8
  ';

	try{
		$stmt = $db->prepare($sql);
		$stmt->bindvalue(1, intval($limit_page), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}catch(PDOException $e){
		set_error('データ取得に失敗しました');
	}
	return false;
}

function get_order_historys($db, $user_id){
  $sql = '
    SELECT
      order_number,
      date,
      total_price
    FROM
      order_historys
    WHERE
      user_id = ?
    ';

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました');
  }
}

function get_order_history($db, $order_number){
  $sql = '
    SELECT
      order_number,
      date,
      total_price
    FROM
      order_historys
    WHERE
      order_number = ?
    ';

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $order_number, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました');
  }
}

function get_order_details($db, $order_number){
  $sql = '
    SELECT
      image,
      name,
      price,
      amount
    FROM
      order_details
    LEFT JOIN
      items
    ON
      order_details.item_id = items.item_id
    WHERE
      order_number = ?
  ';

  try{
    $stmt = $db->prepare($sql);
    $stmt->bindvalue(1, $order_number, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました');
  }
}

function all_item_amount($db){
		$sql = '
                SELECT
                        item_id,
                        name,
                        stock,
                        price,
                        image,
                        status
                FROM
                        items
                WHERE
                        status = 1
                ORDER BY
                        item_id desc
        ';

        try{
                $stmt = $db->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll();
        }catch(PDOException $e){
                set_error('データ取得に失敗しました');
        }
        return false;
}
				

// 非DB

function is_open($item){
  return $item['status'] === 1;
}

function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}
