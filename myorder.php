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

	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$order_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}


	$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS order_date, DATE_FORMAT(updated, '%e %b %Y %h:%i %p') AS updated_date FROM abbijan_orders WHERE order_id='$order_id' AND user_id='$userid' ORDER BY created DESC LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	
	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Order Details";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">

	<h1>Order Details</h1>


	<?php
		if ($total > 0) { $row = mysql_fetch_array($result);
	?>

		<div class="order_column1">
		<table width="100%" cellpadding="2" cellspacing="2" border="0" align="center">
		<tr>
			<td nowrap="nowrap" width="100" align="left" valign="top">Order Number:</td>
			<td align="left" valign="top"><?php echo $row['reference_id']; ?></td>
		</tr>
		<tr>
			<td nowrap="nowrap" width="100" align="left" valign="top">Payment Method:</td>
			<td align="left" valign="top"><?php echo GetPaymentMethodName($row['payment_method_id']); ?></td>
		</tr>
		<tr>
			<td align="left" valign="top">Date:</td>
			<td align="left" valign="top"><?php echo $row['order_date']; ?></td>
		</tr>
		<tr>
			<td align="left" valign="top">Status:</td>
			<td align="left" valign="top">
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
		</tr>
		<?php if ($row['reason'] != "") { ?>
		<tr>
			<td align="left" valign="top">Reason:</td>
			<td align="left" valign="top"><span class="order_reason"><?php echo $row['reason']; ?></span></td>
		</tr>
		<?php } ?>
		<?php if ($row['updated_date'] != "") { ?>
		<tr>
			<td align="left" valign="top">Updated:</td>
			<td align="left" valign="top"><?php echo $row['updated_date']; ?></td>
		</tr>
		<?php } ?>
		</table>
		</div>

		<div class="order_column2">
			<b>Shipping Address</b>
			<div class="shipping-address"><?php echo $row['shipping_details']; ?></div>
		</div>
		<div style="clear: both"></div>
	

		<?php
				$items_query = "SELECT * FROM abbijan_order_items WHERE order_id='$order_id'";
				$items_result = smart_mysql_query($items_query);
				$items_total = mysql_num_rows($items_result);
		?>
			<br/><br/>
			<table width="100%" cellpadding="2" cellspacing="2" border="0" align="center">
			<tr>
				<td colspan="5" align="left" valign="top"><b>Purchased Items</b></td>
			</tr>
			<tr>
				<td align="left" valign="top">
					<table width="100%" style="border-bottom: 1px solid #F2F2F2;" cellpadding="3" cellspacing="0" border="0">
					<tr height="30" bgcolor="#F7F7F7" align="center">
						<td nowrap="nowrap" width="10%" style="border-right: 1px solid #FFFFFF;">Item ID</td>
						<td width="45%" style="border-right: 1px solid #FFFFFF;">Item</td>
						<td nowrap="nowrap" width="15%" style="border-right: 1px solid #FFFFFF;">Price</td>
						<td nowrap="nowrap" width="13%" style="border-right: 1px solid #FFFFFF;">Quantity</td>
						<td nowrap="nowrap" width="15%" style="border-right: 1px solid #FFFFFF;">Subtotal</td>
					</tr>
					<?php
				 
						if ($items_total > 0)
						{
							while ($item_row = mysql_fetch_array($items_result))
							{
								$cc++;
								$subtotal			= $item_row['item_price']*$item_row['item_quantity'];
								$TotalPrice			+= $subtotal;
					?>
					<tr height="25" bgcolor="<?php if (($cc%2) == 0) echo "#F9F9F9"; else echo "#FFFFFF";?>">
						<td align="center" valign="middle"><?php echo $item_row['item_id']; ?></td>
						<td align="left" valign="middle"><a href="deal_details.php?id=<?php echo $item_row['item_id']; ?>"><?php echo $item_row['item_title']; ?></a></td>
						<td align="center" valign="middle"><?php echo DisplayPrice($item_row['item_price']); ?></td>
						<td align="center" valign="middle"><?php echo $item_row['item_quantity']; ?></td>
						<td align="center" valign="middle"><?php echo DisplayPrice($item_row['item_price']*$item_row['item_quantity']); ?></td>
					</tr>
					<?php } ?>

			<?php }else{ ?>
					<tr height="20"><td valign="middle" align="center" colspan="5"><p>Sorry, no items found for this order.</p></td></tr>
			<?php } ?>
					</table>
				</td>
			</tr>
			<tr>
			<td colspan="5" align="right" valign="top">
              <table width="180" border="0" cellpadding="3" cellspacing="3">
				<tr>
					<td width="100" nowrap="nowrap" valign="middle" align="right">Order Subtotal:</td>
					<td nowrap="nowrap" valign="middle" align="right"><?php echo DisplayMoney($TotalPrice); ?></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="middle" align="right">Shipping:</td>
					<td nowrap="nowrap" valign="middle" align="right"><?php echo DisplayMoney($row['shipping_total']); ?></td>
				</tr>
                <tr height="40">
					<td nowrap="nowrap" valign="middle" align="right"><b>TOTAL:</b></td>
					<td nowrap="nowrap" valign="middle" align="right"><b><?php echo DisplayMoney($row['total']+$row['shipping_total']); ?></b></td>
				</tr>
			  </table>
			</td>
			</tr>
            <tr>
              <td align="center" valign="bottom">
				<a href="#" class="goback" onclick="javascript:document.location.href='myorders.php'" />Go Back</a>
			  </td>
            </tr>
			</table>

           </table>

     <?php }else{ ?>
				<p align="center">Sorry, order not found.</p>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
	 <?php } ?>


</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>