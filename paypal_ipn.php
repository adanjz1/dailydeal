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


	// for test: www.sandbox.paypal.com
	// for live: www.paypal.com


	// PayPal settings
	$paypal_email	= PAYPAL_ACCOUNT;
	$order_id		= (int)$_POST['custom'];
	$return_url		= SITE_URL.'payment_success.php';
	$cancel_url		= SITE_URL.'payment_cancelled.php';
	$notify_url		= SITE_URL.'paypal_ipn.php';
	
	$amount			= (float)$_POST['amount'];
	$item_name		= SITE_TITLE." Cart Purchase";


	// clear shopping cart
	unset($_SESSION['cart_items'], $_SESSION['quantity'], $_SESSION['ShippingPrice'], $_SESSION['Total']);


	###  Paypal request ###
	###########################################################################################
	if (isset($_POST['action']) && $_POST['action'] == "checkout" && !isset($_POST["txn_id"]) && !isset($_POST["txn_type"]))
	{
		// Firstly Append paypal account to querystring
		$querystring .= "?business=".urlencode($paypal_email)."&";	

		//The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
		$querystring .= "item_name=".urlencode($item_name)."&";
		//$querystring .= "amount=".urlencode($amount)."&";
		$querystring .= "currency_code=".urlencode(SITE_CURRENCY_CODE)."&";
	
		//loop for posted values and append to querystring
		foreach($_POST as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$querystring .= "$key=$value&";
		}
	
		// Append paypal return addresses
		$querystring .= "return=".urlencode(stripslashes($return_url))."&";
		$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
		$querystring .= "notify_url=".urlencode($notify_url);
	
		// Append querystring with custom field
		$querystring .= "&custom=".$order_id;
	
		// Redirect to paypal IPN
		header('Location: https://www.paypal.com/cgi-bin/webscr'.$querystring);
		exit();
	}


	###  Paypal response  ###
	###########################################################################################
	if (isset($_POST["txn_id"]) && isset($_POST["txn_type"]))
	{
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value); // IPN fix
			$req .= "&$key=$value";
		}
	
		// assign posted variables to local variables
		$data['txn_id']				= $_POST['txn_id'];
		$data['business']			= $_POST['business'];
		$data['item_name']			= $_POST['item_name'];
		$data['item_number'] 		= $_POST['item_number'];
		$data['payment_amount'] 	= (float)getPostParameter('mc_gross');
		$data['payment_currency']	= mysql_real_escape_string(getPostParameter('mc_currency'));
		$data['payment_status'] 	= $_POST['payment_status'];
		$data['receiver_email'] 	= $_POST['receiver_email'];
		$data['payer_email'] 		= $_POST['payer_email'];
		$data['custom'] 			= (int)getPostParameter('custom');
		
		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	
		$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);	
	
		if (!$fp)
		{
			// HTTP ERROR
			header ("Location: /payment_cancelled.php");
			exit();
		}
		else
		{
			fputs ($fp, $header . $req);

			while (!feof($fp))
			{
				$res = fgets ($fp, 1024);

				if (strcmp($res, "VERIFIED") == 0)
				{
					// check that txn_id has not been previously processed
					// check that payment_amount/payment_currency are correct

					$order_id			= $data['custom'];
					$order_total		= $data['payment_amount'];
					$order_currency		= $data['payment_currency']; 

					$check_result = smart_mysql_query("SELECT * FROM abbijan_orders WHERE order_id='".$order_id."' AND payment_method='paypal' AND currency='".$order_currency."' AND total='".$order_total."' AND status='pending' LIMIT 1");
							
					// PAYMENT VALIDATED & VERIFIED!
					if (mysql_num_rows($check_result) != 0 && $data['receiver_email'] == PAYPAL_ACCOUNT)
					{
						// confirm transaction
						$payment_query = "UPDATE abbijan_orders SET status='complete' WHERE order_id='".$order_id."' AND status='pending' LIMIT 1";
						
						// payment has been made
						if (smart_mysql_query($payment_query))
						{
							// send order receipt
							SendReceipt($order_id);

							$_SESSION['order_id'] = $order_id;

							header ("Location: /payment_success.php");
							exit();
						}
						else
						{
							// Error inserting into DB
							//@mail(SITE_MAIL, "PAYPAL - Error inserting into Database", "Error inserting into DB<br />data = <pre>".print_r($_POST, true)."</pre>");
						}
					}
					else
					{					
						// Fake payment
						header ("Location: /payment_cancelled.php");
						exit();
					}
				}
				elseif (strcmp ($res, "INVALID") == 0)
				{
					// PAYMENT INVALID & INVESTIGATE MANUALY!
					//@mail(SITE_MAIL, "PAYPAL DEBUGGING", "Invalid Response<br />data = <pre>".print_r($_POST, true)."</pre>");
					header ("Location: /payment_cancelled.php");
					exit();
				}
			}

			fclose ($fp);
		}
	}

?>