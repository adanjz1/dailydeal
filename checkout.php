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


	$cart_items = $_SESSION['cart_items'];
	$cart_items[] = "11111111111111111";
	$cart_items = array_map('intval', $cart_items);

	$query = "SELECT * FROM abbijan_items WHERE item_id IN (".@implode(",", $cart_items).") AND deal_type<>'affiliate' AND start_date<=NOW() AND end_date>NOW() AND status='active' ORDER BY title";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total == 0)
	{
		header ("Location: /cart.php");
		exit();
	}


	if (isset($_POST["action"]) && $_POST["action"] == "checkout")
	{
		unset($errs);
		$errs = array();

		$success					= 0;
		$reference_id				= GenerateReferenceID();
		$order_total				= DisplayMoney($_SESSION['Total'], 1);
		$shipping_method			= (int)getPostParameter('shipping_method');
		$shipping_method_id			= (int)getPostParameter('shipping_method_id');
		$shipping_total				= GetShippingCost($shipping_method);
		$payment_method				= (int)getPostParameter('payment_method');
		$payment_method_name		= mysql_real_escape_string(GetPaymentMethodName($payment_method));
		$_SESSION['order_id']		= $reference_id;
		$_SESSION['ShippingPrice']	= $shipping_total;


		if ($shipping_method_id == 0)
		{
			unset($errs);
			$errs = array();

			$fname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('fname'))));
			$lname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('lname'))));
			$address	= mysql_real_escape_string(getPostParameter('address'));
			$address2	= mysql_real_escape_string(getPostParameter('address2'));
			$city		= mysql_real_escape_string(getPostParameter('city'));
			$state		= mysql_real_escape_string(getPostParameter('state'));
			$zip		= mysql_real_escape_string(getPostParameter('zip'));
			$country	= mysql_real_escape_string(getPostParameter('country'));
			$phone		= mysql_real_escape_string(getPostParameter('phone'));

			if (!($fname && $lname && $address && $city && $state && $zip && $country && $phone))
			{
				$errs[] = "Please enter shipping information";
			}
		}
		else if ($shipping_total == false)
		{
			$errs[] = "Please select shipping method";
		}
		else if ($shipping_method_id > 0)
		{	
			$scheck_query = "SELECT * FROM abbijan_shipping WHERE shipping_id='$shipping_method_id' AND user_id='$userid' LIMIT 1";
			$scheck_result = smart_mysql_query($scheck_query);

			if (mysql_num_rows($scheck_result) == 0)
			{
				$errs[] = "Saved shipping address not found";
			}
			else
			{
				$shipping_row = mysql_fetch_array($scheck_result);

				$fname		= $shipping_row['fname'];
				$lname		= $shipping_row['lname'];
				$address	= $shipping_row['address'];
				$address2	= $shipping_row['address2'];
				$city		= $shipping_row['city'];
				$state		= $shipping_row['state'];
				$zip		= $shipping_row['zip'];
				$country	= $shipping_row['country'];
				$phone		= $shipping_row['phone'];
			}
		}
		else if (!$shipping_method)
		{
			$errs[] = "Please select shipping method";
		}

		if (!$payment_method)
		{
			$errs[] = "Please select payment method";
		}
		else
		{
			if ($payment_method == 2)
			{
				$cc_fname			= mysql_real_escape_string(getPostParameter('cc_fname'));
				$cc_lname			= mysql_real_escape_string(getPostParameter('cc_lname'));
				$cc_type			= mysql_real_escape_string(getPostParameter('cc_type'));
				$cc_number			= mysql_real_escape_string(getPostParameter('cc_number'));
				$cc_month			= mysql_real_escape_string(getPostParameter('cc_month'));
				$cc_year			= mysql_real_escape_string(getPostParameter('cc_year'));
				$exp_date			= $cc_month."".$cc_year; //$exp_date = $cc_month."".substr($cc_year, -2);
				$cc_cvv				= mysql_real_escape_string(getPostParameter('cc_cvv'));
				$billing_address	= mysql_real_escape_string(getPostParameter('billing_address'));
				$billing_address2	= mysql_real_escape_string(getPostParameter('billing_address2'));
				$billing_city		= mysql_real_escape_string(getPostParameter('billing_city'));
				$billing_state		= mysql_real_escape_string(getPostParameter('billing_state'));
				$billing_zip		= mysql_real_escape_string(getPostParameter('billing_zip'));
				$billing_country	= mysql_real_escape_string(getPostParameter('billing_country'));
				$billing_phone		= mysql_real_escape_string(getPostParameter('billing_phone'));

				if (!($cc_fname && $cc_lname && $cc_number && $cc_month && $cc_year && $cc_cvv && $billing_address && $billing_city && $billing_state && $billing_zip && $billing_country && $billing_phone))
				{
					$errs[] = "Please fill in all required fields";
				}
				elseif (!$cc_type)
				{
					$errs[] = "Please select credit card type";
				}
			}
			else if ($payment_method == 3)
			{
				if (GetUserBalance($userid, $hide_currency = 1) < $order_total)
				{
					$errs[] = "Sorry, you have not enough money in your account to complete order";
				}
			}
		}


		if (count($errs) == 0)
		{
				$shipping_address = "";
				$shipping_address .= $address."<br/>";
				if ($address2 != "") $shipping_address .= $address2."<br/>";
				$shipping_address .= $city.", ".$state." ".$zip."<br/>";
				$shipping_address .= $country."<br/>";
				$shipping_address .= $phone;
				$shipping_address = mysql_real_escape_string($shipping_address);


				// save shipping address
				if ($shipping_method_id == 0)
				{
					smart_mysql_query("INSERT INTO abbijan_shipping SET user_id='$userid', shipping_name='My Shipping Address', fname='$fname', lname='$lname', address='$address', address2='$address2', city='$city', state='$state', zip='$zip', country='$country', phone='$phone'");
				}
				
				if ($payment_method == 1) // paypal ipn
				{
					$oresult = smart_mysql_query("INSERT INTO abbijan_orders SET reference_id='$reference_id', shipping_id='$shipping_id', user_id='$userid', shipping_method_id='$shipping_method', shipping_details='$shipping_address', payment_method_id='$payment_method', payment_method='paypal', currency='".SITE_CURRENCY_CODE."', shipping_total='$shipping_total', total='$order_total', status='pending', created=NOW()");
					$order_id = mysql_insert_id();

					if ($total > 0)
					{
						while ($row = mysql_fetch_array($result))
						{
							$item_id		= (int)$row['item_id'];
							$title			= mysql_real_escape_string($row['title']);
							$quantity		= mysql_real_escape_string($_SESSION['quantity'][$item_id]);
							$item_price		= mysql_real_escape_string($row['price']);

							smart_mysql_query("INSERT INTO abbijan_order_items SET order_id='".(int)$order_id."', user_id='$userid', item_id='$item_id', item_title='$title', item_quantity='$quantity', item_price='$item_price'");
						}
					}

					header ("Location: /paypal_redirect.php");
					exit();

				}
				elseif ($payment_method == 2) // credit card
				{
					// if Paypal
					if (CC_GATEWAY == "paypal")
					{
						require_once("inc/payments/paypal.inc.php");

						$paymentType		= urlencode('Sale');
						$firstName			= urlencode($cc_fname);
						$lastName			= urlencode($cc_lname);
						$creditCardType		= urlencode($cc_type);
						$creditCardNumber	= urlencode($cc_number);
						$padDateMonth		= urlencode(str_pad($cc_month, 2, '0', STR_PAD_LEFT));
						$expDateYear		= urlencode($cc_year);
						$cvv2Number			= urlencode($cc_cvv);
						$address1			= urlencode($billing_address);
						$address2			= urlencode($billing_address2);
						$city				= urlencode($billing_city);
						$state				= urlencode($billing_state);
						$zip				= urlencode($billing_zip);
						$country			= urlencode($billing_country);		// US or other valid country code
						$amount				= urlencode($order_total);
						$currencyID			= urlencode(SITE_CURRENCY_CODE);	// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

						// add request-specific fields to the request string
						$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
									"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
									"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";

						// execute the API operation
						$httpParsedResponseAr = PPHttpPost('DoDirectPayment', $nvpStr);

						// if successfull payment
						if ("Success" == $httpParsedResponseAr["ACK"])
						{
							$success			= 1; // required
							$payment_method		= "credit card";
							$payment_details	= $httpParsedResponseAr["TRANSACTIONID"];
							$order_status		= "complete";
							
						}
						else
						{
							$payment_method		= "credit card";
							$payment_details	= $httpParsedResponseAr["CORRELATIONID"];
							$decline_reason		= mysql_real_escape_string(urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]));
							$order_status		= "declined";
						}
					}
					// if Authorize.net
					else if (CC_GATEWAY == "authorizenet")
					{
						require_once("inc/payments/authorizenet.inc.php");
					}
					// other payment gateway
					else if (CC_GATEWAY == "other")
					{
						// procceed credit cart with other payment gateway
						// add your code here
					}


					// if payment was successful
					if ($success == 1)
					{
						$oresult = smart_mysql_query("INSERT INTO abbijan_orders SET reference_id='$reference_id', shipping_id='$shipping_id', user_id='$userid', shipping_method_id='$shipping_method', shipping_details='$shipping_address', payment_method_id='$payment_method', payment_method='credit card', payment_details='$payment_details', shipping_total='$shipping_total', total='$order_total', status='$order_status', created=NOW()");
						$order_id = mysql_insert_id();

						if ($total > 0)
						{
							while ($row = mysql_fetch_array($result))
							{
								$item_id		= (int)$row['item_id'];
								$title			= mysql_real_escape_string($row['title']);
								$quantity		= mysql_real_escape_string($_SESSION['quantity'][$item_id]);
								$item_price		= mysql_real_escape_string($row['price']);

								smart_mysql_query("INSERT INTO abbijan_order_items SET order_id='".(int)$order_id."', user_id='$userid', item_id='$item_id', item_title='$title', item_quantity='$quantity', item_price='$item_price'");
							}
						}

						// send order receipt
						SendReceipt($order_id);

						unset($_SESSION['cart_items'], $_SESSION['quantity'], $_SESSION['ShippingPrice'], $_SESSION['Total']);
						
						header ("Location: /payment_success.php");
						exit();				
					}
					else
					{
						smart_mysql_query("INSERT INTO abbijan_orders SET reference_id='$reference_id', shipping_id='$shipping_id', user_id='$userid', shipping_method_id='$shipping_method', shipping_details='$shipping_address', payment_method_id='$payment_method', payment_method='$payment_method_name', payment_details='$payment_details', total='$order_total', status='$order_status', reason='$decline_reason', created=NOW()");

						header ("Location: /payment_cancelled.php");
						exit;
					}

				}
				else
				{
					$oresult = smart_mysql_query("INSERT INTO abbijan_orders SET reference_id='$reference_id', shipping_id='$shipping_id', user_id='$userid', shipping_method_id='$shipping_method', shipping_details='$shipping_address', payment_method_id='$payment_method', payment_method='$payment_method_name', currency='".SITE_CURRENCY_CODE."', shipping_total='$shipping_total', total='$order_total', status='complete', created=NOW()");
					$order_id = mysql_insert_id();

					if ($total > 0)
					{
						while ($row = mysql_fetch_array($result))
						{
							$item_id		= (int)$row['item_id'];
							$title			= mysql_real_escape_string($row['title']);
							$quantity		= mysql_real_escape_string($_SESSION['quantity'][$item_id]);
							$item_price		= mysql_real_escape_string($row['price']);

							smart_mysql_query("INSERT INTO abbijan_order_items SET order_id='".(int)$order_id."', user_id='$userid', item_id='$item_id', item_title='$title', item_quantity='$quantity', item_price='$item_price'");
						}
					}

					// send order receipt
					SendReceipt($order_id);

					unset($_SESSION['cart_items'], $_SESSION['quantity'], $_SESSION['ShippingPrice'], $_SESSION['Total']);
						
					header ("Location: /payment_success.php");
					exit();	
				}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}

	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Checkout";

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
		<td class="first active" valign="top">
			<span class="number">4 </span> <span class="title">Checkout</span>
		</td>
		<td class=" next" valign="top">
			<span class="number">5 </span> <span class="title">Complete</span>
		</td>
	</tr>
	</table>


	<h1>Checkout</h1>


	<?php if (isset($allerrors) && $allerrors != "") { ?>
		<div style="width: 94%" class="error_msg"><?php echo $allerrors; ?></div>
	<?php } ?>

	<div align="right"><span class="req">* denotes required field</span></div>

	<form action="" method="post" id="form1" name="form1">

	<div style="float: left; width: 45%; min-height: 350px;">

	<center><h2>1. Shipping Method</h2></center>
	<?php
			$d_query = "SELECT * FROM abbijan_shipping_methods WHERE (countries LIKE '%$country%' OR countries='all') AND status='active'";
			$d_result = smart_mysql_query($d_query);
			$d_total = mysql_num_rows($d_result);

			if ($d_total > 0) {
	?>
			<?php while ($d_row = mysql_fetch_array($d_result)) { ?>
				<input type="radio" name="shipping_method" value="<?php echo $d_row['shipping_method_id']; ?>" <?php if ($shipping_method == $d_row['shipping_method_id']) echo 'checked="checked"'; ?>/>
				<b><?php echo $d_row['title']; ?></b> (+ <?php echo DisplayPrice($d_row['cost']); ?>)
				<?php if ($d_row['delivery_time'] != "") { ?> <sup><?php echo $d_row['delivery_time']; ?></sup><br/><?php } ?>
				<?php if ($d_row['desciption'] != "") { ?><?php echo $d_row['desciption']; ?><br/><?php } ?>
			<?php } ?>
	<?php	}else{ ?>
			<p>Sorry, no available shipping methods for your country.</p>
	<?php	} ?>


	<center><h2>2. Shipping Address</h2></center>
	<?php
			$s_query = "SELECT * FROM abbijan_shipping WHERE user_id='$userid'";
			$s_result = smart_mysql_query($s_query);
			$s_total = mysql_num_rows($s_result);

			if ($s_total > 0)
			{
	?>
		<div style="width: 100%; text-align: center; background: #F7F7F7; border-radius: 7px; padding: 15px 10px;">
			<select name="shipping_method_id" id="shipping_method_id" style="width: 240px;" onchange="javascript:hiddenDiv()">
				<option value="">--- Select shipping address ---</option>
				<?php while ($s_row = mysql_fetch_array($s_result)) { ?>
					<option value="<?php echo $s_row['shipping_id']; ?>" <?php if ($shipping_method_id == $s_row['shipping_id']) echo 'selected="selected"'; ?>><?php echo $s_row['shipping_name']." : ".$s_row['fname']." ".$s_row['lname']." (".$s_row['address'].", ".$s_row['city'].", ".$s_row['state'].", ".$s_row['zip'].")"; ?></option>
				<?php } ?>
			</select>
		</div>
		<div id="new_shipping_box" <?php if (isset($shipping_method_id) && $shipping_method_id > 0) { ?>style="display: none;"<?php } ?>>
		<p align="center"><b>- OR -</b></p>
		<p align="center"><b>New Shipping Address</b></p>
	<?php } ?>

		<script type="text/javascript">
		<!--
			function hiddenDiv(){
				if(document.getElementById("shipping_method_id").value != ""){
					document.getElementById("new_shipping_box").style.display = "none";
				}else{
					document.getElementById("new_shipping_box").style.display = "";
				}
			}
		-->
		</script>
		<table width="350" cellpadding="3" cellspacing="0" border="0">
		<tr>
            <td width="90" align="right" valign="middle"><span class="req">* </span>First Name:</td>
			<td align="left" valign="top"><input type="text" id="fname" name="fname" size="25" value="<?php echo getPostParameter('fname'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Last Name:</td>
			<td align="left" valign="top"><input type="text" id="lname" name="lname" size="25" value="<?php echo getPostParameter('lname'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Address:</td>
			<td align="left" valign="top"><input type="text" id="address" name="address" size="25" value="<?php echo getPostParameter('address'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle">Address Line 2:</td>
			<td align="left" valign="top"><input type="text" id="address2" name="address2" size="25" value="<?php echo getPostParameter('address2'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Country:</td>
			<td align="left" valign="top">
				<select name="country" class="textbox2" id="country" style="width: 170px;" onChange="document.form1.submit()">
					<option value="">--- Select country ---</option>
					<?php
						$sql_country = "SELECT * FROM abbijan_countries ORDER BY name ASC";
						$rs_country = smart_mysql_query($sql_country);
						$total_country = mysql_num_rows($rs_country);

						if ($total_country > 0)
						{
							while ($row_country = mysql_fetch_array($rs_country))
							{
								if ($country == $row_country['name'])
									echo "<option value='".$row_country['name']."' selected>".$row_country['name']."</option>\n";
								else
									echo "<option value='".$row_country['name']."'>".$row_country['name']."</option>\n";
							}
						}
					?>
				</select>			
			</td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>City:</td>
			<td align="left" valign="top"><input type="text" id="city" name="city" size="25" value="<?php echo getPostParameter('city'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>State:</td>
			<td align="left" valign="top"><input type="text" id="state" name="state" size="25" value="<?php echo getPostParameter('state'); ?>" class="textbox" />
			</td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Zip Code:</td>
			<td align="left" valign="top"><input type="text" id="zip" name="zip" size="25" value="<?php echo getPostParameter('zip'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Phone:</td>
			<td align="left" valign="top"><input type="text" id="phone" name="phone" size="25" value="<?php echo getPostParameter('phone'); ?>" class="textbox" /></td>
		</tr>
		</table>
	</div>
	</div>

	<div style="float: right; width: 48%; text-align: center;">

		<script type="text/javascript">
		<!--
			function onSelect(objSelect) {
				if (objSelect.value == "2") {
					document.getElementById("cc_info").style.display = "";
				}else{
					document.getElementById("cc_info").style.display = "none";
				}
			}
		-->
		</script>

	
	<h2>3. Payment Method</h2>

	<?php
			$pmethods_query = "SELECT * FROM abbijan_payment_methods WHERE status='active'";
			$pmethods_result = smart_mysql_query($pmethods_query);
			$pmethods_total = mysql_num_rows($pmethods_result);

			if ($pmethods_total > 0) {
	?>
			<div style="width: 250px; text-align: left; border-radius: 7px; padding: 5px 5px 5px 50px; margin: 0 auto;">
			<?php while ($pmethods_row = mysql_fetch_array($pmethods_result)) { ?>
				<?php if ($pmethods_row['payment_method_id'] == 3 && $pmethods_row['status'] == "active") { ?>
					<?php if (GetUserBalance($userid, $hide_currency = 1) > 0) { ?>
						<img src="/images/icon_wallet.png" align="absmiddle" />
						<input type="radio" name="payment_method" value="<?php echo $pmethods_row['payment_method_id']; ?>" <?php if (@$payment_method == $pmethods_row['payment_method_id']) echo 'checked="checked"'; ?>/>
						<b><?php echo $pmethods_row['title']; ?></b> &nbsp; (<?php echo GetUserBalance($userid); ?>)<br/>
					<?php } ?>
				<?php }else{ ?>
					<?php if ($pmethods_row['pmethod_image'] != "") { ?><img src="/images/<?php echo $pmethods_row['pmethod_image']; ?>" align="absmiddle" /><?php } ?>
					<input type="radio" name="payment_method" id="payment_method" value="<?php echo $pmethods_row['payment_method_id']; ?>" onClick="onSelect(this)" <?php if (@$payment_method == $pmethods_row['payment_method_id']) echo 'checked="checked"'; ?>/> <b><?php echo $pmethods_row['title']; ?></b><br/>
				<?php } ?>
			<?php } ?>
			</div>
	<?php	}else{ ?>
			<p>Sorry, no available payment methods. Please check back soon.</p>
	<?php	} ?>


	<div id="cc_info" <?php if (@$payment_method != 2) { ?>style="display: none;"<?php } ?>>
	<center><h2>Credit Card Information</h2></center>
	<table width="350" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
            <td width="90" align="right" valign="middle"><span class="req">* </span>First Name:</td>
			<td align="left" valign="top"><input type="text" id="cc_fname" name="cc_fname" size="25" value="<?php echo getPostParameter('cc_fname'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td width="90" align="right" valign="middle"><span class="req">* </span>Last Name:</td>
			<td align="left" valign="top"><input type="text" id="cc_lname" name="cc_lname" size="25" value="<?php echo getPostParameter('cc_lname'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Card Type:</td>
			<td align="left" valign="top">
				<select name="cc_type" class="textbox2" id="cc_type" >
					<option value="">--- Please select ---</option>
					<option value="Visa" <?php if ($cc_type == "Visa") echo "selected='selected'"; ?>>Visa</option>
					<option value="MasterCard" <?php if ($cc_type == "MasterCard") "selected='selected'"; ?>>Master Card</option>
					<option value="Discover" <?php if ($cc_type == "Discover") "selected='selected'"; ?>>Discover</option>
					<option value="Amex" <?php if ($cc_type == "Amex") echo "selected='selected'"; ?>>American Express</option>
				</select>			
			</td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Card Number:</td>
			<td align="left" valign="top"><input type="text" id="cc_number" name="cc_number" size="25" value="<?php echo getPostParameter('cc_number'); ?>" class="textbox" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Expiry Date:</td>
			<td align="left" valign="top">
				<select id="cc_month" name="cc_month">
					<option value="01" <?php if ($cc_month == "01") echo "selected='selected'"; ?>>01</option>
					<option value="02" <?php if ($cc_month == "02") echo "selected='selected'"; ?>>02</option>
					<option value="03" <?php if ($cc_month == "03") echo "selected='selected'"; ?>>03</option>
					<option value="04" <?php if ($cc_month == "04") echo "selected='selected'"; ?>>04</option>
					<option value="05" <?php if ($cc_month == "05") echo "selected='selected'"; ?>>05</option>
					<option value="06" <?php if ($cc_month == "06") echo "selected='selected'"; ?>>06</option>
					<option value="07" <?php if ($cc_month == "07") echo "selected='selected'"; ?>>07</option>
					<option value="08" <?php if ($cc_month == "08") echo "selected='selected'"; ?>>08</option>
					<option value="09" <?php if ($cc_month == "09") echo "selected='selected'"; ?>>09</option>
					<option value="10" <?php if ($cc_month == "10") echo "selected='selected'"; ?>>10</option>
					<option value="11" <?php if ($cc_month == "11") echo "selected='selected'"; ?>>11</option>
					<option value="12" <?php if ($cc_month == "12") echo "selected='selected'"; ?>>12</option>
				</select>
				/
				<select id="cc_year" name="cc_year">
				<?php
						$current_year = date("Y");
						$till_year = $current_year+15;

						for ($year = $current_year; $year <= $till_year; $year++)
						{
							if ($cc_year == $year)
								echo "<option value='$year' selected='selected'>$year</option>";
							else
								echo "<option value='$year'>$year</option>";
						}
				?>
				</select>				
			</td>
		</tr>
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>CVV Number:</td>
			<td align="left" valign="top"><input type="text" id="cc_cvv" name="cc_cvv" size="5" value="<?php echo getPostParameter('cc_cvv'); ?>" class="textbox" /> <a id="cvv_link" href="/about_cvv.php" class="fancy"><img src="/images/icon_question.png" /></a></td>
		</tr>
	</table>


	<h2>Billing Address</h2>

	<table width="350" align="center" cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td width="90" align="right" valign="middle"><span class="req">* </span>Address:</td>
		<td align="left" valign="top"><input type="text" id="billing_address" name="billing_address" size="25" value="<?php echo getPostParameter('billing_address'); ?>" class="textbox" /></td>
	</tr>
	<tr>
		<td align="right" valign="middle">Address Line 2:</td>
		<td align="left" valign="top"><input type="text" id="billing_address2" name="billing_address2" size="25" value="<?php echo getPostParameter('billing_address2'); ?>" class="textbox" /></td>
	</tr>
	<tr>
		<td align="right" valign="middle"><span class="req">* </span>Country:</td>
		<td align="left" valign="top">
				<select name="billing_country" class="textbox2" id="billing_country" style="width: 170px;">
					<option value="">--- Select country ---</option>
					<?php
						$sql_country = "SELECT * FROM abbijan_countries ORDER BY name ASC";
						$rs_country = smart_mysql_query($sql_country);
						$total_country = mysql_num_rows($rs_country);

						if ($total_country > 0)
						{
							while ($row_country = mysql_fetch_array($rs_country))
							{
								if ($row_country['code'] === $billing_country)
									echo "<option value='".$row_country['code']."' selected>".$row_country['name']."</option>\n";
								else
									echo "<option value='".$row_country['code']."'>".$row_country['name']."</option>\n";
							}
						}
					?>
				</select>			
		</td>
	</tr>
	<tr>
		<td align="right" valign="middle"><span class="req">* </span>City:</td>
		<td align="left" valign="top"><input type="text" id="billing_city" name="billing_city" size="25" value="<?php echo getPostParameter('billing_city'); ?>" class="textbox" /></td>
	</tr>
	<tr>
		<td align="right" valign="middle"><span class="req">* </span>State:</td>
		<td align="left" valign="top"><input type="text" id="billing_state" name="billing_state" size="25" value="<?php echo getPostParameter('billing_state'); ?>" class="textbox" /></td>
	</tr>
	<tr>
		<td align="right" valign="middle"><span class="req">* </span>Zip Code:</td>
		<td align="left" valign="top"><input type="text" id="billing_zip" name="billing_zip" size="25" value="<?php echo getPostParameter('billing_zip'); ?>" class="textbox" /></td>
	</tr>
	<tr>
		<td align="right" valign="middle"><span class="req">* </span>Phone:</td>
		<td align="left" valign="top"><input type="text" id="billing_phone" name="billing_phone" size="25" value="<?php echo getPostParameter('billing_phone'); ?>" class="textbox" /></td>
	</tr>
	</table>
	</div>

	</div>
	<div style="clear: both;"></div>

		<div style="margin-top: 10px; border-top: 3px solid #F7F7F7; padding: 5px 0;">
		<table width="100%" align="center" border="0" cellspacing="3" cellpadding="3">
		<tr>
			<td width="50%" valign="top" align="left">
				<input type="button" name="cancel" class="cancel" value="&#171; Back to Cart" onClick="javascript:document.location.href='/cart.php'" />
			</td>
			<td width="50%" valign="top" align="right">
				<input type="hidden" name="action" value="checkout" />
				<input type="submit" class="checkout" name="proceed" value="Place Order!" />
			</td>
		</tr>
		</table>
		</div>

	</form>


<?php require_once ("inc/footer.inc.php"); ?>