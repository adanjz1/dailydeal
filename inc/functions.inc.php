<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/


/**
 * Runs mysql query
 * @param	$sql		mysql query to run
 * @return	boolean		false if failed mysql query
*/

function smart_mysql_query($sql)
{
	$res = mysql_query($sql) or die("<p align='center'><span style='font-size:11px; font-family: tahoma; color: red;'>query failed: ".mysql_error()."</span></p>");
	if(!$res){
		return false;
	}
	return $res;
}



/**
 * Retrieves parameter from POST array
 * @param	$name	parameter name
*/

function getPostParameter($name)
{
	$data = isset($_POST[$name]) ? $_POST[$name] : null;
	if(!is_null($data) && get_magic_quotes_gpc() && is_string($data))
	{
		$data = stripslashes($data);
	}
	$data = trim($data);
	$data = htmlentities($data, ENT_QUOTES, 'UTF-8');
	return $data;
}



/**
 * Retrieves parameter from GET array
 * @param	$name	parameter name
*/

function getGetParameter($name)
{
	return isset($_GET[$name]) ? $_GET[$name] : false;
}



/**
 * Tries to find parameter in GET array, if it is not set
 * tries to return from post array
 * @param	$name	Pararmeter name
 */

function getGetOrPostParameter($name){
	if($value = getGetParameter($name)){
		return $value;
	}else{
		return getPostParameter($name);
	}
}



/** 
 * Retrieves parameter from SESSION array
 * @param	$name	Parameter name
 */

function getSessionParameter($name){
	return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
}



/**
 * Returns random password
 * @param	$length		length of string
 * @return	string		random password
*/

if (!function_exists('generatePassword')) {
	function generatePassword($length = 9)
	{
		$password = "";
		$possible = "0123456789abcdefghijkmnpqrstvwxyzABCDEFGHJKLMNPQRTVWXYZ!()";
		$i = 0; 

		while ($i < $length)
		{ 
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

			if (!strstr($password, $char))
			{ 
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
}



/**
 * Returns random key
 * @param	$text		string
 * @return	string		random key for user verification
*/

if (!function_exists('GenerateKey')) {
	function GenerateKey($text)
	{
		$text = preg_replace("/[^0-9a-zA-Z]/", " ", $text);
		$text = substr(trim($text), 0, 50);
		$key = md5(time().$text.mt_rand(1000,9999));
		return $key;
	}
}



/**
 * Returns savings percentage
 * @param	$original_price		original price
 * @param	$discount_price		discount price
 * @return	string				savings percentage
*/

function CalculateSavingsPercentage($original_price, $discount_price)
{
	$savings = $original_price - $discount_price;
	$savings_percentage = round(($savings/$original_price)*100, 0);
	return $savings_percentage."%";
}



/**
 * Calculate percentage
 * @param	$amount				Amount
 * @param	$percent			Percent value
 * @return	string				amoutn percentage
*/

if (!function_exists('CalculatePercentage')) {
	function CalculatePercentage($amount, $percent)
	{
		return number_format(($amount/100)*$percent,2,'.','');
	}
}


/**
 * Returns  member's current balance
 * @param	$userid					User ID
 * @param	$hide_currency_option	Hide or show currency sign
 * @return	string					member's current balance
*/

if (!function_exists('GetUserBalance')) {
	function GetUserBalance($userid, $hide_currency_option = 0)
	{
		$query = "SELECT balance FROM abbijan_users WHERE user_id='".(int)$userid."' LIMIT 1";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) != 0)
		{
			$row = mysql_fetch_array($result);

			$orders_total = mysql_fetch_array(smart_mysql_query("SELECT SUM(total) AS orders_total FROM abbijan_orders WHERE user_id='".(int)$userid."' AND payment_method_id='3' AND (status!='refunded' AND status!='declined')"));

			$balance = $row['balance'] - $orders_total['orders_total'];

			if ($balance > 0)
			{
				return DisplayMoney($balance, $hide_currency_option);
			}
			else
			{
				return DisplayMoney(0, $hide_currency_option);
			}
		}
		else
		{
			return DisplayMoney(0, $hide_currecy_option);
		}
	}
}


/**
 * Returns formatted money value
 * @param	$amount				Amount
 * @param	$hide_currency		Hide or Show currency sign
 * @return	string				returns formatted money value
*/

if (!function_exists('DisplayMoney')) {
	function DisplayMoney($amount, $hide_currency = 0)
	{
		$newamount = number_format($amount, 2, '.', '');

		if ($hide_currency == 1)
		{
			return $newamount;
		}
		else
		{
			switch (SITE_CURRENCY_FORMAT)
			{
				case "1": $newamount = SITE_CURRENCY.$newamount; break;
				case "2": $newamount = $newamount.SITE_CURRENCY; break;
				default: $newamount = SITE_CURRENCY.$newamount; break;
			}

			return $newamount;
		}
	}
}



/**
 * Returns formatted money value
 * @param	$amount				Amount
 * @param	$hide_currency		Hide or Show currency sign
 * @return	string				returns formatted money value
*/

if (!function_exists('DisplayPrice')) {
	function DisplayPrice($amount, $hide_currency = 0)
	{
		$newamount = number_format($amount, 2, '.', '');
		
		if ($hide_currency == 1)
		{
			return $newamount;
		}
		else
		{
			$cents = substr($newamount, -2);

			if ($cents == "00")
				$newamount = substr($newamount, 0, -3);

			switch (SITE_CURRENCY_FORMAT)
			{
				case "1": $newamount = SITE_CURRENCY.$newamount; break;
				case "2": $newamount = $newamount.SITE_CURRENCY; break;
				default: $newamount = SITE_CURRENCY.$newamount; break;
			}

			return $newamount;
		}
	}
}



/**
 * Returns relative date
 * @param	$time			time
 * @return	string			returns relative date
*/

if (!function_exists('relative_date')) {
	function relative_date($time)
	{
		define("SECOND", 1);
		define("MINUTE", 60 * SECOND);
		define("HOUR", 60 * MINUTE);
		define("DAY", 24 * HOUR);
		define("MONTH", 30 * DAY);

		$delta = time() - $time;

		if ($delta < 1 * MINUTE)
		{
			return $delta == 1 ? "one second ago" : $delta . " seconds ago";
		}
		if ($delta < 2 * MINUTE)
		{
			return "a minute ago";
		}
		if ($delta < 45 * MINUTE)
		{
			return floor($delta / MINUTE) . " minutes ago";
		}
		if ($delta < 90 * MINUTE)
		{
			return "an hour ago";
		}
		if ($delta < 24 * HOUR)
		{
			return floor($delta / HOUR) . " hours ago";
		}
		if ($delta < 48 * HOUR)
		{
			return "yesterday";
		}
		if ($delta < 30 * DAY)
		{
			return floor($delta / DAY) . " days ago";
		}
		if ($delta < 12 * MONTH)
		{
			$months = floor($delta / DAY / 30);
			return $months <= 1 ? "one month ago" : $months . " months ago";
		}
		else
		{
			$years = floor($delta / DAY / 365);
			return $years <= 1 ? "one year ago" : $years . " years ago";
		}
	}
}



/**
 * Returns time left
 * @return	string	time left
*/

if (!function_exists('GetTimeLeft')) {
	function GetTimeLeft($time_left)
	{
		$days		= floor($time_left / (60 * 60 * 24));
		$remainder	= $time_left % (60 * 60 * 24);
		$hours		= floor($remainder / (60 * 60));
		$remainder	= $remainder % (60 * 60);
		$minutes	= floor($remainder / 60);
		$seconds	= $remainder % 60;

		$days == 1 ? $dw = "day" : $dw = "days";
		$hours == 1 ? $hw = "hr" : $hw = "hrs";
		$minutes == 1 ? $mw = "min" : $mw = "mins";
		$seconds == 1 ? $sw = "second" : $sw = "seconds";

		if ($time_left > 0)
		{
			//$new_time_left = $days." $dw ".$hours." $hw ".$minutes." $mw";
			$new_time_left = $days." $dw ".$hours." $hw";
			return $new_time_left;
		}
		else
		{
			return "<span class='expired'>expired</span>";
		}
	}
}



/**
 * Returns random string
 * @param	$len	string length
 * @param	$chars	chars in the string
 * @return	string	random string
*/

if (!function_exists('GenerateRandString')) {
	function GenerateRandString($len, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
	{
		$string = '';
		for ($i = 0; $i < $len; $i++)
		{
			$pos = rand(0, strlen($chars)-1);
			$string .= $chars{$pos};
		}
		return $string;
	}
}



/**
 * Returns random order's reference ID
 * @return	string	Reference ID
*/

if (!function_exists('GenerateReferenceID')) {
	function GenerateReferenceID()
	{
		unset($num);

		$num = GenerateRandString(8,"0123456789");
    
		$check = smart_mysql_query("SELECT * FROM abbijan_orders WHERE reference_id='$num'");
    
		if (mysql_num_rows($check) == 0)
		{
			return $num;
		}
		else
		{
			return GenerateOrderID();
		}
	}
}



/**
 * Returns Encrypted password
 * @param	$password	password
 * @return	string		encrypted password
*/

if (!function_exists('PasswordEncryption')) {
	function PasswordEncryption($password)
	{
		return md5(sha1($password));
	}
}



/**
 * Returns setting value by setting's key
 * @param	$setting_key	Setting's Key
 * @return	string	setting's value
*/

if (!function_exists('GetSetting')) {
	function GetSetting($setting_key)
	{
		$setting_result = smart_mysql_query("SELECT setting_value FROM abbijan_settings WHERE setting_key='".$setting_key."'");
		$setting_total = mysql_num_rows($setting_result);

		if ($setting_total > 0)
		{
			$setting_row = mysql_fetch_array($setting_result);
			$setting_value = $setting_row['setting_value'];
		}
		else
		{
			die ("settings not found");
		}

		return $setting_value;
	}
}



/**
 * Returns content for static pages
 * @param	$content_name	Content's Name or Content ID
 * @return	array	(1) - Page Title, (2) - Page Text
*/

if (!function_exists('GetContent')) {
	function GetContent($content_name)
	{
		if (is_numeric($content_name))
		{
			$content_id = (int)$content_name;
			$content_result = smart_mysql_query("SELECT * FROM abbijan_content WHERE content_id='".$content_id."' LIMIT 1");
		}
		else
		{
			$content_result = smart_mysql_query("SELECT * FROM abbijan_content WHERE name='".$content_name."' LIMIT 1");
		}

		$content_total = mysql_num_rows($content_result);

		if ($content_total > 0)
		{
			$content_row = mysql_fetch_array($content_result);
			$contents['title'] = stripslashes($content_row['title']);
			$contents['text'] = stripslashes($content_row['description']);
		}
		else
		{
			$contents['title'] = "Page not found";
			$contents['text'] = "<p align='center'>Sorry, page not found.</p>";
			$contents['text'] .= "<p align='center'><a class='goback' href='/'>Go back to the home page</a></p>";
		}

		return $contents;
	}
}



/**
 * Returns content for email template
 * @param	$email_name	Email Template Name
 * @return	array	(1) - Email Subject, (2) - Email Message
*/

if (!function_exists('GetEmailTemplate')) {
	function GetEmailTemplate($email_name)
	{
		$etemplate_result = smart_mysql_query("SELECT * FROM abbijan_email_templates WHERE email_name='".$email_name."' LIMIT 1");
		$etemplate_total = mysql_num_rows($etemplate_result);

		if ($etemplate_total > 0)
		{
			$etemplate_row = mysql_fetch_array($etemplate_result);
			$etemplate['email_subject'] = stripslashes($etemplate_row['email_subject']);
			$etemplate['email_message'] = stripslashes($etemplate_row['email_message']);

			$etemplate['email_message'] = "<html>
								<head>
									<title>".$etemplate['email_subject']."</title>
								</head>
								<body>
								<table width='80%' border='0' cellpadding='10'>
								<tr>
									<td align='left' valign='top'>".$etemplate['email_message']."</td>
								</tr>
								</table>
								</body>
							</html>";
		}

		return $etemplate;
	}
}


/**
 * Returns sub categories list
 * @param	$cat_id		Primary Category ID
 * @return	array		sub categories list 
*/

if (!function_exists('GetSubCategories')) {
	function GetSubCategories ($cat_id)
	{
		static $sub_categories;

		$result = smart_mysql_query("SELECT category_id FROM abbijan_categories WHERE parent_id='".(int)$cat_id."'");
	
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				$sub_categories[] = $row['category_id'];
				GetSubCategories($row['category_id']);
			}

			return $sub_categories;
		}
	}
}



/**
 * Returns deal's categories
 * @param	$item_id	Deal ID
 * @return	string		deal's categories list
*/

if (!function_exists('GetDealCategory')) {
	function GetDealCategory($item_id)
	{
		$sql_item_categories = smart_mysql_query("SELECT cc.*, c.name FROM abbijan_item_to_category cc, abbijan_categories c WHERE cc.category_id=c.category_id AND cc.item_id='".(int)$item_id."' ORDER BY c.name");
		
		if (mysql_num_rows($sql_item_categories) > 0)
		{
			$item_categories = "";

			while ($row_item_categories = mysql_fetch_array($sql_item_categories))
			{
				$item_categories .= "<a href='/deals.php?cat=".$row_item_categories['category_id']."'>".$row_item_categories['name']."</a> ";
			}

			return $item_categories;
		}
	}
}



/**
 * Returns country name
 * @param	$country_id		Country ID
 * @return	string			country name
*/

if (!function_exists('GetCountry')) {
	function GetCountry($country_id)
	{
		$result = smart_mysql_query("SELECT * FROM abbijan_countries WHERE country_id='".(int)$country_id."'");

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$country_name = $row['name'];
			return $country_name;
		}
	}
}


/**
 * Returns members savings total
 * @return	string	members savings total
*/

if (!function_exists('GetSavingsTotal')) {
	function GetSavingsTotal()
	{
		$total = 0; 

		$result = smart_mysql_query("SELECT * FROM abbijan_items WHERE start_date<NOW()");

		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				if ($row['discount'] > 0)
				{
					if ($row['deal_type'] == "affiliate")
						$subtotal = $row['visits']*$row['discount'];
					else
						$subtotal = GetDealSalesTotal($row['item_id'])*$row['discount'];

					$total += $subtotal;
				}
			}

			switch (SITE_CURRENCY_FORMAT)
			{
				case "1": $total = SITE_CURRENCY.number_format($total); break;
				case "2": $total = number_format($total).SITE_CURRENCY; break;
				default: $total = SITE_CURRENCY.number_format($total); break;
			}

			return $total;
		}
		else
		{
			return DisplayMoney("0"); break;
		}
	}
}



/**
 * Returns live deals total
 * @return	integer		live deals total
*/

if (!function_exists('GetLiveDealsTotal')) {
	function GetLiveDealsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_items WHERE start_date<=NOW() AND end_date>NOW() AND status='active'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns total of users reports
 * @return	integer		total of users reports
*/

if (!function_exists('GetUserReportsTotal')) {
	function GetUserReportsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_reports WHERE user_id<>'0'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns total of deal reports
 * @return	integer		total of deal reports
*/

if (!function_exists('GetDealReportsTotal')) {
	function GetDealReportsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_reports WHERE item_id<>'0'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns lowest shipping price
 * @return	integer		lowest shipping price
*/

if (!function_exists('GetLowerShippingCost')) {
	function GetLowerShippingCost()
	{
		$result = smart_mysql_query("SELECT cost FROM abbijan_shipping_methods ORDER BY cost LIMIT 1");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['cost'];
		}
		else
		{
			return false;
		}
	}
}



/**
 * Returns shipping method cost
 * @param	$shipping_method_id		Shipping method ID
 * @return	integer					shipping method cost
*/

if (!function_exists('GetShippingCost')) {
	function GetShippingCost($shipping_method_id)
	{
		$query = "SELECT * FROM abbijan_shipping_methods WHERE shipping_method_id='".(int)$shipping_method_id."' AND status='active' LIMIT 1";
		$result = smart_mysql_query($query);
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['cost'];
		}
		else
		{
			return false;
		}
	}
}



/**
 * Returns total of deal options
 * @param	$item_id		Deal ID
 * @return	integer		total of deal options
*/

if (!function_exists('GetDealOptionsTotal')) {
	function GetDealOptionsTotal($item_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_item_options WHERE item_id='".(int)$item_id."'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns total of new testimonials
 * @param	$active		count only active testimonials
 * @return	integer		total of new testimonials
*/

if (!function_exists('GetTestimonialsTotal')) {
	function GetTestimonialsTotal($active = 0)
	{
		if ($active == 1)
			$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_testimonials WHERE status='active'");
		else
			$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_testimonials WHERE status='inactive'");

		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns total of items in cart
 * @return	integer		total of items in cart
*/

if (!function_exists('GetCartItemsTotal')) {
	function GetCartItemsTotal()
	{
		$products = 0;

		if (is_array($_SESSION['quantity']))
		{
			if (count($_SESSION['quantity']))
			{
				foreach ($_SESSION['quantity'] as $quantity)
					$products += $quantity;
			}
		}

		return $products;
	}
}



/**
 * Returns user's information
 * @param	$user_id	User ID
 * @param	$show_as	Show name style
 * @return	string		user name, or "User not found"
*/

if (!function_exists('GetUsername')) {
	function GetUsername($user_id, $show_as = 0)
	{
		$result = smart_mysql_query("SELECT * FROM abbijan_users WHERE user_id='".(int)$user_id."' LIMIT 1");
		
		if (mysql_num_rows($result) != 0)
		{
			$row = mysql_fetch_array($result);

			if ($show_as > 0)
			{
				switch ($show_as)
				{
					case "1": $user_name = $row['fname']; break;
					case "2": $user_name = $row['fname']." ".substr($row['lname'], 0, 1); break;
					case "3": $user_name = $row['fname']." ".$row['lname']; break;
					case "4": $user_name = $row['nickname']; break;
				}
			}
			else
			{
				$user_name = $row['fname']." ".$row['lname'];
			}

			return $user_name;
		}
		else
		{
			return "User not found";
		}
	}
}



/**
 * Returns user's orders total
 * @param	$user_id	User ID
 * @return	integer		orders total
*/

if (!function_exists('GetUserOrdersTotal')) {
	function GetUserOrdersTotal($user_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_orders WHERE user_id='".(int)$user_id."'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns user's shipping addresses total
 * @param	$user_id	User ID
 * @return	integer		shipping addresses total
*/

if (!function_exists('GetUserShippingTotal')) {
	function GetUserShippingTotal($user_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_shipping WHERE user_id='".(int)$user_id."'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns deal quantity
 * @param	$item_id		Deal ID
 * @return	string			deal quantity
*/

if (!function_exists('GetDealQuantity')) {
	function GetDealQuantity($item_id)
	{
		$row = mysql_fetch_array(smart_mysql_query("SELECT SUM(item_quantity) AS quantity_sold FROM abbijan_order_items WHERE item_id='".(int)$item_id."' AND order_id IN (SELECT order_id FROM abbijan_orders WHERE (status='complete' OR status='shipped' OR status='delivered' OR (status='pending' AND TIMESTAMPDIFF(MINUTE, created, NOW()) < ".CHECKOUT_MINUTES_LIMIT.")))"));
		$quantity_sold = $row['quantity_sold'];

		$row2 = mysql_fetch_array(smart_mysql_query("SELECT quantity FROM abbijan_items WHERE item_id='".(int)$item_id."' LIMIT 1"));
		$quantity = $row2['quantity'];

		// if quantity is limited
		if ($quantity > 0)
		{
			$available_quantity = $quantity - $quantity_sold;
		
			if ($available_quantity > 0)
				return $available_quantity;
			else
				return "0";
		}
		else
		{
			return true;
		}
	}
}



/**
 * Returns sold deals
 * @param	$item_id		Deal ID
 * @return	string			sold deals
*/

if (!function_exists('GetDealSoldQuantity')) {
	function GetDealSoldQuantity($item_id)
	{
		$row = mysql_fetch_array(smart_mysql_query("SELECT SUM(item_quantity) AS quantity_sold FROM abbijan_order_items WHERE item_id='".(int)$item_id."' AND order_id IN (SELECT order_id FROM abbijan_orders WHERE (status='complete' OR status='shipped' OR status='delivered' OR (status='pending' AND TIMESTAMPDIFF(MINUTE, created, NOW()) < ".CHECKOUT_MINUTES_LIMIT.")))"));
		$quantity_sold = $row['quantity_sold'];

		return $quantity_sold;
	}
}



/**
 * Returns stock bar width
 * @param	$item_id		Deal ID
 * @return	string			stock bar width
*/

if (!function_exists('GetStockBarWidth')) {
	function GetStockBarWidth($item_id)
	{
		$item_id = (int)$item_id;

		$row = mysql_fetch_array(smart_mysql_query("SELECT quantity FROM abbijan_items WHERE item_id='$item_id' LIMIT 1"));
		$quantity = $row['quantity'];

		$sold_out = GetDealSoldQuantity($item_id);

		if ($sold_out == 0)
		{
			return "100%";
		}
		else if ($quantity == $sold_out)
		{
			return "0%";
		}
		else
		{
			$instock	= $quantity - $sold_out;
			$count		= ($instock/$quantity) * 100;
			$bar_width	= number_format($count, 0, '', '')."%";

			return $bar_width;
		}
	}
}



/**
 * Send order receipt
 * @param	$order_id	Order ID
*/

if (!function_exists('SendReceipt')) {
	function SendReceipt($order_id)
	{
		$order_id = (int)$order_id;

		$oresult = smart_mysql_query("SELECT users.*, orders.* FROM abbijan_users users, abbijan_orders orders WHERE users.user_id=orders.user_id AND orders.order_id='$order_id' LIMIT 1");

		$result = smart_mysql_query("SELECT * FROM abbijan_order_items WHERE order_id='$order_id'");

		if (mysql_num_rows($oresult) > 0 && mysql_num_rows($result) > 0)
		{
			$orow = mysql_fetch_array($oresult);

			$order_items = "<p>---------------------------------------</p>\n";

			while ($row = mysql_fetch_array($result))
			{
				$order_items .= "<p><b>".$row['item_title']."</b>: ".$row['item_quantity']."x".DisplayMoney($row['item_price'])."</p>\n";
			}

			$order_items .= "<p>---------------------------------------</p>\n";

			$order_items .= "<p>".DisplayMoney($orow['total'])."</p>\n";
			$order_items .= "<p>Shipping: ".DisplayMoney($orow['shipping_total'])."</b></p>\n";
			$order_items .= "<p><b>TOTAL: ".DisplayMoney($orow['total']+$orow['shipping_total'])."</b></p>\n";

			$etemplate = GetEmailTemplate('order_receipt');
			$esubject = $etemplate['email_subject'];
			$emessage = $etemplate['email_message'];

			$emessage = str_replace("{first_name}", $orow['fname'], $emessage);
			$emessage = str_replace("{order_id}", $orow['reference_id'], $emessage);
			$emessage = str_replace("{order_items}", $order_items, $emessage);

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
}



/**
 * Send deal alert to members
 * @param	$item_id		Deal ID
*/

if (!function_exists('SendDealInvitations')) {
	function SendDealInvitations($item_id)
	{
		$item_id = (int)$item_id;

		$iresult = smart_mysql_query("SELECT *, DATE_FORMAT(start_date, '%d %b %Y %h:%i') AS sale_start_date, DATE_FORMAT(end_date, '%d %b %Y %h:%i') AS sale_end_date FROM abbijan_items WHERE item_id='$item_id' LIMIT 1");
		$result = smart_mysql_query("(SELECT email, activation_key AS unsubscribe_key, fname AS first_name, CONCAT(fname, \" \", lname) as full_name FROM abbijan_users WHERE email != '' AND newsletter='1' AND status='active') UNION (SELECT email, unsubscribe_key, \"Subscriber\" AS first_name, \"Newsletter Subscriber\" AS full_name FROM abbijan_subscribers WHERE email != '' AND status='active')");

		if (mysql_num_rows($iresult) > 0 && mysql_num_rows($result) > 0)
		{
			$irow = mysql_fetch_array($iresult);

			while ($row = mysql_fetch_array($result))
			{
				$etemplate = GetEmailTemplate('daily_deal_alert');
				$esubject = $etemplate['email_subject'];
				$emessage = $etemplate['email_message'];

				$emessage = str_replace("{first_name}", $row['fname'], $emessage);
				$emessage = str_replace("{deal_name}", $irow['title'], $emessage);
				$emessage = str_replace("{deal_brief_description}", stripslashes($irow['brief_description']), $emessage);
				$emessage = str_replace("{deal_description}", stripslashes($irow['description']), $emessage);
				$emessage = str_replace("{deal_image_url}", substr(SITE_URL, 0, -1).IMAGES_URL.$irow['thumb'], $emessage);
				$emessage = str_replace("{deal_start_date}", $irow['sale_start_date'], $emessage);
				$emessage = str_replace("{deal_end_date}", $irow['sale_end_date'], $emessage);
				$emessage = str_replace("{deal_price}", DisplayMoney($irow['price']), $emessage);
				$emessage = str_replace("{deal_url}", SITE_URL."deal_details.php?id=".$irow['item_id'], $emessage);

				$user_name = $row['fname']." ".$row['lname'];
				$user_email = $row['email'];

				$to_email = $user_name.' <'.$user_email.'>';
				$subject = $esubject;
				$message = $emessage;
		
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";

				@mail($to_email, $subject, $message, $headers);
			}
		}
	}
}



/**
 * Returns total of new messages
 * @param	$user_id		user ID
 * @return	integer			total of new messages
*/

if (!function_exists('GetMessagesTotal')) {
	function GetMessagesTotal($user_id = 0)
	{
		if ($user_id > 0)
			$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_messages WHERE user_id='".(int)$user_id."' AND is_admin='1' AND viewed='0'");
		else
			$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_messages WHERE is_admin='0' AND viewed='0'");
		
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns deal's total sales
 * @param	$deal_id	Deal ID
 * @return	integer		deal's total sales
*/

if (!function_exists('GetDealSalesTotal')) {
	function GetDealSalesTotal($deal_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS sales_total FROM abbijan_orders orders LEFT JOIN abbijan_order_items details ON orders.order_id=details.order_id WHERE details.item_id='".(int)$deal_id."' AND (orders.status='complete' OR orders.status='shipped' OR orders.status='delivered') AND orders.total>0");
		$row = mysql_fetch_array($result);
		return (int)$row['sales_total'];
	}
}



/**
 * Returns deal's first customer
 * @param	$deal_id	Deal ID
 * @return	string		deal's first customer
*/

if (!function_exists('GetDealFirstCustomer')) {
	function GetDealFirstCustomer($deal_id)
	{
		$result = smart_mysql_query("SELECT user_id FROM abbijan_orders WHERE order_id IN (SELECT order_id FROM abbijan_order_items WHERE item_id='".(int)$deal_id."') AND (status='complete' OR status='shipped' OR status='delivered') ORDER BY created ASC LIMIT 1");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return GetUsername($row['user_id'], $show_as = 1);
		}
		else
		{
			return "---";
		}
	}
}



/**
 * Returns deal's last customer
 * @param	$deal_id	Deal ID
 * @return	string		deal's last customer
*/

if (!function_exists('GetDealLastCustomer')) {
	function GetDealLastCustomer($deal_id)
	{
		$result = smart_mysql_query("SELECT user_id FROM abbijan_orders WHERE order_id IN (SELECT order_id FROM abbijan_order_items WHERE item_id='".(int)$deal_id."') AND (status='complete' OR status='shipped' OR status='delivered') ORDER BY created DESC LIMIT 1");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return GetUsername($row['user_id'], $show_as = 1);
		}
		else
		{
			return "---";
		}
	}
}



/**
 * Returns deal's first sale
 * @param	$deal_id	Deal ID
 * @return	string		deal's first sale
*/

if (!function_exists('GetDealFirstSale')) {
	function GetDealFirstSale($deal_id)
	{
		$result = smart_mysql_query("SELECT *, DATE_FORMAT(created, '%e %b %Y') AS sale_date FROM abbijan_orders WHERE order_id IN (SELECT order_id FROM abbijan_order_items WHERE item_id='".(int)$deal_id."') AND (status='complete' OR status='shipped' OR status='delivered') ORDER BY created ASC LIMIT 1");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return relative_date(strtotime($row['created']));
		}
		else
		{
			return "---";
		}
	}
}



/**
 * Returns deal's first sale speed // in minutes
 * @param	$deal_id	Deal ID
 * @return	string		deal's first sale speed  // in minutes
*/

if (!function_exists('GetDealFirstSaleSpeed')) {
	function GetDealFirstSaleSpeed($deal_id)
	{
		$result = smart_mysql_query("SELECT *, DATE_FORMAT(created, '%e %b %Y') AS sale_date FROM abbijan_orders WHERE order_id IN (SELECT order_id FROM abbijan_order_items WHERE item_id='".(int)$deal_id."') AND (status='complete' OR status='shipped' OR status='delivered') ORDER BY created ASC LIMIT 1");
		$result2 = smart_mysql_query("SELECT * FROM abbijan_items WHERE item_id='".(int)$deal_id."' LIMIT 1");
		if (mysql_num_rows($result) > 0 && mysql_num_rows($result2) > 0)
		{
			$row = mysql_fetch_array($result);
			$row2 = mysql_fetch_array($result2);
			
			if ($row2['start_date'] != "0000-00-00 00:00:00")
				$t1 = strtotime($row2['start_date']);
			else
				$t1 = strtotime($row2['added']);

			$t2 = strtotime($row['created']);

			return round(abs($t2 - $t1) / 60,0)." minutes";
		}
		else
		{
			return "---";
		}
	}
}



/**
 * Returns deal's last sale
 * @param	$deal_id	Deal ID
 * @return	string		deal's last sale
*/

if (!function_exists('GetDealLastSale')) {
	function GetDealLastSale($deal_id)
	{
		$result = smart_mysql_query("SELECT *, DATE_FORMAT(created, '%e %b %Y') AS sale_date FROM abbijan_orders WHERE order_id IN (SELECT order_id FROM abbijan_order_items WHERE item_id='".(int)$deal_id."') AND (status='complete' OR status='shipped' OR status='delivered') ORDER BY created DESC LIMIT 1");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return relative_date(strtotime($row['created']));
		}
		else
		{
			return "---";
		}
	}
}



/**
 * Returns deal's images
 * @param	$item_id		Deal ID
 * @return	string			deal's images
*/

if (!function_exists('GetDealImages')) {
	function GetDealImages($item_id)
	{
		$result = smart_mysql_query("SELECT * FROM abbijan_item_images WHERE item_id='".(int)$item_id."' ORDER BY main_image DESC, item_image_id ASC");

		if (mysql_num_rows($result) > 0)
		{
			$images1 = "<div class=\"productImages\">\n";
			$images2 = "<div style=\"width:0; height:0; overflow:hidden;\">\n";

			$cc = 1;
			$other_images = 0;

			while ($row = mysql_fetch_array($result))
			{
				$cc++;

				if ($row['main_image'] == 1)
				{
					$main_image = $row['medium_image'];
					$images1 .= "<div id=\"main_image\"><a class=\"fancy\" href=\"#lrgImage".$item_id."\" rel=\"group\"><img src=\"".IMAGES_URL.$row['medium_image']."\" id=\"mainImage".$item_id."\" class=\"imgMedium\" /></a></div>\n";
					$images2 .= "<div id=\"lrgImage".$item_id."\"><img src=\"".IMAGES_URL.$row['image']."\" class=\"fancy\" /></div>\n";
				}
				else
				{
					$other_images++;

					$e .= '<li><a class="fancy" href="'.IMAGES_URL.$row['image'].'" rel="group"><img src="'.IMAGES_URL.$row['thumb_image'].'" width="55" height="55" alt="" class="thumb" onmouseover="document.getElementById(\'mainImage'.$item_id.'\').src=\''.IMAGES_URL.$row['medium_image'].'\';" onmouseout="document.getElementById(\'mainImage'.$item_id.'\').src=\''.IMAGES_URL.$main_image.'\';" /></a></li>';
				}
			}

			$images2 .= "</div>\n";
			if ($other_images > 0)
				$images2 .= '<div style="float: right; width: 70px;"><ul id="mycarousel" class="jcarousel-skin-tango">'.$e.'</ul></div>';
			$images2 .= "</div>\n";
			$images = $images1.$images2;

			return $images;
		}
		else
		{
			return false;
		}
	}
}



/**
 * Checks deal's quantity limit
 * @param	$item_id		Deal ID
 * @param	$quantity		Quantity
 * @param	$customer_id	Customer ID
 * @return	boolean			false or true
*/

if (!function_exists('CheckCustomerLimit')) {
	function CheckCustomerLimit($item_id, $quantity, $customer_id = 0)
	{
		$result = smart_mysql_query("SELECT customer_limit FROM abbijan_items WHERE item_id='".(int)$item_id."' AND status='active' LIMIT 1");

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			if ($row['customer_limit'] == 0 || $quantity <= $row['customer_limit'])
				return true;
			else
				return false;

			// check quantity with previuos customer's orders
			if ($customer_id > 0)
			{
				$orow = mysql_fetch_array(smart_mysql_query("SELECT SUM(quantity) AS customer_quantity FROM abbijan_order_items WHERE user_id='$customer_id' AND item_id='".(int)$item_id."'"));

				$total_quantity = $orow['customer_quantity'] + $quantity;
				
				if ($total_quantity <= $row['customer_limit'])
					return true;
				else
					return false;
			}
		}
		else
		{
			return false;
		}
	}
}



/**
 * Returns deal's title
 * @param	$item_id		Deal ID
 * @return	string			deal's title
*/

if (!function_exists('GetDealName')) {
	function GetDealName($item_id)
	{
		$result = smart_mysql_query("SELECT title FROM abbijan_items WHERE item_id='".(int)$item_id."' AND status='active' LIMIT 1");

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['title'];
		}
	}
}



/**
 * Returns deal's quantity limit per customer
 * @param	$item_id		Deal ID
 * @return	string			deal's quantity limit
*/

if (!function_exists('GetCustomerLimit')) {
	function GetCustomerLimit($item_id)
	{
		$result = smart_mysql_query("SELECT customer_limit FROM abbijan_items WHERE item_id='".(int)$item_id."' AND status='active' LIMIT 1");

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return (int)$row['customer_limit'];
		}
	}
}



/**
 * Checks deal's inventory
 * @param	$item_id		Deal ID
 * @return	boolean			true or false
*/

if (!function_exists('CheckInventory')) {
	function CheckInventory($item_id, $quantity)
	{
		$row = mysql_fetch_array(smart_mysql_query("SELECT SUM(item_quantity) AS quantity_sold FROM abbijan_order_items WHERE item_id='".(int)$item_id."' AND order_id IN (SELECT order_id FROM abbijan_orders WHERE (status='complete' OR status='shipped' OR status='delivered') OR (status='pending' AND TIMESTAMPDIFF(MINUTE, created, NOW()) < ".CHECKOUT_MINUTES_LIMIT."))"));
		$quantity_sold = $row['quantity_sold'];

		$row2 = mysql_fetch_array(smart_mysql_query("SELECT quantity FROM abbijan_items WHERE item_id='".(int)$item_id."' LIMIT 1"));
		$item_quantity = $row2['quantity'];

		// if limited quantity
		if ($item_quantity > 0)
		{
			$available_quantity = $item_quantity - $quantity_sold - $quantity;
		
			if ($available_quantity > 0)
				return true;
			else
				return false;
		}
		else
		{
			return true;
		}
	}
}



/**
 * Returns member's referrals total
 * @param	$userid		User's ID
 * @return	string		member's referrals total
*/

if (!function_exists('GetReferralsTotal')) {
	function GetReferralsTotal($userid)
	{
		$query = "SELECT COUNT(*) AS total FROM abbijan_users WHERE ref_id='".(int)$userid."'";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['total'];
		}
	}
}



/**
 * Returns member's referrals list
 * @param	$userid		User's ID
 * @return	array		member's referrals list
*/

if (!function_exists('GetUserReferrals')) {
	function GetUserReferrals($userid)
	{
		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS signup_date FROM abbijan_users WHERE ref_id='".(int)$userid."' AND status='active' ORDER BY fname";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			$myRefs = array();
			while ($row = mysql_fetch_array($result))
			{
				$myRefs[] = "<a href='user_profile.php?id=".$row['user_id']."'>".$row['fname']." ".substr($row['lname'], 0, 1).".</a>";
			}
			
			return $myRefs;
		}
	}
}



/**
 * Saves referral's ID in cookies
 * @param	$ref_id		Referrals's ID
*/

if (!function_exists('setReferral')) {
	function setReferral($ref_id)
	{
		//set up cookie for one month period
		setcookie("referer_id", $ref_id, time()+(60*60*24*30));
	}
}




/**
 * Returns payment type name 
 * @return	string	payment type name
*/

if (!function_exists('GetPaymentName')) {
	function GetPaymentName($payment_type)
	{
		switch($payment_type)
		{
			case "withdraw": $payment_name = "Withdraw"; break;
			case "deposit": $payment_name = "Deposit"; break;
		}

		return $payment_name;
	}
}



/**
 * Returns payment method name by payment method ID
 * @param	$payment_method_id		Payment Method ID
 * @return	string					payment method name
*/

if (!function_exists('GetPaymentMethodName')) {
	function GetPaymentMethodName($payment_method_id)
	{
		$result = smart_mysql_query("SELECT * FROM abbijan_payment_methods WHERE payment_method_id='".(int)$payment_method_id."' LIMIT 1");
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['title'];
		}
	}
}



/**
 * Returns shipping method name by shipping method ID
 * @param	$smethod_id		Shipping Method ID
 * @return	string			shipping method name
*/

if (!function_exists('GetShippingMethodName')) {
	function GetShippingMethodName($smethod_id)
	{
		$result = smart_mysql_query("SELECT * FROM abbijan_shipping_methods WHERE shipping_method_id='".(int)$smethod_id."' LIMIT 1");
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			$row = mysql_fetch_array($result);
			return $row['title'];
		}
	}
}



/**
 * Returns date of last forum post
 * @param	$forum_id	Forum ID
 * @return	string		date of last post
*/

if (!function_exists('GetLastForumPost')) {
	function GetLastForumPost($forum_id)
	{
		$result = smart_mysql_query("SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS date_added FROM abbijan_forum_comments WHERE forum_id='".(int)$forum_id."' ORDER BY added DESC LIMIT 1");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$last_post = "<a href='/forum_details.php?id=".$row['forum_id']."#".$row['forum_comment_id']."'>last post</a> by ".GetUsername($row['user_id'], $show_as = 1)."<br/>";
			$last_post .= "<span class='last_post_date'>".$row['date_added']."</span>";
			return $last_post;
		}
		else
		{
			return "-----";
		}
	}
}



/**
 * Returns total of forum comments
 * @param	$forum_id	Forum ID
 * @param	$all		show all or only active / awaiting approval
 * @return	integer		total of forum comments
*/

if (!function_exists('GetForumPostsTotal')) {
	function GetForumPostsTotal($forum_id, $all = 0)
	{
		if ($all == 1)
			$result = smart_mysql_query("SELECT COUNT(*) as total_comments FROM abbijan_forum_comments WHERE forum_id='".(int)$forum_id."'");
		else if ($all == 2)
			$result = smart_mysql_query("SELECT COUNT(*) as total_comments FROM abbijan_forum_comments WHERE forum_id='".(int)$forum_id."' AND status='pending'");
		else
			$result = smart_mysql_query("SELECT COUNT(*) as total_comments FROM abbijan_forum_comments WHERE forum_id='".(int)$forum_id."' AND status='active'");

		$row = mysql_fetch_array($result);
		return (int)$row['total_comments'];
	}
}



/**
 * Returns forum title
 * @param	$forum_id	Forum ID
 * @return	string		forum title
*/

if (!function_exists('GetForumTitle')) {
	function GetForumTitle($forum_id)
	{
		$result = smart_mysql_query("SELECT title FROM abbijan_forums WHERE forum_id='".(int)$forum_id."'");
		$row = mysql_fetch_array($result);
		return $row['title'];
	}
}



/**
 * Returns total of deal comments
 * @param	$item_id	Deal ID
 * @param	$all		show all or only active
 * @return	integer		total of deal comments
*/

if (!function_exists('GetDealCommentsTotal')) {
	function GetDealCommentsTotal($item_id, $all = 0)
	{
		if ($all == 1)
			$result = smart_mysql_query("SELECT COUNT(*) as total_comments FROM abbijan_forum_comments WHERE item_id='".(int)$item_id."'");
		else
			$result = smart_mysql_query("SELECT COUNT(*) as total_comments FROM abbijan_forum_comments WHERE item_id='".(int)$item_id."' AND status='active'");

		$row = mysql_fetch_array($result);
		return (int)$row['total_comments'];
	}
}



/**
 * Returns total of past deals
 * @return	integer		total of past deals
*/

if (!function_exists('GetPastDealsTotal')) {
	function GetPastDealsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) as past_deals FROM abbijan_items WHERE end_date<NOW() OR status='sold'");

		$row = mysql_fetch_array($result);
		return (int)$row['past_deals'];
	}
}



/**
 * Returns total of future deals
 * @return	integer		total of future deals
*/

if (!function_exists('GetFutureDealsTotal')) {
	function GetFutureDealsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) as future_deals FROM abbijan_items WHERE start_date>NOW() AND status!='inactive'");

		$row = mysql_fetch_array($result);
		return (int)$row['future_deals'];
	}
}



/**
 * Returns deal's thumb image url
 * @param	$item_id	Deal ID
 * @return	string		deal's thumb image url
*/

if (!function_exists('GetDealThumb')) {
	function GetDealThumb($item_id)
	{
		$result = smart_mysql_query("SELECT thumb FROM abbijan_items WHERE item_id='".(int)$item_id."' LIMIT 1");
		$row = mysql_fetch_array($result);

		if ($row['thumb'] != "")
		{
			return "<img src=\"".IMAGES_URL.$row['thumb']."\" width=\"70\" height=\"70\" class=\"thumb\" />";
		}
	}
}




/**
 * Returns order's total items
 * @param	$order_id	Order ID
 * @return	integer		order's total items
*/

if (!function_exists('GetOrderItemsTotal')) {
	function GetOrderItemsTotal($order_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_order_items WHERE order_id='".(int)$order_id."'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns total of pending orders
 * @return	integer		total of pending orders
*/

if (!function_exists('GetPendingOrdersTotal')) {
	function GetPendingOrdersTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_orders WHERE viewed='0'"); //status='pending'
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns total of member's requested money
 * @return	string	total
*/

if (!function_exists('GetRequestsTotal')) {
	function GetRequestsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_transactions WHERE payment_type='withdraw' AND status='request'");
		$row = mysql_fetch_array($result);
		return $row['total'];
	}
}



/**
 * Returns total of users which added deal to their favorites list
 * @return	integer		total of users
*/

if (!function_exists('GetFavoritesTotal')) {
	function GetFavoritesTotal($item_id)
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_favorites WHERE item_id='".(int)$item_id."'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}



/**
 * Returns prev-next navigation links
 * @param	$item_id	Deal ID
 * @return	string		prev-next navigation links
*/

function PrevNextNav($item_id)
{
	$item_id = (int)$item_id;

	$prev_result = smart_mysql_query("SELECT * FROM abbijan_items WHERE item_id<'$item_id' AND start_date<=NOW() AND end_date>NOW() AND status='active' ORDER BY item_id DESC LIMIT 1");
	$next_result = smart_mysql_query("SELECT * FROM abbijan_items WHERE item_id>'$item_id' AND start_date<=NOW() AND end_date>NOW() AND status='active' ORDER BY item_id ASC LIMIT 1");
	$prev_total = mysql_num_rows($prev_result);
	$next_total = mysql_num_rows($next_result);

	if ($prev_total != 0 || $next_total != 0)
	{
		$navigation = "<div class=\"nextprevlinks\">";
		if ($prev_total != 0) { $prev_row = mysql_fetch_array($prev_result); $navigation .= "<a href=\"/deal_details.php?id=".$prev_row['item_id']."\" class=\"prev\">Prev Deal</a> "; }
		if ($next_total != 0) { $next_row = mysql_fetch_array($next_result); $navigation .= "<a href=\"/deal_details.php?id=".$next_row['item_id']."\" class=\"next\">Next Deal</a>"; }
		$navigation .= "</div>";

		return $navigation;
	}
}



/**
 * Resize Image
 * @param	$img		Image Name
 * @param	$new_name	Image 'Save As' Name
 * @param	$maxwidth	Image Max Width
 * @param	$maxheight	Image Max Height
*/

define('WATERMARK_IMAGE', '../images/watermark.png');
define('WATERMARK_OPACITY', 60);

function resize_img($img, $newname, $watermark=false, $maxwidth = 800, $maxheight = 600)
{
	$method = "scale";
	
	list($width, $height, $type) = @getimagesize($img);	

	// if either max_width or max_height are 0 or null then calculate it proportionally
	if( !$maxwidth ){
		$maxwidth = $width / ($height / $maxheight);
	}
	elseif( !$maxheight ){
		$maxheight = $height / ($width / $maxwidth);
	}
 
	// initialize some variables
	$thumb_x = $thumb_y = 0;	// offset into thumbination image
 
	// if scaling the image calculate the dest width and height
	$dx = $width / $maxwidth;
	$dy = $height / $maxheight;
	if( $method == 'scale' ){
		$d = max($dx,$dy);
	}
	// otherwise assume cropping image
	else{
		$d = min($dx, $dy);
	}
	$new_width = $width / $d;
	$new_height = $height / $d;
	// sanity check to make sure neither is zero
	$new_width = max(1,$new_width);
	$new_height = max(1,$new_height);
 
	$thumbwidth = min($maxwidth, $new_width);
	$thumbheight = min($maxheight, $new_height);

	$imgbuffer = imagecreatetruecolor($thumbwidth, $thumbheight);
	
	switch($type) {
		case 1: $image = imagecreatefromgif($img); break;
		case 2: $image = imagecreatefromjpeg($img); break;
		case 3: $image = imagecreatefrompng($img); break;
		default: return "Tried to create thumbnail from $img: not a valid image";
	}

	if ($watermark)
	{
		$watermark = imagecreatefrompng(WATERMARK_IMAGE);
		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);

		imagecopymerge(
			$image,
			$watermark,
			5,
			$height - $watermark_height - 5,
			0,
			0,
			$watermark_width,
			$watermark_height,
			WATERMARK_OPACITY
	);
		 imagedestroy($watermark);
	}

    if (!$image)
	{
		return "Image creation from $img failed for an unknown reason. Probably not a valid image.";
	}
    else
	{
      imagecopyresampled($imgbuffer, $image, 0, 0, 0, 0, $thumbwidth, $thumbheight, $width, $height);
      imageinterlace($imgbuffer);
      $output = imagejpeg($imgbuffer, $newname, 90);
      imagedestroy($imgbuffer);
      return $output;
    }
} 



/**
 * Create thumbnail image
 * @param	$source		Image Name
 * @param	$dest		Thumb 'Save As' Name
 * @param	$new_width	Thumb Width
 * @param	$new_height	Thumb Height
*/

function create_thumb($source, $dest, $new_width, $new_height)
{
    $sourcedate = 0;
    $destdate = 0;
    global $convert;

    if (file_exists($dest)) {
       clearstatcache();
       $sourceinfo = stat($source);
       $destinfo = stat($dest);
       $sourcedate = $sourceinfo[10];
       $destdate = $destinfo[10];
    }
    if (!file_exists("$dest") or ($sourcedate > $destdate)) {
       global $ImageTool;
       $imgsize = @GetImageSize($source);
       $width = $imgsize[0];
       $height = $imgsize[1];
	   $type = $imgsize[2];
 
      if ($width > $height) { // If the width is greater than the height it's a horizontal picture
        $xoord = ceil(($width - $height) / 2 );
        $width = $height;      // Then we read a square frame that  equals the width
      } else {
        $yoord = ceil(($height - $width) / 2);
        $height = $width;
      }

        $new_im = ImageCreatetruecolor($new_width,$new_height);
		//$im = ImageCreateFromJPEG($source);

		switch($type) {
			case 1: $im = imagecreatefromgif($source); break;
			case 2: $im = imagecreatefromjpeg($source); break;
			case 3: $im = imagecreatefrompng($source); break;
			default: return "JPG, GIF and PNG only. Please try again";
		}

        imagecopyresampled($new_im,$im,0,0,$xoord,$yoord,$new_width,$new_height,$width,$height);
        ImageJPEG($new_im,$dest,90);
    }
}


/**
 * Delete user avatar
 * @param	$user_id	User ID
*/

if (!function_exists('DeleteAvatar')) {
	function DeleteAvatar($user_id)
	{
		$userid = (int)$user_id;
		$row = mysql_fetch_array(smart_mysql_query("SELECT avatar FROM abbijan_users WHERE user_id='$userid' LIMIT 1"));
		if (file_exists(PUBLIC_HTML_PATH.AVATARS_URL.$row['avatar']) && $row['avatar'] != "" && $row['avatar'] != "no_avatar.png") @unlink(PUBLIC_HTML_PATH.AVATARS_URL.$row['avatar']);
	}
}


/**
 * Sends custom email
 * @param	$recipient	RECIPIENT'S EMAIL
 * @param	$sender		SENDER'S NAME/EMAIL
 * @param	$subject	EMAIL SUBJECT
 * @param	$message	EMAIL MESSAGE
 * @param	$search		ARRAY OF STRINGS TO SEARCH FOR
 * @param	$replace	ARRAY OF STRINGS TO REPLACE $search WITH
*/

function send_email($recipient, $sender, $subject, $message, $search, $replace)
{
	// DECODE SUBJECT AND EMAIL FOR SENDING
	$subject = htmlspecialchars_decode($subject, ENT_QUOTES);
	$message = htmlspecialchars_decode($message, ENT_QUOTES);

	// REPLACE VARIABLES IN SUBJECT AND MESSAGE
	$subject = str_replace($search, $replace, $subject);
	$message = str_replace($search, $replace, $message);

	// ENCODE SUBJECT FOR UTF8
	$subject="=?UTF-8?B?".base64_encode($subject)."?=";

	// REPLACE CARRIAGE RETURNS WITH BREAKS
	$message = str_replace("\n", "<br>", $message);

	// SET HEADERS
	$headers = "MIME-Version: 1.0"."\n";
	$headers .= "Content-type: text/html; charset=utf-8"."\n";
	$headers .= "Content-Transfer-Encoding: 8bit"."\n";
	$headers .= "From: $sender"."\n";
	$headers .= "Return-Path: $sender"."\n";
	$headers .= "Reply-To: $sender\n";

	// SEND MAIL
	mail($recipient, $subject, $message, $headers);

	return true;
}

?>