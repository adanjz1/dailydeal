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

	$results_per_page = PAST_DEALS_RESULTS;
	$cc = 0;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$where = " end_date<NOW() OR status='sold'";
	
	$query = "SELECT *, DATE_FORMAT(end_date, '%W %d %M, %Y') as expired_date FROM abbijan_items WHERE $where ORDER BY end_date DESC LIMIT $from, $results_per_page";
	$total_result = smart_mysql_query("SELECT * FROM abbijan_items WHERE $where");
	$total = mysql_num_rows($total_result);
	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Past Deals";

	require_once ("inc/header.inc.php");

?>

	<h1>Past Deals</h1>


	<?php if ($total > 0) { ?>

			<p align="center">Here you will find our past deals. See what deals you missed.</p>

			<div id="past_deals">
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				<div id="past_deal">
					<span class="date"><?php echo $row['expired_date']; ?></span>
					<span class="price"><?php echo DisplayPrice($row['price']); ?></span>
					<a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><img src="<?php echo IMAGES_URL.$row['thumb']; ?>" width="<?php echo THUMB_WIDTH; ?>" height="<?php echo THUMB_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" class="thumb" /></a>
					<h3><a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><?php echo $row['title']; ?></a></h3>
				</div>
			<?php } ?>
			</div>
			<div style="clear: both"></div>
			
			<?php echo ShowPagination("items",$results_per_page,"past_deals.php?","WHERE ".$where); ?>

	<?php }else{ ?>
			<center><h2>There are no deals history at this time.</h2></center>
			<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>