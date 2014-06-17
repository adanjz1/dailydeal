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

	if (isset($_SESSION['order_id']) && is_numeric($_SESSION['order_id']))
	{
		$order_id = (int)$_SESSION['order_id'];

		// send notification
		if (NEW_ORDER_ALERT == 1)
		{
			$message = "New order placed";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
			@mail(SITE_MAIL, "New order placed", $message, $headers);
		}
	}
	else
	{		
		header ("Location: /");
		exit();
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Order Complete";
	
	require_once ("inc/header.inc.php");
	
?>

	<table id="steps" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="" valign="top">
			<span class="number">1</span> <span class="title">Shopping Cart</span>
		</td>
		<td class="" valign="top">
			<span class="number">2 </span> <span class="title">Your Details</span>
		</td>
		<td class="" valign="top">
			<span class="number">3 </span> <span class="title">Payment Method</span>
		</td>
		<td class="" valign="top">
			<span class="number">4 </span> <span class="title">Checkout</span>
		</td>
		<td class="first active" valign="top">
			<span class="number">5 </span> <span class="title">Complete</span>
		</td>
	</tr>
	</table>


	<h1 class="success">Order Complete</h1>

	<p>Thank you for your purchase!</p>
	<p>Your order number: <b><?php echo $order_id; ?></b></p>
	<p>&nbsp;</p>

	<p>	
		<a href="/" class="button">Back to home</a> 
		<a href="/deals.php" class="button">All Deals</a> 
		<a href="/mytestimonial.php" class="button">Write feedback about us</a>
	</p>


<?php require_once ("inc/footer.inc.php"); ?>