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
	require_once("./inc/adm_functions.inc.php");

	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];
		$pn	= (int)$_GET['pn'];
		
		$query = "SELECT o.*, DATE_FORMAT(o.created, '%e %b %Y') AS order_date, DATE_FORMAT(o.updated, '%e %b %Y') AS updated_date, u.username, u.email, u.fname, u.lname FROM abbijan_orders o LEFT JOIN abbijan_users u ON o.user_id=u.user_id WHERE o.order_id='$id'";
		
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			$row = mysql_fetch_array($result);
		}
	}

	$cc = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Order #<?php echo $row['order_id']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
    body {
        width: 1000px;
        margin-left: auto;
        margin-right: auto;
		font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
		font-size: 13px;
		color: #000;
		background: #FFFFFF;  
		border: 2px dotted #eee;
    }
    
    div#header{
    	margin-left: 50px;
    	margin-top: 50px;
    	height: 150px;
    	width: 500px;
    	float: left;
    }
    div#company{
    	margin-right: 50px;
    	margin-top: 50px;
    	height: 150px;
    	width: 400px;
    	float: right;
    	text-align: right;
    }
    div#map{
    	margin-left: 50px;
    	height: 350px;
    	width: 500px;
    	float: left;
    }
    h1{
    	margin: 0;
    	font-weight: normal;
    	font-size: 40px;
    }
    h2{
    	margin: 0;
    	font-weight: normal;
    	font-size: 24px;
    }
    h3{
    	margin: 0;
    	font-weight: normal;
    	font-size: 24px;
    }
    p{
    	margin: 0;
    	font-size: 20px;
    }
	.website {
		color: #499EFF;
	}
	.total {
		font-size: 17px;
		font-weight: normal;
	}
    </style>	
</head>
<body onload="window.print(); window.close();">

	<?php if ($total > 0) { ?>

	<div id="header">
		<h1>Order #<?php echo $row['order_id']; ?></h1>
		<p><?php echo $row['order_date']; ?></p>
	</div>

	<div id="company">
		<h2><?php echo SITE_TITLE; ?></h2>
		<p><span class="website"><?php echo substr(SITE_URL, 0, -1); ?></span></p>
	</div>

<table width="90%" cellpadding="3" cellspacing="0" border="0" align="center">
<tr>
<td nowrap="nowrap" align="right" valign="top">

	<h2>Payment method</h2>
	<?php echo GetPaymentMethodName($row['payment_method_id']); ?>

	<h2>Shipping method</h2>
	<?php echo GetShippingMethodName($row['shipping_method_id']); ?>

	<h2>Customer</h2>
		<?php if ($row['user_id'] > 0) { ?>
			<?php echo $row['fname']." ".$row['lname']; ?>
		<?php }else{ ?>
			<?php echo $row['client_name']; ?>
		<?php } ?>
		<br/>
		<?php echo $row['shipping_details']; ?>

</td>
</tr>
</table>

		<?php
				$items_query = "SELECT * FROM abbijan_order_items WHERE order_id='$id'";
				$items_result = smart_mysql_query($items_query);
				$items_total = mysql_num_rows($items_result);
		?>

			<table width="90%" cellpadding="2" cellspacing="2" border="0" align="center">
			<tr>
				<td align="left" valign="top"><h3>Purchased items</h3></td>
			</tr>
			<tr>
				<td align="left" valign="top">
					<table width="100%" style="border-bottom: 1px solid #EEE;" cellpadding="3" cellspacing="0" border="0">
					<tr height="35" bgcolor="#727272" style="color: #FFF;">
						<td nowrap="nowrap" width="40%" align="left"><b>Item</b></td>
						<td nowrap="nowrap" width="15%" align="center"><b>Price</b></td>
						<td nowrap="nowrap" width="15%" align="center"><b>Quantity</b></td>
						<td nowrap="nowrap" width="20%" align="center"><b>Subtotal</b></td>
					</tr>
					<?php
				 
						if ($items_total > 0)
						{
							while ($items_row = mysql_fetch_array($items_result))
							{
								$cc++;

								$subtotal		= $items_row['item_price']*$items_row['item_quantity'];
								$TotalPrice		+= $subtotal;
								//$ShippingPrice	+= $items_row['shipping_price']*$items_row['product_quantity'];
					?>
					<tr height="25" bgcolor="<?php if (($cc%2) == 0) echo "#F7F7F7"; else echo "#FFFFFF";?>">
						<td align="left" valign="middle"><b><?php echo $items_row['item_title']; ?></b></td>
						<td align="center" valign="middle"><?php echo DisplayMoney($items_row['item_price']); ?></td>
						<td align="center" valign="middle"><?php echo $items_row['item_quantity']; ?></td>
						<td align="center" valign="middle"><?php echo DisplayMoney($items_row['item_price']*$items_row['item_quantity']); ?></td>
					</tr>
					<?php
							}
						}
						else
						{
							echo "<tr height='20'><td valign='middle' align='center' colspan='4'>no items found</td></tr>";
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
					<td width="100" nowrap="nowrap" valign="middle" align="right">Subtotal:</td>
					<td nowrap="nowrap" valign="middle" align="right"><?php echo DisplayMoney($TotalPrice); ?></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="middle" align="right">Shipping:</td>
					<td nowrap="nowrap" valign="middle" align="right"><?php echo DisplayMoney($row['shipping_total']); ?></td>
				</tr>
                <tr style="border-top: 1px solid #eee;" height="40">
					<td nowrap="nowrap" valign="middle" align="right"><b>TOTAL:</b></td>
					<td nowrap="nowrap" valign="middle" align="right"><span class="total"><?php echo DisplayMoney($row['total']+$row['shipping_total']); ?></span></td>
				</tr>
			  </table>
			</div>
			</td>
			</tr>
			</table>

      <?php }else{ ?>
				<div class="info_box">Sorry, order not found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go back</a></p>
      <?php } ?>

</body>
</html>