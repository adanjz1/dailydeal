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


	if (isset($_POST['action']) && $_POST['action'] == "track_order")
	{
		unset($errs);
		$errs = array();

		$order_id	= (int)getPostParameter('order_id');
		$email		= mysql_real_escape_string(strtolower(getPostParameter('email')));

		if (!($order_id && $email))
		{
			$errs[] = "Please enter your order number and email address";
		}
		else
		{
			if (!(is_numeric($order_id) && $order_id > 0))
			{
				$errs[] = "Please enter valid order number";
			}

			if (isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				$errs[] = "Please enter a valid email address";
			}
		}

		if (count($errs) == 0)
		{		
			$query = "SELECT o.*, DATE_FORMAT(o.created, '%e %b %Y %h:%i %p') AS order_date, DATE_FORMAT(o.updated, '%e %b %Y %h:%i %p') AS updated_date, u.username, u.email, u.fname, u.lname FROM abbijan_orders o LEFT JOIN abbijan_users u ON o.user_id=u.user_id WHERE o.order_id='$order_id' AND (u.username='$email' OR u.email='$email')";
			$result = smart_mysql_query($query);
			$total = mysql_num_rows($result);

			if ($total > 0)
			{
				$row = mysql_fetch_array($result);
			}
		}
		else
		{
			foreach ($errs as $errorname)
			{
				$errormsg .= "&#155; ".$errorname."<br/>\n";
			}
		}
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Order Status";

	require_once ("inc/header.inc.php");

?>

	<h1>Order Status</h1>

	<p align="center">To check your order status, please enter the order number and the email address you used when placing your order.</p>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div style="width: 400px;" class="error_msg"><?php echo $errormsg; ?></div>
	<?php } ?>

	<?php if (isset($_POST['action']) && $_POST['action'] == "track_order" && count($errs) == 0) { ?>
		<?php if ($total == 0) { ?>
			<div style="width: 400px;" class="error_msg">Sorry, order not found.</div>
		<?php }else{ ?>
			<div style="width: 400px;" class="success_msg">
			Your order status: <b><?php
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
					?></b>
			</div>
		<?php } ?>
	<?php } ?>

	<div style="width: 430px; margin: 0 auto; background: #F7F7F7; padding: 10px; border-radius: 10px;">
	<form action="" method="post">
	<table width="400" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
            <td align="right" valign="middle">Order Number:</td>
			<td align="left" valign="top"><input type="text" name="order_id" class="textbox" value="<?php echo getPostParameter('order_id'); ?>" size="30" /></td>
		</tr>
		<tr>
			<td align="right" valign="middle">Your Email:</td>
			<td align="left" valign="top"><input type="text" name="email" class="textbox" value="<?php echo getPostParameter('email'); ?>" size="30" /></td>
		</tr>
		<tr>
			<td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="action" id="action" value="track_order" />
				<input type="submit" class="submit" name="Submit" value="Submit" />
			</tr>
		</tr>
	</table>
	</form>
	</div>


<?php require_once ("inc/footer.inc.php"); ?>