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

	$results_per_page = 10;
	$cc = 0;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS order_date FROM abbijan_orders WHERE user_id='$userid' ORDER BY created DESC LIMIT $from, $results_per_page";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	
	///////////////  Page config  ///////////////
	$PAGE_TITLE = "My Orders";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">

	<h1>My Orders <?php echo ($total > 0) ? "(".$total.")" : ""; ?></h1>


	<?php if ($total > 0) {  ?>
    
			<table class="brd" align="center" width="100%" border="0" cellspacing="0" cellpadding="3">
              <tr>
                <th width="20%">Date</th>
				<th width="25%">Reference ID</th>
                <th width="25%">Items</th>
				<th width="17%">Total</th>
                <th width="17%">Status</th>
				<th width="10%"></th>
              </tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
                  <td valign="middle" align="center"><?php echo $row['order_date']; ?></td>
                  <td valign="middle" align="center"><a href="/myorder.php?id=<?php echo $row['order_id']; ?>"><?php echo $row['reference_id']; ?></a></td>
                  <td valign="middle" align="center"><a href="/myorder.php?id=<?php echo $row['order_id']; ?>"><?php echo GetOrderItemsTotal($row['order_id']); ?> items</a></td>
				  <td valign="middle" align="center"><?php echo DisplayMoney($row['total']+$row['shipping_total']); ?></td>
                  <td nowrap="nowrap" valign="middle" align="center">
					<?php
							switch ($row['status'])
							{
								case "complete": echo "<span class='complete_status'>complete</span>"; break;
								case "shipped": echo "<span class='shipped_status'>shipped</span>"; break;
								case "delivered": echo "<span class='delivered_status'>delivered</span>"; break;
								case "pending": echo "<span class='pending_status'>pending</span>"; break;
								case "declined": echo "<span class='declined_status'>declined</span>"; break;
								case "refunded": echo "<span class='refund_status'>refunded</span>"; break;
								default: echo "<span class='payment_status'>".$row['status']."</span>"; break;
							}
					?>
				  </td>
				  <td valign="middle" align="center"><a href="/myorder.php?id=<?php echo $row['order_id']; ?>"><img src="/images/icon_view.png" /></a></td>
                </tr>
			<?php } ?>
           </table>

				<?php echo ShowPagination("orders",$results_per_page,"myorders.php?","WHERE user_id='$userid'"); ?>

	<?php }else{ ?>
			<p align="center">You have not yet made any purchases.</p>
			<p align="center"><a class="button" href="/">Start Shopping &raquo;</a></p>
	<?php } ?>


</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>