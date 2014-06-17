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


	$pn = (int)$_GET['pn'];


if (isset($_POST["action"]) && $_POST["action"] == "add_option")
{
		unset($errors);
		$errors = array();

		$item_id			= (int)getPostParameter('item_id');
		$option_name		= mysql_real_escape_string(getPostParameter('option_name'));
		$price				= mysql_real_escape_string(getPostParameter('price'));
		$quantity			= mysql_real_escape_string(getPostParameter('quantity'));
		$customer_limit		= mysql_real_escape_string(getPostParameter('customer_limit'));
		$description		= mysql_real_escape_string($_POST['description']);
		$required			= (int)getPostParameter('required');
		$status				= mysql_real_escape_string(getPostParameter('status'));


		if (!($deal_title && $retail_price && $price && $description && $status))
		{
			$errors[] = "Please enter option name and option value";
		}
		else
		{
			if (!(is_numeric($price) && $price > 0))
			{
				$errors[] = "Please enter correct deal price";
				$price = "0.00";
			}

			if (isset($quantity)  && $quantity != "" && !(is_numeric($quantity) && $quantity >= 0))
			{
				$errors[] = "Please enter correct quantity";
			}

			if ($main_deal == 1)
			{
				$check_query = smart_mysql_query("SELECT * FROM abbijan_items WHERE item_id<>'$item_id' AND start_date<=NOW() AND end_date>NOW() AND main_deal='1' AND status='active'");
				if (mysql_num_rows($check_query) != 0)
				{
					$errors[] = "Sorry, you can mark just one deal as main.";
				}
			}
		}


		if (count($errors) == 0)
		{
			smart_mysql_query("UPDATE abbijan_items SET title='$deal_title', deal_type='$deal_type', url='$affiliate_link', quantity='$quantity', retail_price='$retail_price', price='$price', discount='$discount', customer_limit='$customer_limit', start_date='$sale_start_date', end_date='$sale_end_date', conditions='$condition', brief_description='$brief_description', description='$description', featured='$featured', main_deal='$main_deal', allow_comments='$allow_comments', status='$status' WHERE item_id='$item_id'");

			$insert_sql = "INSERT INTO abbijan_item_options SET item_id='$item_id', option_id='', option_value='', required='', category_id='".(int)$cat_id."'";
					smart_mysql_query($insert_sql);

			if (count($category) > 0)
			{			
				foreach ($options as $option_id)
				{
					$insert_sql = "INSERT INTO abbijan_item_option_values SET item_option_id='', item_id='', option_id='', option_value_id='', quantity='', substract='', price='', price_prefix=''";
					smart_mysql_query($insert_sql);
				}
			}

			header("Location: deals.php?msg=updated");
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
		$id	= (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_items WHERE item_id='$id' LIMIT 1";
		$rs	= smart_mysql_query($query);
		$total = mysql_num_rows($rs);

		$options_query = "SELECT * FROM abbijan_item_options ORDER BY option_id DESC";
		$options_result = smart_mysql_query($options_query);
		$options_total = mysql_num_rows($options_result);
	}


	$title = "Deal Options";
	require_once ("inc/header.inc.php");

?>


	<div id="addnew"><a class="addnew" id="show" href="#options">Add Option</a></div>

    <h2>Deal Options</h2>

	<?php if ($total > 0) {
		
		$row = mysql_fetch_array($rs);

	?>

		<?php if ($row['thumb'] != "") { ?>
			<div style="float: right"><img src="<?php echo IMAGES_URL.$row['thumb']; ?>" width="65" height="65" alt="<?php echo $row['title']; ?>" class="thumb" /></div>
		<?php } ?>

        <?php if ($options_total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added": echo "Deal has been successfully added!"; break;
						case "updated": echo "Deal has been successfully updated!"; break;
						case "deleted": echo "Deal has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>

		<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
		<td nowrap="nowrap" valign="middle" align="left" width="50%">
            <form id="form1" name="form1" method="get" action="">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>>Recently Added</option>
			<option value="title" <?php if ($_GET['column'] == "title") echo "selected"; ?>>Title</option>
			<option value="end_date" <?php if ($_GET['column'] == "end_date") echo "selected"; ?>>End Soonest</option>
			<option value="price" <?php if ($_GET['column'] == "price") echo "selected"; ?>>Price</option>
			<option value="discount" <?php if ($_GET['column'] == "discount") echo "selected"; ?>>Discount</option>
			<option value="sales" <?php if ($_GET['column'] == "sales") echo "selected"; ?>>Sales</option>
			<option value="views" <?php if ($_GET['column'] == "views") echo "selected"; ?>>Popularity</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
		  <input type="hidden" name="page" value="<?php echo $page; ?>" />
		  &nbsp;&nbsp;View: 
          <select name="show" id="order" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
          </select>
            </form>
			</td>
			<td nowrap="nowrap" width="30%" valign="middle" align="left">
				<div class="admin_filter">
					<input type="text" name="filter" value="<?php echo $filter; ?>" class="textbox" size="30" /> <input type="submit" class="submit" value="Search" />
					<?php if (isset($filter) && $filter != "") { ?><a title="Cancel Search" href="deals.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Cancel Search" /></a><?php } ?> 
				</div>
			</td>
			<td nowrap="nowrap" valign="middle" width="33%" align="right">
				Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>

			<form id="form2" name="form2" method="post" action="">
			<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="7%">ID</th>
				<th width="35%">Title</th>
				<th width="9%">Price</th>
				<th width="9%">Options</th>
				<th width="9%">Sales</th>
				<th width="10%">Start Date</th>
				<th width="10%">End Date</th>
				<th width="10%">Status</th>
				<th width="10%">Actions</th>
			</tr>
			<?php while ($options_row = mysql_fetch_array($options_result)) { $cc++; ?>				  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td nowrap="nowrap" align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['item_id']; ?>]" id="id_arr[<?php echo $row['item_id']; ?>]" value="<?php echo $row['item_id']; ?>" /></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['item_id']; ?></td>
					<td align="left" valign="middle">
						<a href="deal_details.php?id=<?php echo $row['item_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>">
							<?php if (strlen($row['title']) > 100) echo substr($row['title'], 0, 100)."..."; else echo $row['title']; ?>
						</a>
						<?php if ($row['main_deal'] == 1) { ?><span class="main_deal" alt="Main Deal" title="Main Deal"></span><?php } ?>
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="Featured" title="Featured"></span><?php } ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo DisplayMoney($row['price']); ?></td>
					<td nowrap="nowrap" align="center" valign="middle"><a href="deal_options.php?id=<?php echo $row['item_id']; ?>"><?php echo GetDealOptionsTotal($row['item_id']); ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo GetDealSalesTotal($row['item_id']); ?></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['date_start']; ?></td>
					<td nowrap="nowrap" align="center" valign="middle" title="Ends in <?php echo GetTimeLeft($row['time_left']); ?>"><?php echo $row['date_end']; ?></td>
					<td style="padding-left:7px;" nowrap="nowrap" align="left" valign="middle">
					<?php
						switch ($row['status'])
						{
							case "active": echo "<span class='active_s'>".$row['status']."</span>"; break;
							case "inactive": echo "<span class='inactive_s'>".$row['status']."</span>"; break;
							case "expired": echo "<span class='expired_status'>".$row['status']."</span>"; break;
							case "sold": echo "<span class='sold_status'>sold out</span>"; break;
							default: echo "<span class='default_status'>".$row['status']."</span>"; break;
						}
					?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="deal_details.php?id=<?php echo $row['item_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="deal_edit.php?id=<?php echo $row['item_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this deal') )location.href='deal_delete.php?id=<?php echo $row['item_id']; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>&pn=<?php echo $page?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
				<td colspan="10" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
				</tr>
            </table>
			</form>

          <?php }else{ ?>
					<div class="info_box">There are no deal options for <b><?php echo $row['title']; ?><b>.</div>
          <?php } ?>
	

			<br/>
			<div id="options" style="width: 370px; background: #F9F9F9; border: 1px dotted #EEE; padding: 10px; margin: 0 auto;  <?php if (!isset($_POST['action'])) { ?>display: none;<?php } ?>" >
			<p style="float: right"><a id="hide" href="#options">x close</a></p>

			<h3>Add Option</h3>

			<?php if (isset($errormsg) && $errormsg != "") { ?>
				<div style="margin: 0 auto; width: 87%;" class="error_box"><?php echo $errormsg; ?></div>
			<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
				<div style="margin: 0 auto; width: 87%;" class="success_box">Option has been successfully added</div>
			<?php } ?>

			<form action="" method="post" name="form1">
			<table cellpadding="2" cellspacing="3" border="0">
			<tr>
				<td align="left" valign="middle">
					Option Name: <input type="text" name="option_name" size="35" placeholder="size, color, etc." required="required" value="<?php echo getPostParameter('option_name'); ?>" class="textbox" /> <input type="checkbox" name="required" value="1" <?php if ($required == 1) echo 'checked="checked"'; ?> class="checkboxx" /> Required
				</td>
			</tr>
			<tr>
			<td>
			<table width="360" cellpadding="2" cellspacing="3" border="0">
			<tr>
				<th>Option value</th>
				<th>Quantity<br/><small>0 = unlim</small></th>
				<th>Price</th>
			</tr>
			<?php for ($i=0; $i<7; $i++) { ?>
			<tr>
				<td align="center" valign="top"><input type="text" name="product_option[1][product_option_value][$i][name]" value="<?php echo getPostParameter('ddd'); ?>" size="30" class="textbox" /></td>
				<td align="center" valign="top"><input type="text" name="customer_limit" id="customer_limit" value="<?php echo (isset($customer_limit)) ? getPostParameter('dddd') : "0" ?>" size="5" class="textbox" /></td>
				<td align="center" valign="top">
					<select name="product_option[1][product_option_value][$i][price_prefix]">
						<option value="+">+</option>
						<option value="-">-</option>
					</select>
					<?php echo SITE_CURRENCY; ?><input type="text" name="product_option[1][product_option_value][$i][price]" value="<?php echo getPostParameter('ddddd'); ?>" size="7" class="textbox" />
				</td>
			</tr>
			<?php } ?>
			</table>
			</td>
			</tr>
            <tr>
				<td align="center" colspan="3" valign="bottom">
					<input type="hidden" name="action" id="action" value="add_option">
					<input type="submit" class="submit" name="add" id="add" value="Add Option" />
				</td>
            </tr>
			</table>
			</form>	
			</div>

			<script>
			$("#show").click(function () {
			  $("#options").show("fast");
			});
			$("#hide").click(function () {
			  $("#options").hide();
			});
			</script>

			<!--
			<table id="option-value10" class="list" style="border: 1px solid #eee">  	 <thead>      <tr>        <td class="left">Option Value:</td>        <td class="right">Quantity:</td>        <td class="left">Subtract Stock:</td>        <td class="right">Price:</td>        <td class="right">Weight:</td>        <td></td>      </tr>  	 </thead>    <tbody id="option-value-row11">  <tr>    <td class="left"><select name="product_option[10][product_option_value][11][option_value_id]">  <option value="40">Blue</option>  <option value="41">Green</option>  <option value="39">Red</option>  <option value="42">Yellow</option>      </select><input name="product_option[10][product_option_value][11][product_option_value_id]" value="" type="hidden"></td>    <td class="right"><input name="product_option[10][product_option_value][11][quantity]" value="" size="3" type="text"></td>    <td class="left"><select name="product_option[10][product_option_value][11][subtract]">      <option value="1">Yes</option>      <option value="0">No</option>    </select></td>    <td class="right"><select name="product_option[10][product_option_value][11][price_prefix]">      <option value="+">+</option>      <option value="-">-</option>    </select>    <input name="product_option[10][product_option_value][11][price]" value="" size="5" type="text"></td>    <td class="right">   <td class="right"><select name="product_option[10][product_option_value][11][weight_prefix]">      <option value="+">+</option>      <option value="-">-</option>    </select>    <input name="product_option[10][product_option_value][11][weight]" value="" size="5" type="text"></td>    <td class="left"><a onclick="$('#option-value-row11').remove();" class="button">Remove</a></td>  </tr></tbody><tfoot>      <tr>        <td colspan="6"></td>        <td class="left"><a onclick="addOptionValue(10);" class="button">Add Option Value</a></td>      </tr>    </tfoot>  </table>
			-->

			<br/>
			<p align="center"><a class="goback" href="deals.php">Go Back</a></p>

      <?php }else{ ?>
				<div class="info_box">Sorry, no deal found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>




<?php require_once ("inc/footer.inc.php"); ?>