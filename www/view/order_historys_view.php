<!DOCTYPE html>
<html lang="ja">
<head>  
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
</head>
<body>
  <?php include_once VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>
  <div class="container">
    <?php include_once VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($historys) > 0){ ?>
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>明細</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($historys as $history){ ?>
            <tr>
              <td><?php print h($history['order_number']); ?></td>
              <td><?php print h($history['date']); ?></td>
              <td><?php print h(number_format($history['total_price'])); ?>円</td>
              <td>
                <form method="post" action="<?php print h(DETAILS_URL); ?>">
                  <input type="hidden" name="order_number" value="<?php print h($history['order_number']); ?>">
                  <input type="submit" value="明細">
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php }else{ ?>
      <p>購入履歴がありません</p>
    <?php } ?>
  </div>
</body>
</html>
            
        