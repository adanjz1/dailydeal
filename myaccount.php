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

	$cc = 0;

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "My Account";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">

	<h1>My Account</h1>


	<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
	<div class="success_msg" style="width: 94%;">
		<?php if ($_GET['msg'] == "welcome") { ?>Your account is now active and you are currently signed in!<?php } ?>
	</div>
	<?php } ?>

	<p>Welcome, <b><?php echo $_SESSION['FirstName']; ?></b>. You are currently signed in.</p>
	<?php if (GetUserBalance($userid, $hide_currency = 1) > 0) { ?><p>Account balance: <b><?php echo GetUserBalance($userid); ?></b></p><?php } ?>
	

	<?php
		// show 10 recent orders
		$orders_query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS order_date FROM abbijan_orders WHERE user_id='$userid' ORDER BY created DESC LIMIT 5";
		$orders_result = smart_mysql_query($orders_query);
		$orders_total = mysql_num_rows($orders_result);

		if ($orders_total > 0) {
	?>
		
		<div class="view_all"><a href="/myorders.php" class="more">view all orders</a></div>
		<h3>Recent Orders</h3>
        <table class="brd" width="100%" align="center" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <th width="20%">Date</th>
			<th width="25%">Reference ID</th>
            <th width="25%">Items</th>
			<th width="17%">Total</th>
            <th width="17%">Status</th>
        </tr>
		<?php while ($orders_row = mysql_fetch_array($orders_result)) { $cc++; ?>
		<tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
			<td valign="middle" align="center"><?php echo $orders_row['order_date']; ?></td>
			<td valign="middle" align="center"><a href="/myorder.php?id=<?php echo $orders_row['order_id']; ?>"><?php echo $orders_row['reference_id']; ?></a></td>
			<td valign="middle" align="center"><a href="/myorder.php?id=<?php echo $orders_row['order_id']; ?>"><?php echo GetOrderItemsTotal($orders_row['order_id']); ?> items</a></td>
			<td valign="middle" align="center"><?php echo DisplayMoney($orders_row['total']+$orders_row['shipping_total']); ?></td>
			<td nowrap="nowrap" valign="middle" align="center">
			<?php
				switch ($orders_row['status'])
				{
					case "complete": echo "<span class='complete_status'>complete</span>"; break;
					case "shipped": echo "<span class='shipped_status'>shipped</span>"; break;
					case "delivered": echo "<span class='delivered_status'>delivered</span>"; break;
					case "pending": echo "<span class='pending_status'>pending</span>"; break;
					case "declined": echo "<span class='declined_status'>declined</span>"; break;
					case "refunded": echo "<span class='refund_status'>refunded</span>"; break;
					default: echo "<span class='payment_status'>".$orders_row['status']."</span>"; break;
				}
			?>
			</td>
         </tr>
		<?php } ?>
		</table>
		<br/>

	<?php }else{ ?>
		<h3>Recent Orders</h3>
		<p>You have not yet made any purchases.</p>
	<?php }?>

</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>