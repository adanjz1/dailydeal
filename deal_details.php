<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$item_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}

	$query = "SELECT *, TIMESTAMPDIFF(SECOND, NOW(), end_date) as expire_sec, DATE_FORMAT(end_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_end_date, DATE_FORMAT(start_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_start_date FROM abbijan_items WHERE item_id='$item_id' AND status!='inactive' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		$row = mysql_fetch_array($result);

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
		if ($row['deal_type'] != "affiliate" && GetDealQuantity($row['item_id']) == 0)
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

		smart_mysql_query("UPDATE abbijan_items SET views=views+1 WHERE item_id='$item_id'");
		
		$ptitle = $row['title']." ".DisplayPrice($row['price']);
	}
	else
	{
		$ptitle = "Deal not found";
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE			= $ptitle;
	$PAGE_DESCRIPTION	= $row['meta_description'];
	$PAGE_KEYWORDS		= $row['meta_keywords'];

	require_once ("inc/header.inc.php");

?>

	<?php

		if ($total > 0) {

	?>
		<h1 class="deal_title"><?php echo $row['title']; ?></h1>
		
		<div class="breadcrumbs"><a href="/" class="home_link">Home</a> &#155; <a href="/deals.php">Deals</a> &#155; <?php echo $row['title']; ?></div>
		
		<?php echo PrevNextNav($row['item_id']); ?>


	<div id="content_left">

		<?php if ($row['brief_description'] != "") { ?>
			<p class="brief_description"><?php echo stripslashes($row['brief_description']); ?></p>
		<?php } ?>

		<?php if ($row['conditions'] != "") { ?>
			<p><b>Condition</b>: <?php echo $row['conditions']; ?></p>
		<?php } ?>

		<?php if ($row['retail_price'] != "0.00") { ?>
		<div class="deal-discount">
			<span class="price-title">Retail Price<br/><span class="rprice-num"><?php echo DisplayPrice($row['retail_price']); ?></span></span>
			<span class="price-title">Discount<br/><span class="price-num"><?php echo CalculateSavingsPercentage($row['retail_price'],$row['price']); ?></span></span>
			<span class="price-title">You Save<br/><span class="price-num"><?php echo DisplayPrice($row['discount']); ?></span></span>
		</div>
		<?php } ?>

		<span class="price"><?php echo DisplayPrice($row['price']); ?></span><br/>

		<?php if ($row['status'] == "expired") { ?>
			<div class="expired">Sorry, this deal has ended!</div>
		<?php }elseif ($row['status'] == "sold") { ?>
			<div class="sold-out">Sorry, this deal is sold out!</div>
		<?php }elseif ($row['start_date'] > date('Y-m-d H:i:s')) { ?>
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

		<?php if (GetDealOptionsTotal($row['item_id']) > 0) { ?>
			<?php
				$options_query = "SELECT * FROM abbijan_item_option_values WHERE item_id='".$row['item_id']."' ORDER BY option_id";
				$options_result = smart_mysql_query($options_query);
				$options_total = mysql_num_rows($options_result);

				if ($options_total > 0)
				{
			?>
				<?php echo GetOptionName($row['option_id']); ?>
				<select name="option[<?php echo $row['option_id']; ?>]">
					<option value="">-- select --</option>
					<?php while ($option_row = mysql_fetch_array($options_result)) { ?>
						<option value="<?php echo $options_row['option_value_id']; ?>"><?php echo $options_row['option_value']; ?></option>
					<?php } ?>
				</select>
			<?php
				}
			?>
		<?php } ?>


		<?php if ($row['start_date'] > date('Y-m-d H:i:s')) { ?>
			<?php
				/// Countdown ///
				echo "
					<script type=\"text/javascript\">
						$(function () {
						$('#start_".$row['item_id']."').countdown({until: $.countdown.UTCDate(".SITE_TIMEZONE.", ".$row['deal_start_date']."), compact: ".COUNTDOWN_COMPACT.",  format: '".COUNTDOWN_FORMAT."',  serverSync: ahead5Mins, 
						layout: '".COUNTDOWN_LAYOUT."'}); 
					});
					</script>";
			?>
			<div id="deal-timer">
				<span>This Deal Starts In:</span>
				<div id="start_<?php echo $row['item_id']; ?>" class="timeformat_large"></div>
			</div>
		<?php }else{ ?>
			<?php if ($row['status'] == "active") { ?>
				<?php
					/// Countdown ///
					echo "
						<script type=\"text/javascript\">
							$(function () {
							$('#count_".$row['item_id']."').countdown({until: $.countdown.UTCDate(".SITE_TIMEZONE.", ".$row['deal_end_date']."), compact: ".COUNTDOWN_COMPACT.",  format: '".COUNTDOWN_FORMAT."',  serverSync: ahead5Mins, 
							layout: '".COUNTDOWN_LAYOUT."'}); 
						});
						</script>";
				?>
				<div id="deal-timer">
					<span>This Deal Expires In:</span>
					<div id="count_<?php echo $row['item_id']; ?>" class="timeformat_large"></div>
				</div>
			<?php } ?>
		<?php } ?>

	</div>
	<div id="content_right">
		
		<div id="deal-image">
			<?php if ($row['featured'] == 1) { ?><span class="featured" alt="Featured Deal" title="Featured Deal"></span><?php } ?>
			<?php echo GetDealImages($row['item_id']); ?>
		</div>

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
				<?php if ($row['specs'] != ""){ ?><li><a href="#specs"><span>Specs</span></a></li><?php } ?>
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
				// show recent comments //
				$last_comments_query = "SELECT r.*, DATE_FORMAT(r.added, '%e/%m/%Y') AS review_date, u.user_id, u.username, u.fname, u.lname, u.avatar FROM abbijan_forum_comments r LEFT JOIN abbijan_users u ON r.user_id=u.user_id WHERE r.item_id='$item_id' AND r.status='active' ORDER BY r.added DESC LIMIT 10";
				$last_comments_result = smart_mysql_query($last_comments_query);
				$last_comments_total = mysql_num_rows($last_comments_result);

				$cc = 0;
				if ($last_comments_total > 0) {
		?>
			<?php while ($last_comments_row = mysql_fetch_array($last_comments_result)) { $cc++; ?>
            <div id="comment" style="background: <?php if (($cc%2) == 0) echo "#FFFFFF"; else echo "#F9F9F9"; ?>">
				<img src="<?php echo AVATARS_URL.$last_comments_row['avatar']; ?>" height="<?php echo AVATAR_HEIGHT; ?>" width="<?php echo AVATAR_WIDTH; ?>" alt="" class="thumb" align="left"/>
                <span class="comment-author"><?php echo $last_comments_row['fname']; ?></span>
				<span class="comment-date"><?php echo $last_comments_row['comment_date']; ?></span><br/>
				<div class="comment-text"><?php echo $last_comments_row['comment']; ?></div>
                <div style="clear: both;"></div>
            </div>
			<?php } ?>
			<div style="clear: both"></div>
		<?php }else{ ?>
				<p align="center">No comments yet. Be the first!</p>
		<?php } ?>
		</div>
		<?php } ?>

	</div>
	<div style="clear: both;"></div>

		<?php
				// start related deals //
				$query_like = "SELECT * FROM abbijan_items WHERE item_id<>'$item_id' AND status='active' ORDER BY RAND() LIMIT ".OTHER_DEALS_RESULTS;
				$result_like = smart_mysql_query($query_like);
				$total_like = mysql_num_rows($result_like);

				if ($total_like > 0)
				{
		?>
		<div class="other_deals">
			<h2>Other deals you may be interested in</h2>
			<ul id="other_deals" class="jcarousel-skin-tango">
				<?php while ($row_like = mysql_fetch_array($result_like)) { ?>
					<li>
						<a href="/deal_details.php?id=<?php echo $row_like['item_id']; ?>"><img src="<?php echo IMAGES_URL.$row_like['image']; ?>" width="130" height="130" alt="<?php echo $row_like['title']; ?>" title="<?php echo $row_like['title']; ?>" class="thumb" border="0" /></a>
						<a href="/deal_details.php?id=<?php echo $row_like['item_id']; ?>"><h2><?php echo $row_like['title']; ?> <span class="deal-price"><?php echo DisplayPrice($row_like['price']); ?></span></h2></a>
					</li>
				<?php } ?>
			</ul>
		</div>		
		<?php	} // end related deals // ?>



	<?php }else{ ?>
		<h1>Deal not found</h1>
		<p>Sorry, no deal found. <a href="/deals.php">Search for other deals</a>.</p>
	<?php } ?>



<?php require_once ("inc/footer.inc.php"); ?>