<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	if (file_exists("./install.php"))
	{
		header ("Location: install.php");
		exit();
	}

	session_start();
	require_once("inc/config.inc.php");


	// save referral's link //
	if (isset($_GET['ref']) && is_numeric($_GET['ref']))
	{
		$ref_id = (int)$_GET['ref'];
		setReferral($ref_id);

		header("Location: index.php");
		exit();
	}

	
	if (SHOW_RANDOM == 1)
		$query = "SELECT *, TIMESTAMPDIFF(SECOND, NOW(), end_date) as expire_sec, DATE_FORMAT(end_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_end_date FROM abbijan_items WHERE start_date<=NOW() AND end_date>NOW() AND status='active' ORDER BY RAND() LIMIT 1";
	else
		$query = "SELECT *, TIMESTAMPDIFF(SECOND, NOW(), end_date) as expire_sec, DATE_FORMAT(end_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_end_date FROM abbijan_items WHERE start_date<=NOW() AND end_date>NOW() AND main_deal='1' AND status='active' LIMIT 1";


	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = SITE_HOME_TITLE;

	require_once("inc/header.inc.php");

?>

<?php
		
		if ($total > 0)
		{
			$row = mysql_fetch_array($result);
			
			$item_id = $row['item_id'];

			// if deal is expired
			if ($row['expire_sec'] <= 0)
			{
				smart_mysql_query("UPDATE abbijan_items SET status='expired' WHERE item_id='$item_id'");

				// send notification
				if (DEAL_EXPIRED_ALERT == 1)
				{
					$message = "Deal expired";

					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
					@mail(SITE_MAIL, "Deal is expired", $message, $headers);
				}
			}

			// if deal is sold out
			if ($row['deal_type'] != "affiliate" && GetDealQuantity($item_id) == 0)
			{
				smart_mysql_query("UPDATE abbijan_items SET status='sold' WHERE item_id='$item_id'");
				
				// send notification
				if (SOLD_OUT_ALERT == 1)
				{
					$message = "Deal sold out";

					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
					@mail(SITE_MAIL, "Deal sold out", $message, $headers);
				}
			}

?>

	<a class="deal_title" href="deal_details.php?id=<?php echo $row['item_id']; ?>"><h1 class="deal_title"><?php echo $row['title']; ?></h1></a>

<div id="content_left">

	<?php if ($row['brief_description'] != "") { ?>
		<p class="brief_description"><?php echo stripslashes($row['brief_description']); ?></p>
	<?php } ?>

	<?php if ($row['conditions'] != "") { ?>
		<p><b>Condition</b>: <?php echo $row['conditions']; ?></p>
	<?php } ?>

	<?php if ($row['retail_price'] != "0.00") { ?>
	<div class="deal-discount">
		<span class="price-title">Value<br/><span class="rprice-num"><?php echo DisplayPrice($row['retail_price']); ?></span></span>
		<span class="price-title">Discount<br/><span class="price-num"><?php echo CalculateSavingsPercentage($row['retail_price'],$row['price']); ?></span></span>
		<span class="price-title">Savings<br/><span class="price-num"><?php echo DisplayPrice($row['discount']); ?></span></span>
	</div>
	<?php } ?>

	<span class="price"><?php echo DisplayPrice($row['price']);?></span><br/>

	<?php if ($row['status'] == "expired") { ?>
		<div class="expired">Sorry, this deal has ended!</div>
	<?php }elseif ($row['status'] == "sold") { ?>
		<div class="sold-out">Sorry, this deal is sold out!</div>
	<?php }else{ ?>
		<?php if ($row['quantity'] > 0 && SHOW_QUANTITY == 1) { ?>
			<?php if (SHOW_STOCK_BAR == 1) { ?>
			<div class="stock_bar">
				<b>Stock Availability</b>: <div class="progress_bar"><div class="progress" style="width: <?php echo GetStockBarWidth($row['item_id']); ?>;"></div><div class="percent"><?php echo GetStockBarWidth($row['item_id']); ?> left</div></div>
			</div>
			<?php }else{ ?>
				Quantity: <span class="quantity"><?php echo GetDealQuantity($row['item_id']); ?></span><br/>
				<?php if ($row['quantity'] <= 5) { ?><span class="low_in_stock">Low in Stock</span><br/><?php } ?>
			<?php } ?>	
		<?php } ?>
		<?php if ($row['deal_type'] == "affiliate") { ?>
			<br/><a class="buy_large" href="/visit.php?id=<?php echo $row['item_id']; ?>" target="_blank">Buy Now!</a>
		<?php } else { ?>
			<br/><a class="buy_large" href="/cart.php?id=<?php echo $row['item_id']; ?>&action=add">Buy Now!</a>
		<?php } ?>
		<?php if (GetLowerShippingCost()) { ?><p class="shipping">Shipping starting at <?php echo DisplayPrice(GetLowerShippingCost()); ?></p><?php } ?>
	<?php } ?>

	<?php if ($row['status'] == "active") { ?>
		<?php
			/// Countdown ///
			echo "
				<script type=\"text/javascript\">
					$(function () {
					$('#count_".$row['item_id']."').countdown({until: $.countdown.UTCDate(".SITE_TIMEZONE.", ".$row['deal_end_date']."), compact: ".COUNTDOWN_COMPACT.",  format: '".COUNTDOWN_FORMAT."',  serverSync: ahead5Mins, 
					layout: '".COUNTDOWN_LAYOUT."'}); 
				});";

			echo "</script>";
		?>
		<div id="deal-timer">
			<span>This Deal Expires In:</span>
			<div id="count_<?php echo $row['item_id']; ?>" class="timeformat_large"></div>
		</div>
	<?php } ?>

	</div>
	<div id="content_right">

			<div id="deal-image"><?php echo GetDealImages($row['item_id']); ?></div>

			<div id="deal-share">
				<span class="deal-sharer">   
					<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
					<fb:like href="<?php echo SITE_URL."deal_details.php?id=".$row['item_id']; ?>" layout="button_count" show_faces="false" ></fb:like>
				</span>
				<span class="deal-sharer">
					<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo SITE_URL."deal_details.php?id=".$row['item_id']; ?>" data-text="<?php echo $row['title']." - ".DisplayPrice($row['price']); ?>" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
				</span>
				<span class="deal-sharer">
					<g:plusone size="medium" annotation="none"></g:plusone>
					<script type="text/javascript">
					  (function() {
						var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
						po.src = 'https://apis.google.com/js/plusone.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
				</span>
				<span class="deal-sharer">
					<a href="mailto:?body=<?php echo htmlentities(SITE_URL."deal_details.php?id=".$row['item_id']."&subject=".$row['title']); ?>"><img src="/images/icon_mail.png" /></a>
					<a href="/myfavorites.php?id=<?php echo $row['item_id']; ?>&act=add" title="add to favorites"><img src="/images/icon_favorite.png" alt="add to favorites" /></a>
					<a href="/deal_report.php?id=<?php echo $row['item_id']; ?>" title="report"><img src="/images/icon_report.png" alt="report" /></a>
				</span>
			</div>

			<div id="tabs_container">
				<ul id="tabs">
					<li class="active"><a href="#description"><span>Description</span></a></li>
					<?php if ($row['specs'] != "") { ?><li><a href="#specs"><span>Specs</span></a></li><?php } ?>
					<?php if ($row['youtube_video'] != ""){ ?><li><a href="#video"><span>Video</span></a></li><?php } ?>
					<?php if (SHOW_SALES_STATS == 1) { ?><li><a href="#stats"><span>Sales Stats</span></a></li><?php } ?>
					<?php if ($row['allow_comments'] == 1) { ?><li><a href="#comments"><span>Comments (<?php echo GetDealCommentsTotal($row['item_id']); ?>)</span></a></li><?php } ?>
				</ul>
			</div>

			<div id="description" class="tab_content">
				<?php echo stripslashes($row['description']); ?>
			</div>

			<?php if ($row['features'] != "") { ?>
			<div id="features" class="tab_content">
				<?php echo stripslashes($row['features']); ?>
			</div>
			<?php } ?>

			<?php if ($row['specs'] != "") { ?>
			<div id="specs" class="tab_content">
				<?php echo stripslashes($row['specs']); ?>
			</div>
			<?php } ?>

			<?php if ($row['youtube_video'] != "") { ?>
			<div id="video" class="tab_content">
				<p align="center"><iframe width="420" height="315" frameborder="0" allowfullscreen="true" src="<?php echo str_replace("watch?v=", "embed/", $row['youtube_video'])."?rel=0&autoplay=0&loop=0&wmode=opaque&showinfo=0&theme=light"; ?>" marginwidth="0" marginheight="0"></iframe></p>
			</div>
			<?php } ?>
			
			<?php if (SHOW_SALES_STATS == 1) { ?>
			<div id="stats" class="tab_content">
				<div class="sales_stats">
					<?php if (GetDealSalesTotal($row['item_id']) > 0) { ?>
						Sales: <?php echo GetDealSalesTotal($row['item_id']); ?><br/>
						First customer: <?php echo GetDealFirstCustomer($row['item_id']); ?><br/>
						Last customer: <?php echo GetDealLastCustomer($row['item_id']); ?><br/>
						First Sale: <?php echo GetDealFirstSale($row['item_id']); ?><br/>
						First Sale Speed: <?php echo GetDealFirstSaleSpeed($row['item_id']); ?><br/>
						Last Sale: <?php echo GetDealLastSale($row['item_id']); ?><br/>
					<?php }else{ ?>
						<p>No statistics at this time.</p>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($row['allow_comments'] == 1) { ?>
			<a name="comments"></a>
			<div id="comments" class="tab_content">
				<p align="right"><a class="comment" href="/forum_details.php?id=<?php echo $row['forum_id']; ?>">Discuss this deal</a></p>
			<?php
				$comments_query = "SELECT r.*, DATE_FORMAT(r.added, '%e/%m/%Y') AS comment_date, u.user_id, u.username, u.fname, u.lname, u.avatar FROM abbijan_forum_comments r LEFT JOIN abbijan_users u ON r.user_id=u.user_id WHERE r.forum_id='".$row['forum_id']."' AND r.status='active' ORDER BY r.added DESC LIMIT 5";
				$comments_result = smart_mysql_query($comments_query);
				$comments_total = mysql_num_rows(smart_mysql_query("SELECT * FROM abbijan_forum_comments WHERE forum_id='".$row['forum_id']."' AND status='active'"));
				
				$cc = 0;
				if ($comments_total > 0) {
			?>
				<?php while ($comments_row = mysql_fetch_array($comments_result)) { $cc++; ?>
				<div id="comment" style="background: <?php if (($cc%2) == 0) echo "#FFFFFF"; else echo "#F9F9F9"; ?>">
					<img src="<?php echo AVATARS_URL.$comments_row['avatar']; ?>" height="<?php echo AVATAR_HEIGHT; ?>" width="<?php echo AVATAR_WIDTH; ?>" alt="" class="thumb" align="left" />
					<span class="comment-author"><a href="/user_profile.php?id=<?php echo $comments_row['user_id']; ?>"><?php echo $comments_row['fname']; ?></a></span>
					<span class="comment-date"><?php echo $comments_row['comment_date']; ?></span><br/>
					<div class="comment-text"><?php echo $comments_row['comment']; ?></div>
					<div style="clear: both"></div>
				</div>
				<?php } ?>
			<?php }else{ ?>
					<p align="center">No comments yet. Be the first!</p>
			<?php } ?>

			</div>
			<?php } ?>
	</div>

	<?php }else{ ?>
		<div class="no_deals">
			<p align="center"><img src="/images/clock.png" /></p>
			<h2>Sorry, there are no available deals at this time. Please check back soon.</h2>
		</div>
	<?php } ?>


<?php require_once("inc/footer.inc.php"); ?>