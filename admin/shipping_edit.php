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


if (isset($_POST['action']) && $_POST['action'] == "edit_shipping")
{
	unset($errs);
	$errs = array();

	$shipping_method_id	= (int)getPostParameter('smethodid');
	$method_title		= mysql_real_escape_string(getPostParameter('method_title'));
	$cost				= mysql_real_escape_string(getPostParameter('cost'));
	$free_cost			= mysql_real_escape_string(getPostParameter('free_cost'));
	$delivery_time		= mysql_real_escape_string(getPostParameter('time'));
	$countries			= array();
	$countries			= $_POST['country'];
	$description		= mysql_real_escape_string(nl2br(getPostParameter('description')));
	$status				= mysql_real_escape_string(getPostParameter('status'));


	if(!($method_title && $cost && $status))
	{
		$errs[] = "Please fill in all required fields";
	}
	elseif (count($countries) == 0)
	{
		$errs[] = "Please select countries";
	}
	else
	{
		if (!(is_numeric($cost) && $cost > 0))
		{
			$errors[] = "Please enter delivery fee";
			$price = "0.00";
		}
	}

	if (count($errs) == 0)
	{
		if (count($countries) > 0)
		{
			$delivery_countries = implode("//", $countries);
		}

		$sql = "UPDATE abbijan_shipping_methods SET title='$method_title', countries='$delivery_countries', delivery_time='$delivery_time', cost='$cost', free_shipping_cost='$free_cost', description='$description', status='$status' WHERE shipping_method_id='$shipping_method_id' LIMIT 1";

		if (smart_mysql_query($sql))
		{
			header("Location: shipping.php?msg=updated");
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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$shipping_method_id = (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_shipping_methods WHERE shipping_method_id='$shipping_method_id' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit Shipping Method";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2><?php echo $row['title']; ?></h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div style="width:60%;" class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td valign="top"><input type="text" name="method_title" id="method_title" value="<?php echo $row['title']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Delivery fee:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="cost" id="cost" value="<?php echo $row['cost']; ?>" size="5" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Free Delivery from:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="free_cost" id="free_cost" value="<?php echo ($row['free_shipping_cost'] != "0.0000") ? $row['free_shipping_cost'] : ""; ?>" size="5" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Countries:</td>
            <td valign="top">
				<div class="scrollbox">
				<div class="even"><input type="checkbox" name="country[]" value="all" <?php echo (strstr($row['countries'], 'all')) ? "checked='checked'" : ""; ?>>All Countries</div>
				<?php

					unset($s_countries);
					$s_countries = array();

					$s_countries = explode("//", $row['countries']);

					$countries_query = "SELECT * FROM abbijan_countries where active=1 ORDER BY name";
					$countries_result = smart_mysql_query($countries_query);

					if (mysql_num_rows($countries_result) > 0)
					{
						while ($countries_row = mysql_fetch_array($countries_result))
						{
							$cc++;
							if (is_array($s_countries) && in_array($countries_row['country_id'], $s_countries)) $checked = 'checked="checked"'; else $checked = '';

							if (($cc%2) == 0)
								echo "<div class=\"even\"><input type=\"checkbox\" name=\"country[]\" value=\"".(int)$countries_row['country_id']."\" ".$checked.">".$countries_row['name']."</div>";
							else
								echo "<div class=\"odd\"><input type=\"checkbox\" name=\"country[]\" value=\"".(int)$countries_row['country_id']."\" ".$checked.">".$countries_row['name']."</div>";
						}
					}

				?>
				</div>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Delivery time:</td>
            <td valign="top"><input type="text" name="time" id="time" value="<?php echo $row['delivery_time']; ?>" size="15" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Description:</td>
            <td valign="top"><textarea name="description" cols="57" rows="5" class="textbox2"><?php echo strip_tags($row['description']); ?></textarea></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="smethodid" id="smethodid" value="<?php echo (int)$row['shipping_method_id']; ?>" />
			<input type="hidden" name="action" id="action" value="edit_shipping" />
			<input type="submit" name="save" id="save" class="submit" value="Update" />
			&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='shipping.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no shipping method found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>