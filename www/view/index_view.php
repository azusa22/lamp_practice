<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'index.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
		<?php if((intval($page) + 8) > $all_amount){ ?>
			<p>総数<?php print h($all_amount); ?>件中 <?php print h(intval($page) + 1); ?>件 - <?php print h($all_amount); ?>件 表示中</p>
		<?php }else{ ?>
			<p>総数<?php print h($all_amount); ?>件中 <?php print h(intval($page) + 1); ?>件 - <?php print h(intval($page) + 8); ?>件 表示中</p>
		<?php } ?>
    
	<form method="get">
		<select name="sort_item">
			<option value="新着順" salected>新着順</option>
			<option value="価格の低い順">価格の低い順</option>
			<option value="価格の高い順">価格の高い順</option>
		</select>
	<input type="hidden" name="sort" value="sortItem">
	<input type="submit" value="並び替え">
	</form>

    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print($item['name']); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . $item['image']); ?>">
              <figcaption>
                <?php print(number_format($item['price'])); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($item['item_id']); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
			<!--ページボタン-->
			<?php if($sort === 'sortItem'){ ?>
				<?php if($now_page !== 1){ ?>
					<form method="get">
						<input type="hidden" name="sort" value="sortItem">
						<input type="hidden" name="sort_item" value="<?php print h($sort_item); ?>">
						<input type="hidden" name="page" value="0">
						<input class="pagebtn" type="submit" value="1">
					</form>
				<?php }else{ ?>
					<form method="get">
                                                <input type="hidden" name="sort" value="sortItem">
                                                <input type="hidden" name="sort_item" value="<?php print h($sort_item); ?>">
                                                <input type="hidden" name="page" value="0">
                                                <input class="now_pagebtn" type="submit" value="1">
                                        </form>
				<?php }
                                if($now_page !== 2){ ?>
                                        <form method="get">
                                                <input type="hidden" name="sort value="sortItem">
                                                <input type="hidden" name="sort_item" value="<?php print h($sort_item); ?>">
                                                <input type="hidden" name="page" value="8">
                                                <input class="pagebtn" type="submit" value="2">
                                        </form>
                                <?php }else{ ?>
                                        <form method="get">
                                                <input type="hidden" name="sort" value="sortItem">
                                                <input type="hidden" name="sort_item" value="<?php print h($sort_item); ?>">
                                                <input type="hidden" name="page" value="8">
                                                <input class="now_pagebtn" type="submit" value="2">
					</form>
				<?php }
				if($now_page !== 3){ ?>
                                        <form method="get">
                                                <input type="hidden" name="sort" value="sortItem">
                                                <input type="hidden" name="sort_item" value="<?php print h($sort_item); ?>">
                                                <input type="hidden" name="page" value="16">
                                                <input class="pagebtn" type="submit" value="3">
                                        </form>
                                <?php }else{ ?>
                                        <form method="get">
                                                <input type="hidden" name="sort" value="sortItem">
                                                <input type="hidden" name="sort_item" value="<?php print h($sort_item); ?>">
                                                <input type="hidden" name="page" value="16">
                                                <input class="now_pagebtn" type="submit" value="3">
                                        </form>
                                <?php }
			}else{ ?>
				<?php if($now_page !== 1){ ?>
					<form method="get">
						<input type="hidden" name="page" value="0">
						<input class="pagebtn" type="submit" value="1">
					</form>
				<?php }else{ ?>
					<form method="get">
						<input type="hidden" name="page" value="0">
						<input class="now_pagebtn" type="submit" value="1">
					</form>
				<?php }
				if($now_page !== 2){ ?>
                                        <form method="get">
                                                <input type="hidden" name="page" value="8">
                                                <input class="pagebtn" type="submit" value="2">
                                        </form>
                                <?php }else{ ?>
                                        <form method="get">
                                                <input type="hidden" name="page" value="8">
                                                <input class="now_pagebtn" type="submit" value="2">
                                        </form>
                                <?php }
				if($now_page !== 3){ ?>
                                        <form method="get">
                                                <input type="hidden" name="page" value="16">
                                                <input class="pagebtn" type="submit" value="3">
                                        </form>
                                <?php }else{ ?>
                                        <form method="get">
                                                <input type="hidden" name="page" value="16">
                                                <input class="now_pagebtn" type="submit" value="3">
                                        </form>
                                <?php }
			} ?>
      </div>
    </div>
  </div>
  
</body>
</html>
