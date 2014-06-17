<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");


	$results_per_page = RESULTS_PER_PAGE;
	$cc = 0;

	$item_id = (int)$_GET['id'];


	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	// add to favorites
	if (isset($_GET['act']) && $_GET['act'] == "add")
	{
		$check_query1 = smart_mysql_query("SELECT *, TIMESTAMPDIFF(SECOND, NOW(), end_date) as expire_sec, DATE_FORMAT(end_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_end_date, DATE_FORMAT(start_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_start_date FROM abbijan_items WHERE item_id='$item_id' AND start_date <= NOW() AND status!='inactive' LIMIT 1"); 
		$check_query2 = smart_mysql_query("SELECT * FROM abbijan_favorites WHERE user_id='$userid' AND item_id='$item_id'");
		
		if (mysql_num_rows($check_query1) != 0 && mysql_num_rows($check_query2) == 0)
		{
			smart_mysql_query("INSERT INTO abbijan_favorites SET user_id='$userid', item_id='$item_id', added=NOW()");
		}

		header("Location: myfavorites.php?msg=added");
		exit();
	}

	// delete from favorites
	if (isset($_GET['act']) && $_GET['act'] == "del")
	{
		$del_query = "DELETE FROM abbijan_favorites WHERE user_id='$userid' AND item_id='$item_id'";
		if (smart_mysql_query($del_query))
		{
			header("Location: myfavorites.php?msg=deleted");
			exit();
		}
	}

	$query = "SELECT abbijan_favorites.*, abbijan_items.*, DATE_FORMAT(abbijan_items.end_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_end_date, DATE_FORMAT(start_date, '%Y, %c-1, %e, %H, %i, %s') AS deal_start_date FROM abbijan_favorites abbijan_favorites, abbijan_items abbijan_items WHERE abbijan_favorites.user_id='$userid' AND abbijan_favorites.item_id=abbijan_items.item_id ORDER BY abbijan_items.title ASC LIMIT ".$from.",".$results_per_page;
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "My Favorites";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");
	
?>

<div id="account_content">

	<h1>My Favorites</h1>


		<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
		<div style="width: 94%;" class="success_msg">
			<?php
				switch ($_GET['msg'])
				{
					case "added": echo "Deal has been added to your favorites list"; break;
					case "deleted": echo "Deal has been deleted"; break;
				}
			?>
		</div>
		<?php } ?>


	<?php

		if ($total > 0) {
 
	?>

		<p>Below is a list of your saved favorite deals.</p>

         <table class="brd" align="center" width="100%" border="0" cellspacing="0" cellpadding="3">
            <tr>
				<th width="15%">Deal</th>
				<th width="70%">&nbsp;</th>
				<th width="15%">&nbsp;</th>
            </tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++ ?>
			<tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
              <td nowrap="nowrap" valign="middle" align="center">
			  	 <a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><img src="<?php echo IMAGES_URL.$row['thumb']; ?>" width="75" height="75" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" class="thumb" /></a><br/>
				 <a href="#" onclick="if (confirm('Are you sure you really want to delete this deal from your favorites?') )location.href='/myfavorites.php?act=del&id=<?php echo $row['item_id']; ?>'" title="Delete"><img src="images/icon_delete.png" border="0" alt="Delete" /></a>
			  </td>
              <td valign="top" align="left">
				<h3><a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><?php echo $row['title']; ?></a></h3>
				<?php if ($row['status'] == "expired") { ?>
					<b>Deal has ended!</b>
				<?php }elseif ($row['status'] == "sold") { ?>
					<b>Deal is sold out!</b>
				<?php }else{ ?>

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
							<span>This Deal Starts In:</span><br/>
							<div id="start_<?php echo $row['item_id']; ?>" class="timeformat"></div>
						</div>
					<?php }else{ ?>
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
							<span>This Deal Expires In:</span><br/>
							<div id="count_<?php echo $row['item_id']; ?>" class="timeformat"></div>
						</div>
					<?php } ?>
				
				<?php } ?>
              </td>
			  <td nowrap="nowrap" valign="top" align="center"><span class="deal-price"><?php echo DisplayPrice($row['price']); ?></span></td>
            </tr>
			<?php } ?>
         </table>

				<?php echo ShowPagination("favorites",$results_per_page,"myfavorites.php?".$params."column=$rrorder&order=$rorder&","WHERE user_id='$userid'"); ?>

    <?php }else{ ?>
			<p align="center">You don't have favorite deals at this time.</p>
     <?php } ?>


</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>