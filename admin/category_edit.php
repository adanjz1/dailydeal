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

		$query = "SELECT * FROM abbijan_categories WHERE category_id='$id'";
		$rs = smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	if (isset($_POST["action"]) && $_POST["action"] == "edit")
	{
		unset($errors);
		$errors = array();
 
		$catid					= (int)getPostParameter('catid');
		$category_name			= mysql_real_escape_string(getPostParameter('catname'));
		$category_description	= mysql_real_escape_string(nl2br(getPostParameter('description')));		

		if (!($category_name && $catid))
		{
			$errors[] = "Please ensure that all fields marked with an asterisk are complete";
		}

		if (count($errors) == 0)
		{
			smart_mysql_query("UPDATE abbijan_categories SET name='$category_name', description='$category_description' WHERE category_id='$catid'");

			header("Location: categories.php?msg=updated");
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}


	$title = "Edit Category";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Category</h2>

	<?php if ($total > 0) {
	
		$row = mysql_fetch_array($rs);

	?>

		<?php if (isset($errormsg) && $errormsg != "") { ?>
				<div style="width:75%;" class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

      <form action="" method="post">
        <table cellpadding="2" cellspacing="5" border="0" align="center">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td width="25%" valign="middle" align="right" class="tb1"><span class="req">* </span>Category Name:</td>
            <td width="75%" valign="top"><input type="text" name="catname" id="catname" value="<?php echo $row['name']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Description:</td>
			<td align="left" valign="top"><textarea name="description" cols="50" rows="7" class="textbox2"><?php echo strip_tags($row['description']); ?></textarea></select>
			</td>
          </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
			  <input type="hidden" name="catid" id="catid" value="<?php echo (int)$row['category_id']; ?>" />
			  <input type="hidden" name="action" id="action" value="edit">
			  <input type="submit" class="submit" name="update" id="update" value="Update" />&nbsp;&nbsp;
              <input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='categories.php'" /></td>
            </tr>
          </table>
      </form>
      
	  <?php }else{ ?>
				<div class="info_box">Sorry, no category found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>