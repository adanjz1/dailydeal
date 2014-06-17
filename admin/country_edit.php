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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_countries WHERE country_id='$id'";
		$rs = smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	if (isset($_POST["action"]) && $_POST["action"] == "edit")
	{
		unset($errors);
		$errors = array();
 
		$country_id		= (int)getPostParameter('country_id');
		$country_name	= mysql_real_escape_string(getPostParameter('country_name'));

		if (!($country_name && $country_name))
		{
			$errors[] = "Please fill in all required fields";
		}

		if (count($errors) == 0)
		{
			$sql = "UPDATE abbijan_countries SET name='$country_name' WHERE country_id='$country_id'";
			
			if (smart_mysql_query($sql))
			{
				header("Location: countries.php?msg=updated");
				exit();
			}
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}

	$title = "Edit Country";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Country</h2>

	<?php if ($total > 0) {
	
		$row = mysql_fetch_array($rs);

	?>

			<?php if (isset($errormsg) && $errormsg != "") { ?>
					<div style="width:55%;" class="error_box"><?php echo $errormsg; ?></div>
			<?php } ?>

      <form action="" method="post">
        <table cellpadding="2" cellspacing="5" border="0" align="center">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Country Name:</td>
            <td valign="top"><input type="text" name="country_name" id="country_name" value="<?php echo $row['name']; ?>" size="32" class="textbox" /></td>
			</tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
			  <input type="hidden" name="country_id" id="country_id" value="<?php echo (int)$row['country_id']; ?>" />
			  <input type="hidden" name="action" id="action" value="edit">
			  <input type="submit" class="submit" name="update" id="update" value="Update" />&nbsp;&nbsp;
              <input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='countries.php'" /></td>
            </tr>
          </table>
      </form>
      
	  <?php }else{ ?>
				<div class="info_box">Sorry, no country found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>