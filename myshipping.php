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
	require_once("inc/pagination.inc.php");

	$results_per_page = 10;
	$cc = 0;

	$shipping_id = (int)$_GET['id'];

	// delete from favorites
	if (isset($_GET['act']) && $_GET['act'] == "del")
	{
		$del_query = "DELETE FROM abbijan_shipping WHERE user_id='$userid' AND shipping_id='$shipping_id'";
		if (smart_mysql_query($del_query))
		{
			header("Location: myshipping.php?msg=deleted");
			exit();
		}
	}

	if (isset($_POST['action']) && $_POST['action'] == "edit_shipping")
	{
		unset($errs);
		$errs = array();

		$shipping_id = (int)getPostParameter('shipping_id');
		$shipping_name = mysql_real_escape_string(getPostParameter('shipping_name'));
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
			$errs[] = "Please fill in all required fields";
		}

		if (count($errs) == 0)
		{
			$up_query = "UPDATE abbijan_shipping SET shipping_name='$shipping_name', fname='$fname', lname='$lname', address='$address', address2='$address2', city='$city', state='$state', zip='$zip', country='$country', phone='$phone', modified=NOW() WHERE shipping_id='$shipping_id' AND user_id='$userid' LIMIT 1";
	
			if (smart_mysql_query($up_query))
			{
				header("Location: myshipping.php?msg=updated");
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


	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$query = "SELECT * FROM abbijan_shipping WHERE user_id='$userid' ORDER BY shipping_id DESC LIMIT ".$from.",".$results_per_page;
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	
	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Shipping Address Book";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">

          <h1>Shipping Address Book</h1>


		  <?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width: 440px;" class="success_msg">
				<?php
					switch ($_GET['msg'])
					{
						case "updated": echo "Shipping address has been updated successfully"; break;
						case "deleted": echo "Shipping address has been deleted"; break;
					}
				?>
			</div>
		<?php } ?>


		<?php
			
			if (isset($_GET['act']) && $_GET['act'] == "edit")
			{

				$shipping_query	= "SELECT * FROM abbijan_shipping WHERE shipping_id='$shipping_id' AND user_id='$userid' LIMIT 1";
				$shipping_result = smart_mysql_query($shipping_query);
				$shipping_total	= mysql_num_rows($shipping_result);

				if ($shipping_total > 0)
				{
					$shipping_row = mysql_fetch_array($shipping_result);
		?>

			<?php if (isset($allerrors)) { ?>
				<div style="width: 94%;" class="error_msg"><?php echo $allerrors; ?></div>
			<?php } ?>

			<form action="" method="post">
			<table width="400" align="center" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td width="120" nowrap="nowrap" align="right" valign="middle"><span class="req">* </span>Title:</td>
				<td align="left" valign="top"><input type="text" id="shipping_name" name="shipping_name" size="25" value="<?php echo $shipping_row['shipping_name']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>First Name:</td>
				<td align="left" valign="top"><input type="text" id="fname" name="fname" size="25" value="<?php echo $shipping_row['fname']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>Last Name:</td>
				<td align="left" valign="top"><input type="text" id="lname" name="lname" size="25" value="<?php echo $shipping_row['lname']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>Address:</td>
				<td align="left" valign="top"><input type="text" id="address" name="address" size="25" value="<?php echo $shipping_row['address']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle">Address Line 2:</td>
				<td align="left" valign="top"><input type="text" id="address2" name="address2" size="25" value="<?php echo $shipping_row['address2']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>Country:</td>
				<td align="left" valign="top">
					<select name="country" class="textbox2" id="country" style="width: 170px;">
						<option value="">--- Select country ---</option>
						<?php
							$sql_country = "SELECT * FROM abbijan_countries where active=1 ORDER BY name ASC";
							$rs_country = smart_mysql_query($sql_country);
							$total_country = mysql_num_rows($rs_country);

							if ($total_country > 0)
							{
								while ($row_country = mysql_fetch_array($rs_country))
								{
									if ($shipping_row['country'] == $row_country['name'])
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
				<td align="left" valign="top"><input type="text" id="city" name="city" size="25" value="<?php echo $shipping_row['city']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>State:</td>
				<td align="left" valign="top"><input type="text" id="state" name="state" size="25" value="<?php echo $shipping_row['state']; ?>" class="textbox" />
				</td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>Zip Code:</td>
				<td align="left" valign="top"><input type="text" id="zip" name="zip" size="25" value="<?php echo $shipping_row['zip']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>Phone:</td>
				<td align="left" valign="top"><input type="text" id="phone" name="phone" size="25" value="<?php echo $shipping_row['phone']; ?>" class="textbox" /></td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="center">
					<input type="hidden" name="shipping_id" value="<?php echo $shipping_row['shipping_id']; ?>" />
					<input type="hidden" name="action" value="edit_shipping" />
					<input type="submit" class="submit" name="update" value="Update" />
					&nbsp;&nbsp;
					<input type="button" name="cancel" class="cancel" value="Cancel" onClick="javascript:document.location.href='/myshipping.php'" />
				</td>
			</tr>
			</table>
			</form>

			<?php }else{ ?>
				<p align="center">Sorry, no shipping address found.</p>
				<p align="center"><a class="goback" href="/myshipping.php">Go Back</a></p>
			<?php } ?>

		<?php }else{ ?>

			<?php if ($total > 0) { ?>

			<p align="center">From here you can manage your shipping addresses.</p>

			<table class="brd" width="500" align="center" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<th width="85%">Address</th>
				<th width="15%">Actions</th>
			</tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
			<tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<td valign="middle" align="left">
				<div class="shipping-address">
					<b><?php echo $row['shipping_name']; ?></b><br/>
					<?php echo $row['fname']." ".$row['lname']; ?><br/>
					<?php echo $row['address']; ?>,
					<?php if ($row['address2'] != "") echo $row['address2'].","; ?>
					<?php echo $row['city'].", ".$row['state']." ".$row['zip']; ?>,<?php echo $row['country']; ?><br/>
					<img src="/images/icon_phone.png" align="absmiddle" /> <?php echo $row['phone']; ?>
				</div>
				</td>
				<td nowrap="nowrap" valign="middle" align="center">
					<a href="/myshipping.php?act=edit&id=<?php echo $row['shipping_id']; ?>" title="Edit"><img src="images/icon_edit.png" border="0" alt="Edit" /></a>
					<a href="#" onclick="if (confirm('Are you sure you really want to delete this shipping address?') )location.href='/myshipping.php?act=del&id=<?php echo $row['shipping_id']; ?>'" title="Delete"><img src="images/icon_delete.png" border="0" alt="Delete" /></a>
				</td>
			</tr>
			<?php } ?>
			</table>

				<?php echo ShowPagination("shipping",$results_per_page,"myshipping.php?","WHERE user_id='$userid'"); ?>

		 <?php }else{ ?>
					<p align="center">You have not saved shipping addresses at this time.</p>
		 <?php } ?>

	 <?php } ?>

</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>