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
	require_once("inc/pagination.inc.php");


	$results_per_page = RESULTS_PER_PAGE;
	$cc = 0;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$where = " start_date>NOW() AND status!='inactive'";
	
	$query = "SELECT *, DATE_FORMAT(start_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_start_date, DATE_FORMAT(end_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_end_date FROM abbijan_items WHERE $where ORDER BY featured DESC, start_date ASC LIMIT $from, $results_per_page";
	
	$total_result = smart_mysql_query("SELECT * FROM abbijan_items WHERE $where ORDER BY title ASC");
	$total = mysql_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Future Deals";

	require_once ("inc/header.inc.php");

?>

	<h1>Future Deals</h1>


	<?php if ($total > 0) { ?>

		<p>Soon on sale. Do not miss out!</p>

		<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
		<div class="deal-item" style="background: <?php if (($cc%2) == 0) echo "#F9F9F9"; else echo "#FFFFFF"; ?>">
		<div class="deal-image">
			<?php if ($row['featured'] == 1) { ?><span class="featured" alt="Featured Deal" title="Featured Deal"></span><?php } ?>
			<a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><img src="<?php echo IMAGES_URL.$row['thumb']; ?>" width="<?php echo THUMB_WIDTH; ?>" height="<?php echo THUMB_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" class="thumb" /></a>
		</div>
		<div class="deal-info">
			<h3><a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><?php if (strlen($row['title']) > 75) $deal_title = substr($row["title"], 0, 70)."..."; else $deal_title = $row["title"]; echo $deal_title; ?></a></h3>
			<div class="info-detail"><?php echo substr(stripslashes($row["brief_description"]), 0, 250); ?></div>
			<div class="share-button">
				<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
				<fb:like href="<?php echo SITE_URL."deal_details.php?id=".$row['item_id']; ?>" layout="button_count" show_faces="false" ></fb:like>
			</div>
			<div class="share-button">
				<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo SITE_URL."deal_details.php?id=".$row['item_id']; ?>" data-text="<?php echo $row['title']." - ".DisplayPrice($row['price']); ?>" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			</div>
			<div class="share-button">
				<g:plusone size="medium" annotation="none"></g:plusone>
				<script type="text/javascript">
				  (function() {
					var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					po.src = 'https://apis.google.com/js/plusone.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				  })();
				</script>
			</div>
			<div class="share-button">
				&nbsp;&nbsp; <a href="mailto:?body=<?php echo htmlentities(SITE_URL."deal_details.php?id=".$row['item_id']."&subject=".$row['title']); ?>"><img src="/images/icon_mail.png" /></a>
			</div>
		</div>
		<div class="deal-values">
			<div class="deal-price"><?php echo DisplayPrice($row['price']); ?></div>
			<?php if ($row['retail_price'] != "0.00") { ?>
			<table class="deal-savings">
			  <tr>
					<td class="deal-regular-price"><span>Value</span><?php echo DisplayPrice($row['retail_price']); ?></td>
					<td class="deal-discount-percent"><span>Discount</span><?php echo CalculateSavingsPercentage($row['retail_price'],$row['price']); ?></td>
					<td class="deal-discount-amount"><span>Savings</span><?php echo DisplayPrice($row['discount']); ?></td>
				</tr>
			</table>
			<?php } ?>

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
					<div id="start_<?php echo $row['item_id']; ?>" class="timeformat"></div>
				</div>
		</div>
		<div style="clear: both"></div>
		</div>
		<?php } ?>

				<?php echo ShowPagination("items",$results_per_page,"future_deals.php?".$params."column=$rrorder&order=$rorder&","WHERE ".$where); ?>

	<?php }else{ ?>
			<center><h2>There are no deals at this time. Please check back soon.</h2></center>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>