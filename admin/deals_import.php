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
	require_once("./inc/parsecsv.inc.php");
	require_once("./inc/adm_functions.inc.php");


if (isset($_POST["action"]) && $_POST["action"] == "import")
{
	unset($errors);
	$errors = array();

	$network_id	= intval(getPostParameter('network_id'));
	$delimiter = getPostParameter('delimiter');

	if (!($network_id && $_FILES['csv_file']['tmp_name']))
	{
		$errors[] = "Please select affiliate network and CSV-report file";
	}
	elseif (!$delimiter)
	{
		$errors[] = "Please select delimiter";
	}
	else
	{
		$csv_file	= $_FILES['csv_file']['name'];

		if (preg_match('/\\.(csv)$/i', $csv_file) != 1)
		{
			$errors[] = "Please upload a CSV-report with the extension .csv";
			@unlink($_FILES['photo']['tmp_name']);
		}
		elseif ($_FILES['csv_file']['size'] > 52428800)
		{
			$errors[] = "The file size is too big. It exceeds 50Mb.";
		}

		$aff_result = smart_mysql_query("SELECT * FROM abbijan_affnetworks WHERE network_id='$network_id' AND status='active' LIMIT 1");
		$aff_row = mysql_fetch_array($aff_result);

		$network_csv_format = stripslashes($aff_row['csv_format']);

		$row_transactionID	= "{TRANSACTIONID}";
		$row_programID		= "{PROGRAMID}";
		$row_userID			= "{USERID}";
		$row_amount			= "{AMOUNT}";
		$row_commission		= "{COMMISSION}";
		$row_status			= "{STATUS}";

		if (!(strstr($network_csv_format, $row_transactionID) && strstr($network_csv_format, $row_programID) && strstr($network_csv_format, $row_userID) && strstr($network_csv_format, $row_amount) && strstr($network_csv_format, $row_commission) && strstr($network_csv_format, $row_status) && $aff_row['confirmeds'] && $aff_row['pendings']))
		{
			$errors[] = "Sorry, you have wrong CSV format value.";
		}
	}

	if (count($errors) == 0)
	{
		$csv = new parseCSV();
		
		$csv->delimiter = $delimiter;
		$separator = $csv->delimiter;

		$csv->parse($_FILES['csv_file']['tmp_name']);

		if (!isset($separator) || $separator == "")
		{
			header("Location: csv_import.php?err=delimiter");
			exit();
		}

		$network_csv_format = explode($separator, $network_csv_format);

		foreach ($network_csv_format as $k=>$value)
		{
			switch ($value)
			{
				case strstr($value, "{TRANSACTIONID}") == true:		$trans_id = $k; break;
				case strstr($value, "{PROGRAMID}") == true:			$program_id = $k; break;
				case strstr($value, "{USERID}") == true:			$sub_id = $k; break;
				case strstr($value, "{AMOUNT}") == true:			$amount_id = $k; break;
				case strstr($value, "{COMMISSION}") == true:		$commission_id = $k; break;
				case strstr($value, "{STATUS}") == true:			$status_id = $k; break;
			}
		}

		foreach ($csv->data as $key => $row)
		{
			$new_row = array_values($row);
			
			$transaction_id_e	= $new_row[$trans_id];
			$program_id_e		= $new_row[$program_id];
			$subid_e			= $new_row[$sub_id];
			$amount_e			= $new_row[$amount_id];
			$commission_e		= $new_row[$commission_id];
			$status_e			= $new_row[$status_id];

			if (!is_numeric($amount_e) || !is_numeric($commission_e))
			{
				header("Location: deals_import.php?err=amount");
				exit();
			}


			if (!empty($abbijan_status))
			{
				$cashback_result = smart_mysql_query("SELECT cashback FROM abbijan_retailers WHERE network_id='$network_id' AND program_id='$program_id_e' LIMIT 1");
				$cashback_row = mysql_fetch_array($cashback_result);
				$cashback = $cashback_row['cashback'];

				if ($cashback != "")
				{
					if (strstr($cashback, '%'))
					{
						$cashback_percent = str_replace('%','',$cashback);
						$member_money = CalculatePercentage($amount_e, $cashback_percent);
					}
					else
					{
						if ($commission_e < $cashback)
						{
							$member_money = $cashback;
							$abbijan_status = "incomplete";
							$reason = "too hight cashback value";
						}
						else
						{
							$member_money = $cashback;
						}
					}

					if ($abbijan_status == "unknown")
					{
						$abbijan_status = "incomplete";
						$reason = "unknown transaction status";
					}
			
					$check_deal_result = smart_mysql_query("SELECT * FROM abbijan_items WHERE item_id='$item_id' AND title='$title' AND price='$price'");

					if (mysql_num_rows($check_deal_result) != 0)
					{
						$deal_query = "UPDATE abbijan_item SET price='$price', status='$abbijan_status', reason='$reason', updated=NOW() WHERE item_id='$item_id'";
					}
					else
					{
						$deal_query = "INSERT INTO abbijan_items SET item_id='$item_id', program_id='$program_id_e', price='$price', status='active', added=NOW(), updated=NOW()";
					}

					smart_mysql_query($deal_query);
				}
			}
		}

		smart_mysql_query("UPDATE abbijan_affnetworks SET last_csv_upload=NOW() WHERE network_id='$network_id'");

		header("Location: csv_import.php?msg=done");
		exit();
	}
	else
	{
		$errormsg = "";
		foreach ($errors as $errorname)
			$errormsg .= "&#155; ".$errorname."<br/>";
	}
}

	$cc = 0;

	$title = "Import Deals";
	require_once ("inc/header.inc.php");

?>

    <h2>Import Deals</h2>

	<p>From here you can import deals. Simply select you CSV file and click "Import".</p>


	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div style="margin: 0 auto; width: 100%;" class="error_box"><?php echo $errormsg; ?></div>
	<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "imported") { ?>
		<div style="margin: 0 auto; width: 100%;" class="success_box">Deals have been successfully imported. Imported deal: <?php echo $imported_total; ?></div>
	<?php } ?>

      <form action="" method="post" name="form1" enctype="multipart/form-data">
        <table bgcolor="#F9F9F9" width="100%" cellpadding="2" cellspacing="5" border="0" align="center">
          <tr id="affiliate_link" <?php if ($deal_type != "affiliate") { ?>style="display: none;" <?php } ?>>
			<td valign="middle" align="right" class="tb1"><span class="req">* </span>Affiliate Link:</td>
			<td valign="top"><input type="text" name="affiliate_link" value="<?php echo $affiliate_link; ?>" size="60" class="textbox" />&nbsp; <small class="note">Your affiliate link</small></td>
          </tr>
			<tr>
				<td valign="middle" align="right" class="tb1"><span class="req">* </span>CSV file:</td>
				<td valign="top"><input type="file" name="import_file" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="tb1"><span class="req">* </span>CSV format:</td>
				<td valign="top"><textarea name="csv_format" cols="120" rows="2" class="textbox2"><?php echo getPostParameter('csv_format'); ?></textarea></td>
            </tr>
			<tr>
				<td valign="top" align="right" class="tb1">CSV format example:</td>
				<td bgcolor="#F5F5F5" style="padding: 5px; color: #777; font-size: 10px;" valign="top">{ID},{TITLE},{PRICE},{RETAIL_PRICE},{IMAGE},{BRIEF_DESCRIPTION},{DESCRIPTION},{START_DATE},{END_DATE},{QUANTITY}</td>
            </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Discount:</td>
            <td valign="top"><input type="text" name="discount" value="<?php echo getPostParameter('discount'); ?>" size="3" class="textbox" />%</td>
          </tr>
            <tr>
				<td valign="middle" align="left" class="tb1">&nbsp;</td>
				<td valign="top"><b>Default values</b></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Quantity:</td>
				<td valign="middle"><input type="text" name="quantity" id="quantity" value="<?php echo (isset($quantity)) ? getPostParameter('quantity') : "0" ?>" size="2" class="textbox" /><span class="note">0 means unlimited</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Limit per customer:</td>
				<td valign="middle"><input type="text" name="customer_limit" id="customer_limit" value="<?php echo (isset($customer_limit)) ? getPostParameter('customer_limit') : "0" ?>" size="2" class="textbox" /><span class="note">0 means no limit</span></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Condition:</td>
				<td valign="middle"><input type="text" name="condition" id="condition" value="<?php echo getPostParameter('condition'); ?>" size="10" class="textbox" /><span class="note">Used, New, etc</span></td>
            </tr>

            <tr>
				<td valign="middle" align="right" class="tb1">Test mode</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="featured" value="1" <?php if (getPostParameter('featured') == 1) echo "checked=\"checked\""; ?> /></td>
            </tr>
            <tr>
				<td align="center" colspan="2" valign="bottom">
					<input type="hidden" name="action" id="action" value="import">
					<input type="submit" class="submit" name="add" id="add" value="Import!" />
				</td>
            </tr>
          </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>