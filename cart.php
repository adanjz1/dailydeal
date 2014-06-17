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


	$cc = 0;


	// go to checkout
	if (isset($_POST["Checkout"]) && $_POST["Checkout"] != "")
	{
		$_SESSION['go2cart'] = 1;
		header ("Location: /checkout.php");
		exit();
	}


	// update cart
	if (isset($_POST["Update"]) && $_POST["Update"] != "")
	{
		unset($errs);
		$errs = array();

		unset($_SESSION['quantity']);

		$quantities = $_POST['quantity'];

		if (@is_array($quantities))
		{	
			foreach ($quantities as $k=>$v)
			{
				if ($v > 0)
				{
					$v = (int)$v;
					$_SESSION['quantity'][$k] = $v;

					$item_id = $k;
					$new_quantity = $v;

					if (!CheckCustomerLimit($item_id, $new_quantity, $userid))
					{
						$max_quantity = GetCustomerLimit($item_id);
						$_SESSION['quantity'][$k] = $max_quantity;
						
						$errs[] = "Sorry, quantity limit per customer for <span style='color: #888'>".GetDealName($item_id)."</span> is <span style='color: #888'>".$max_quantity."</span>";
					}
					elseif (!CheckInventory($item_id, $new_quantity))
					{
						$max_quantity = GetDealQuantity($item_id);
						$_SESSION['quantity'][$k] = $max_quantity;
						
						$errs[] = "Sorry, we have only <span style='color: #888'>".$max_quantity."</span> <span style='color: #8E8E8E'>".GetDealName($item_id)."</span> in stock.";
					}

					if (count($errs) == 0)
					{
						$_SESSION['quantity'][$k] = $v;
					}
					else
					{
						$allerrors = "";
						foreach ($errs as $errorname)
							$allerrors .= "&#155; ".$errorname."<br/>\n";
					}
				}
				else
				{
					$_SESSION['quantity'][$k] = 1;
				}
			}
		}
	}


	// add item to cart
	if (isset($_GET["action"]) && $_GET["action"] == "add" && isset($_GET['id']) && is_numeric($_GET['id']))
	{
		unset($errs);
		$errs = array();

		$item_id = (int)$_GET['id'];

		$check_item = mysql_num_rows(smart_mysql_query("SELECT * FROM abbijan_items WHERE item_id='$item_id' AND deal_type<>'affiliate' AND start_date<=NOW() AND end_date>NOW() AND status='active' ORDER BY title"));

		if (empty($_SESSION['cart_items'])) $_SESSION['cart_items'] = array();

		if ($check_item != 0)
		{
			if (!@in_array($item_id, $_SESSION['cart_items']))
			{
				$_SESSION['cart_items'][]		= $item_id;
				$_SESSION['cart_items']			= array_unique($_SESSION['cart_items']);
				$_SESSION['quantity'][$item_id]	= 1;
			}
			else
			{
				$new_quantity = $_SESSION['quantity'][$item_id] + 1;

				if (!CheckCustomerLimit($item_id, $new_quantity, $userid))
				{
					$max_quantity = GetCustomerLimit($item_id);
					$errs[] = "Sorry, quantity limit per customer for <span style='color: #888'>".GetDealName($item_id)."</span> is <span style='color: #888'>".$max_quantity."</span>";
				}
				elseif (!CheckInventory($item_id, $new_quantity))
				{
					$max_quantity = GetDealQuantity($item_id);
					$errs[] = "Sorry, we have only <span style='color: #888'>".$max_quantity."</span> <span style='color: #8E8E8E'>".GetDealName($item_id)."</span> in stock.";
				}

				if (count($errs) == 0)
				{
					$_SESSION['quantity'][$item_id] += 1;
				
					header ("Location: /cart.php");
					exit();
				}
				else
				{
					$allerrors = "";
					foreach ($errs as $errorname)
						$allerrors .= "&#155; ".$errorname."<br/>\n";
				}
			}
		}
	}


	// delete item from cart
	if (isset($_GET['del']) && is_numeric($_GET['del']))
	{
		$del_product_id = (int)$_GET['del'];

		if (count($_SESSION['cart_items']) > 0) 
		{
			if (@in_array($del_product_id, $_SESSION['cart_items']))
			{
				foreach($_SESSION['cart_items'] as $k => $v)
				{
					if ($del_product_id == $v) 
					{
						unset($_SESSION['cart_items'][$k]);
						unset($_SESSION['quantity'][$v]);
					}
				}
			}
		}

		header ("Location: /cart.php");
		exit();
	}


	$cart_items = $_SESSION['cart_items'];
	if (count($cart_items) == 0) $cart_items[] = "11111111111111111";
	$cart_items = array_map('intval', $cart_items);


	$query = "SELECT * FROM abbijan_items WHERE item_id IN (".@implode(",", $cart_items).") AND deal_type<>'affiliate' AND start_date<=NOW() AND end_date>NOW() AND status='active' ORDER BY title";
	$result = smart_mysql_query($query);
	$result2 = smart_mysql_query($query);
	$total = mysql_num_rows($result);
	$total2 = mysql_num_rows($result);


	if ($total2 > 0)
	{
		unset($subtotal, $TotalPrice);

		while ($row2 = mysql_fetch_array($result2))
		{
			$subtotal[$row2['item_id']]	= $row2['price']*$_SESSION['quantity'][$row2['item_id']];
			$TotalPrice					+= $subtotal[$row2['item_id']];
		}

		$OrderTotal			= $TotalPrice;
		$_SESSION['Total']	= DisplayPrice($OrderTotal,1);
	}
	else
	{
		unset($_SESSION['cart_items'], $_SESSION['quantity'], $_SESSION['Total']);
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Shopping Cart";

	require_once ("inc/header.inc.php");

?>

	<?php if ($total > 0) { ?>
	<table id="steps" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="first active" valign="top">
			<span class="number">1</span> <span class="title">Shopping Cart</span>
		</td>
		<td class=" next" valign="top">
			<span class="number">2 </span> <span class="title">Your Details</span>
		</td>
		<td class="" valign="top">
			<span class="number">3 </span> <span class="title">Payment Method</span>
		</td>
		<td class="" valign="top">
			<span class="number">4 </span> <span class="title">Checkout</span>
		</td>
		<td class="" valign="top">
			<span class="number">5 </span> <span class="title">Complete</span>
		</td>
	</tr>
	</table>
	<?php } ?>


	<h1>Shopping Cart</h1>

	<?php if ($total > 0 && CHECKOUT_MINUTES_LIMIT > 0) { ?>
		<p class="info">Please NOTE: Stock is not reserved until you complete checkout. You have <b><?php echo CHECKOUT_MINUTES_LIMIT; ?></b> minutes limit to complete your order.</p>
	<?php } ?>


	<?php if (isset($allerrors) || isset($_GET['msg'])) { ?>
	<div style="width: 94%;" class="error_msg">
		<?php
			switch ($_GET['msg'])
			{
				case "1": echo "Some items from your cart have been sold out."; break;
			}
			echo $allerrors;
		?>
	</div>
	<?php } ?>



<?php if ($total > 0) { ?>

	<div id="shopping-cart">

		<form action="" method="post">		
        <table align="center" width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<th width="10%"></th>
            <th width="45%">Item</th>
			<th width="15%">Price</th>
            <th width="13%">Quantity</th>
            <th width="15%">Total</th>
			<th width="7%"></th>
        </tr>
		<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
        <tr class="<?php if (($cc%2) == 0) echo "cart_row_even"; else echo "cart_row_odd"; ?>">
			<td valign="middle" align="center">
				<a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><img src="<?php echo IMAGES_URL.$row['thumb']; ?>" width="65" height="65" alt="<?php echo $row['title']; ?>" class="thumb" /></a>
            </td>
            <td valign="top" align="left">
				<a href="/deal_details.php?id=<?php echo $row['item_id']; ?>"><h2><?php echo $row['title']; ?></h2></a>
				<?php if ($row['customer_limit'] > 0) { ?><span class="customer_limit">Limit per customer: <?php echo $row['customer_limit']; ?></span><?php } ?>
			</td>
			<td valign="middle" align="center"><?php echo DisplayMoney($row['price']); ?></td>
            <td valign="middle" align="center"><input type="text" class="textbox" name="quantity[<?php echo $row['item_id']; ?>]" size="2" maxlength="3" value="<?php echo $_SESSION['quantity'][$row['item_id']]; ?>" /></td>
            <td valign="middle" align="center"><?php echo DisplayMoney($subtotal[$row['item_id']]); ?></td>
			<td valign="middle" align="center"><a href="/cart.php?del=<?php echo $row['item_id']; ?>" title="Delete"><img src="/images/delete.png" alt="Delete" /></a></td>
        </tr>
		<?php } ?>
        <tr>
			<td colspan="6" style="border-top: solid 1px #F7F7F7;" valign="middle" align="right">
				<input type="submit" class="update" name="Update" value="Update Cart" />
            </td>
        </tr>
		</table>

		<div class="cart_totals">
        <table align="right" border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td width="150" nowrap="nowrap" valign="middle" align="right">Subtotal:</td>
			<td nowrap="nowrap" valign="middle" align="right"><?php echo DisplayMoney($TotalPrice); ?></td>
		</tr>
        <tr>
			<td nowrap="nowrap" valign="middle" align="right"><b>TOTAL:</b></td>
			<td nowrap="nowrap" valign="middle" align="right"><span class="cart_total"><?php echo DisplayMoney($OrderTotal); ?> <?php echo SITE_CURRENCY_CODE; ?></span></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><small>Shipping cost and tax will be calculated during checkout.</small></td>
		</tr>
        </table>
		</div>

		<div style="clear: both"></div>
            
        <table style="border-top: 3px solid #F7F7F7;" align="center" width="100%" border="0" cellspacing="3" cellpadding="3">
        <tr>
			<td width="50%" valign="top" align="left">
				<input type="button" name="Continue Shopping" class="cancel" value="&#171; Continue Shopping" onClick="javascript:document.location.href='/'" />
			</td>
            <td width="50%" valign="top" align="right">
				<input type="submit" name="Checkout" class="submit" value="Checkout &#187;" />
			</td>
        </tr>
        </table>

		</form>

	</div>


     <?php }else{ ?>
		<div class="empty_cart">
			<h2>Your cart is empty.</h2>
			<p><a class="button" href="/">Continue shopping &raquo;</a></p>
		</div>
	 <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>