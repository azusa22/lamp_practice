<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include_once VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print h(STYLESHEET_PATH . 'order_details.css'); ?>">
</head>
<body>
  <?php include_once VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">
    <?php include_once VIEW_PATH . 'templates/messages.php'; ?>

    <table class="table table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>注文番号</th>
          <th>購入日時</th>
          <th>合計金額</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php print h($history['order_number']); ?></td>
          <td><?php print h($history['date']); ?></td>
          <td><?php print h(number_format($total_price)); ?></td>
        </tr>
      </tbody>
    </table>
    
    <table class="table table-bordered">
      <thead class="thead-light">
       <tr>
          <th>商品画像</td>
          <th>商品名</th>
          <th>価格</th>
          <th>購入数</th>
          <th>小計</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($details as $detail){ ?>
          <tr>
            <td><img src="<?php print h(IMAGE_PATH . $detail['image']); ?>" class="item_image"></td>
            <td><?php print h($detail['name']); ?></td>
            <td><?php print h($detail['price']); ?></td>
            <td><?php print h($detail['amount']); ?></td>
            <td><?php print h(number_format($detail['price'] * $detail['amount'])); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>