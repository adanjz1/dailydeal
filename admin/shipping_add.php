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


if (isset($_POST['action']) && $_POST['action'] == "add_shipping")
{
	unset($errs);
	$errs = array();

	$method_title	= mysql_real_escape_string(getPostParameter('method_title'));
	$cost			= mysql_real_escape_string(getPostParameter('cost'));
	$free_cost		= mysql_real_escape_string(getPostParameter('free_cost'));
	$delivery_time	= mysql_real_escape_string(getPostParameter('time'));
	$countries		= array();
	$countries		= $_POST['country'];
	$description	= mysql_real_escape_string(nl2br(getPostParameter('description')));

	if(!($method_title && $cost))
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

		$sql = "INSERT INTO abbijan_shipping_methods SET title='$method_title', countries='$delivery_countries', delivery_time='$delivery_time', cost='$cost', free_shipping_cost='$free_cost', description='$description', status='active'";

		if (smart_mysql_query($sql))
		{
			header("Location: shipping.php?msg=added");
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

	$title = "Add Shipping Method";
	require_once ("inc/header.inc.php");

?>
 
        <h2>Add Shipping Method</h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div style="width:60%;" class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>


        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td valign="top"><input type="text" name="method_title" id="method_title" value="<?php echo getPostParameter('method_title'); ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Delivery fee:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="cost" id="cost" value="<?php echo getPostParameter('cost'); ?>" size="7" class="textbox" /></td>
          </tr>
          <tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Free Delivery from:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="free_cost" id="free_cost" value="<?php echo getPostParameter('free_cost'); ?>" size="7" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Countries:</td>
            <td valign="top">
				<div class="scrollbox">
				<div class="even"><input type="checkbox" name="country[]" value="all" <?php echo ($country == "all") ? "checked='checked'" : ""; ?>>All Countries</div>
				<?php

					$countries_query = "SELECT * FROM abbijan_countries ORDER BY name";
					$countries_result = smart_mysql_query($countries_query);

					if (mysql_num_rows($countries_result) > 0)
					{
						while ($countries_row = mysql_fetch_array($countries_result))
						{
							$cc++;
							if (is_array($countries) && in_array($countries_row['country_id'], $countries)) $checked = 'checked="checked"'; else $checked = '';

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
            <td valign="top"><input type="text" name="time" id="time" value="<?php echo getPostParameter('time'); ?>" size="15" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Description:</td>
            <td valign="top"><textarea name="description" cols="57" rows="5" class="textbox2"><?php echo getPostParameter('description'); ?></textarea></td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="middle">
				<input type="hidden" name="action" id="action" value="add_shipping" />
				<input type="submit" name="add" id="add" class="submit" value="Add Shipping Method" />
				&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='shipping.php'" />
		  </td>
          </tr>
        </table>
      </form>


<?php require_once ("inc/footer.inc.php"); ?>