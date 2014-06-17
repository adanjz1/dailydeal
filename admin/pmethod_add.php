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


if (isset($_POST['action']) && $_POST['action'] == "addpmethod")
{
	unset($errs);
	$errs = array();

	$pmethod_title	 = mysql_real_escape_string(getPostParameter('pmethod_title'));
	$pmethod_details = mysql_real_escape_string(nl2br(getPostParameter('pmethod_details')));

	if(!($pmethod_title && $pmethod_details))
	{
		$errs[] = "Please fill in all required fields";
	}
	else
	{
		$check_query = smart_mysql_query("SELECT * FROM abbijan_payment_methods WHERE title='$pmethod_title'");
		if (mysql_num_rows($check_query) != 0)
		{
			$errs[] = "Sorry, payment method exists";
		}
	}

	if (count($errs) == 0)
	{
		$sql = "INSERT INTO abbijan_payment_methods SET pmethod_type='deposit', title='$pmethod_title', pmethod_details='$pmethod_details', status='active'";

		if (smart_mysql_query($sql))
		{
			header("Location: pmethods.php?msg=added");
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

	$title = "Add Payment Method";
	require_once ("inc/header.inc.php");

?>
 
        <h2>Add Payment Method</h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div style="width:60%;" class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>


        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td valign="top"><input type="text" name="pmethod_title" id="pmethod_title" value="" size="35" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">
				<span class="req">* </span>Payment Details:
				<br/><span class="help">user will need to provide<br/> this information to complete<br/> the money transfer</span>
			</td>
            <td valign="top"><textarea name="pmethod_details" cols="50" rows="7" class="textbox2"><?php echo getPostParameter('pmethod_details'); ?></textarea></td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="middle">
				<input type="hidden" name="action" id="action" value="addpmethod" />
				<input type="submit" name="add" id="add" class="submit" value="Add Payment Method" />
				&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='pmethods.php'" />
            </td>
          </tr>
        </table>
      </form>


<?php require_once ("inc/footer.inc.php"); ?>