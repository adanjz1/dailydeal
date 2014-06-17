<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("../inc/auth_adm.inc.php");
	require_once("../inc/config.inc.php");



	if (isset($_POST['action']) && $_POST['action'] == "change_status")
	{
		unset($errors);
		$errors = array();

		$order_id		= (int)getPostParameter('order_id');
		$pn				= (int)getPostParameter('pn');
		$status			= mysql_real_escape_string(getPostParameter('status'));
		$reason			= mysql_real_escape_string(getPostParameter('reason'));
		$notification	= (int)getPostParameter('notification');

		if (!$status)
		{
			$errors[] = "Please select order status";
		}

		if (count($errors) == 0)
		{
			$oresult = smart_mysql_query("SELECT * FROM abbijan_orders WHERE order_id='$order_id' LIMIT 1");
			if (mysql_num_rows($oresult) > 0)
			{
				$orow = mysql_fetch_array($oresult);
			}

			if ($status == "complete" || $status == "delivered")
			{
				// check if user received "Refer a Friend" bonus before
				$uresult = smart_mysql_query("SELECT * FROM abbijan_users WHERE user_id='$user_id' AND ref_bonus='0' LIMIT 1");
				$utotal = mysql_num_rows($uresult);

				// Add "Refer a Friend" bonus
				if ($utotal > 0 && REFER_FRIEND_BONUS > 0)
				{
					$urow = mysql_fetch_array($uresult);
					$ref_res = smart_mysql_query("UPDATE abbijan_users SET balance=balance+".REFER_FRIEND_BONUS." WHERE user_id='".(int)$urow['ref_id']."' LIMIT 1");
			
					// mark that "Refer a Friend" bonus received by user
					smart_mysql_query("UPDATE abbijan_users SET ref_bonus='1' WHERE user_id='$user_id' LIMIT 1");
				}
				////////////////////////////	
			}

			if ($orow['status'] == "pending" && $status == "complete")
			{
				// send order receipt
				SendReceipt($order_id);
			}

			smart_mysql_query("UPDATE abbijan_orders SET status='$status', reason='$reason', updated=NOW() WHERE order_id='$order_id' LIMIT 1");


			/////////////////////////////////// send order status notification ////////////////////////////
			if ($notification == 1)
			{
				$oresult = smart_mysql_query("SELECT users.*, orders.* FROM abbijan_users users, abbijan_orders orders WHERE users.user_id=orders.user_id AND orders.order_id='$order_id' LIMIT 1");

				if (mysql_num_rows($oresult) > 0)
				{
					$orow = mysql_fetch_array($oresult);

					$etemplate = GetEmailTemplate('order_status');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];

					$emessage = str_replace("{first_name}", $orow['fname'], $emessage);
					$emessage = str_replace("{order_id}", $orow['reference_id'], $emessage);

					switch ($orow['status'])
					{
						case "complete": $new_status = "complete"; break;
						case "shipped": $new_status = "shipped"; break;
						case "delivered": $new_status = "delivered"; break;
						case "pending": $new_status = "pending"; break;
						case "declined": $new_status = "declined"; break;
						case "refunded": $new_status = "refunded"; break;
						default: $new_status = $row['status']; break;
					}

					$emessage = str_replace("{order_status}", $new_status, $emessage);

					$user_name = $orow['fname']." ".$orow['lname'];
					$user_email = $orow['email'];

					$to_email = $user_name.' <'.$user_email.'>';
					$subject = $esubject;
					$message = $emessage;
				
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";

					@mail($to_email, $subject, $message, $headers);
				}
			}
			///////////////////////////////////////////////////////////////////////////////////////////

			header("Location: orders.php?pn=".$pn."&column=&order=&msg=updated");
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];
		$pn	= (int)$_GET['pn'];
		
		$query = "SELECT o.*, DATE_FORMAT(o.created, '%e %b %Y %h:%i %p') AS order_date, DATE_FORMAT(o.updated, '%e %b %Y %h:%i %p') AS updated_date, u.username, u.email, u.fname, u.lname FROM abbijan_orders o LEFT JOIN abbijan_users u ON o.user_id=u.user_id WHERE o.order_id='$id'";
		
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

		smart_mysql_query("UPDATE abbijan_orders SET viewed='1' WHERE order_id='$id'");
	}

	$cc = 0;

	$title = "Order Details";
	require_once ("inc/header.inc.php");

?>

	<div style="float: right; padding-top: 10px;"><a href="order_print.php?id=<?php echo $id; ?>" target="_blank" title="Print"><img src="images/print.png" border="0" alt="Print" align="absmiddle" /> Print order</a></div>
     <h2 class="orders">Order Details</h2>

	<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
		<div class="success_box">
		<?php

			switch ($_GET['msg'])
			{
				case "confirmed": echo "Order has been confirmed!"; break;
				case "updated": echo "Order status has been successfully updated!"; break;
			}
		?>
		</div>
	<?php } ?>


	<?php if ($total > 0) { 

			$row = mysql_fetch_array($result);
	 ?>

<table width="100%" cellpadding="3" cellspacing="5" border="0" align="center">
<tr>
<td width="35%" align="left" valign="top">

            <table width="100%" cellpadding="3" cellspacing="5" border="0" align="center">
              <tr>
                <td width="70" valign="middle" align="right" class="tb1">Order ID:</td>
                <td valign="top"><?php echo $row['order_id']; ?></td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="middle" align="right" class="tb1">Reference ID:</td>
                <td valign="top"><?php echo $row['reference_id']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Username:</td>
                <td valign="top"><?php echo $row['username']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Customer Name:</td>
                <td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Email Address:</td>
                <td valign="top"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Total:</td>
                <td valign="top"><span class="price"><?php echo DisplayMoney($row['total']); ?></span></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Status:</td>
                <td valign="top">
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
                <td valign="middle" align="right" class="tb1">Reason:</td>
                <td style="color:#FC5F5F;background:#FFEBEB;font-size:10px;" valign="top"><?php echo $row['reason']; ?></td>
              </tr>
			  <?php } ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Created:</td>
                <td valign="top"><?php echo $row['order_date']; ?></td>
              </tr>
			  <?php if ($row['updated_date'] != "") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Updated:</td>
                <td valign="top"><?php echo $row['updated_date']; ?></td>
              </tr>
			  <?php } ?>
          </table>

</td>
<td width="2%" style="border-right: 1px dotted #EEE;" align="left" valign="top">&nbsp;</td>
<td width="30%" align="left" valign="top">

    <p><b>Payment Method</b></p>
    <?php echo GetPaymentMethodName($row['payment_method_id']); ?>
  
	<?php if ($row['payment_details'] != "") { ?>
    <p><b>Payment Details</b></p>
    <?php echo $row['payment_details']; ?>
	<?php } ?>

    <p><b>Shipping Method</b></p>
    <?php echo GetShippingMethodName($row['shipping_method_id']); ?>

	<p><b>Shipping Address</b></p>
	<?php echo $row['shipping_details']; ?>

</td>
<td width="30%" bgcolor="#F9F9F9" style="border-left: 1px dotted #EEE;" align="center" valign="top">

		<h3>Proceed Order</h3>

		<?php if (isset($errormsg) && $errormsg != "") { ?>
			<div style="margin: 0 auto; width: 240px;" class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

		<script type="text/javascript">
		<!--
			function hiddenDiv(){
				if(document.getElementById("status").value == "declined" || document.getElementById("status").value == "refunded"){
					document.getElementById("reason_box").style.display = "";
				}else{
					document.getElementById("reason_box").style.display = "none";
				}
			}
		-->
		</script>

		<form action="" method="post">
		  <table width="290" style="border: 1px solid #EEE;" bgcolor="#FFFFFF" cellpadding="3" cellspacing="5" border="0" align="center">
		  <tr>
			<td width="80" align="right" valign="middle" class="tb1">Change status:</td>
			<td align="left" valign="middle">
			<select name="status" id="status" class="textbox2" onchange="javascript:hiddenDiv()">
				<option value="">------------</option>
				<option value="shipped" style="background:#54ACEA;color:#FFF;">Shipped</option>
				<option value="delivered" style="background:#55FC02;color:#FFF;">Delivered</option>
				<option value="complete" style="background:#A5F407;color:#FFF;">Complete</option>
				<option value="declined" style="background:#FC6D5D;color:#FFF;">Declined</option>
				<option value="refunded" style="background:#CECECE;color:#555;">Refunded</option>
			</select>
			</td>
		  </tr>
		  <tr id="reason_box" <?php if ($status != "declined" && $status != "refunded") { ?>style="display: none;"<?php } ?>>
			<td align="right" valign="middle" class="tb1">Reason:</td>
			<td align="left" valign="middle"><textarea name="reason" cols="30" rows="4" class="textbox2"><?php echo $row['reason']; ?></textarea></td>
		  </tr>
		  <tr>
			<td align="left" valign="middle" class="tb1">&nbsp;</td>
			<td align="left" valign="middle"><input type="checkbox" class="checkboxx" name="notification" value="1" <?php if (getPostParameter('notification') == 1) echo "checked=\"checked\""; ?> />Send notification to customer</td>
		  </tr>
		  <tr>
			<td align="left" valign="top">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="order_id" value="<?php echo (int)$row['order_id']; ?>" />
				<input type="hidden" name="action" value="change_status" />
				<input type="submit" name="submit" class="submit" value="Change" />
			</td>
		  </tr>
		  </table>
		</form>
		<br/>
</td>
</tr>
<tr>
	<td colspan="4" style="border-top: 1px dotted #EEE;">&nbsp;</td>
</tr>
</table>

		<?php
				$items_query = "SELECT * FROM abbijan_order_items WHERE order_id='$id' ORDER BY order_item_id";
				$items_result = smart_mysql_query($items_query);
				$items_total = mysql_num_rows($items_result);
		?>

			<table width="100%" cellpadding="2" cellspacing="2" border="0" align="center">
			<tr>
				<td align="left" valign="top"><b>Order Items</b></td>
			</tr>
			<tr>
				<td align="left" valign="top">
					<table width="100%" style="border-bottom: 1px solid #F2F2F2;" cellpadding="3" cellspacing="0" border="0">
					<tr height="25" bgcolor="#F7F7F7" align="center">
						<td nowrap="nowrap" width="10%" style="border-right: 1px solid #FFFFFF;">Item ID</td>
						<td width="50%" style="border-right: 1px solid #FFFFFF;">Item Name</td>
						<td nowrap="nowrap" width="15%" style="border-right: 1px solid #FFFFFF;">Price</td>
						<td nowrap="nowrap" width="13%" style="border-right: 1px solid #FFFFFF;">Quantity</td>
						<td nowrap="nowrap" width="17%" style="border-right: 1px solid #FFFFFF;">Subtotal</td>
					</tr>
					<?php
				 
						if ($items_total > 0)
						{
							while ($item_row = mysql_fetch_array($items_result))
							{
								$cc++;

								$subtotal		= $item_row['item_price']*$item_row['item_quantity'];
								$TotalPrice		+= $subtotal;
								//$ShippingPrice	+= $item_row['shipping_price']*$item_row['item_quantity'];

					?>
					<tr height="25" bgcolor="<?php if (($cc%2) == 0) echo "#F7F7F7"; else echo "#FFFFFF";?>">
						<td align="center" valign="middle"><?php echo $item_row['item_id']; ?></td>
						<td align="left" valign="middle"><a href="deal_details.php?id=<?php echo $item_row['item_id']; ?>"><?php echo $item_row['item_title']; ?></a></td>
						<td align="center" valign="middle"><?php echo DisplayMoney($item_row['item_price']); ?></td>
						<td align="center" valign="middle"><?php echo $item_row['item_quantity']; ?></td>
						<td align="center" valign="middle"><?php echo DisplayMoney($item_row['item_price']*$item_row['item_quantity']); ?></td>
					</tr>
					<?php
							}
						}
						else
						{
							echo "<tr height='20'><td valign='middle' align='center' colspan='5'><span class='note'>Sorry, no items found for this order.</span></td></tr>";
						}
					?>
					</table>
				</td>
			</tr>
			<tr>
			<td align="right" valign="top">
				<div style="float:right; padding:10px 0;">
              <table border="0" cellpadding="3" cellspacing="3">
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
			</div>
			</td>
			</tr>
            <tr>
              <td colspan="5" align="center" valign="bottom">
				<input type="button" class="submit" name="cancel" value="Go Back" onclick="javascript:document.location.href='orders.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
			  </td>
            </tr>
			</table>

      <?php }else{ ?>
				<div class="info_box">Sorry, no order found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>