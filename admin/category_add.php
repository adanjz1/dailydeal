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


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		$category_name			= mysql_real_escape_string(getPostParameter('catname'));
		$category_description	= mysql_real_escape_string(nl2br(getPostParameter('description')));

		if (!($category_name))
		{
			$errors[] = "Please ensure that all fields marked with an asterisk are complete";
		}

		if (count($errors) == 0)
		{
			$check_query = smart_mysql_query("SELECT * FROM abbijan_categories WHERE name='$category_name'");
			
			if (mysql_num_rows($check_query) == 0)
			{
				$sql = "INSERT INTO abbijan_categories SET name='$category_name', description='$category_description'";

				if (smart_mysql_query($sql))
				{
					header("Location: categories.php?msg=added");
					exit();
				}
			}
			else
			{
				header("Location: categories.php?msg=exists");
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

	$title = "Add Category";
	require_once ("inc/header.inc.php");

?>

		  <h2>Add Category</h2>

			<?php if (isset($errormsg) && $errormsg != "") { ?>
					<div style="width:75%;" class="error_box"><?php echo $errormsg; ?></div>
			<?php } ?>

		  <form action="" method="post">
		  <table align="center" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Category Name:</td>
			<td align="left">
				<input type="text" name="catname" id="catname" value="<?php echo getPostParameter('catname'); ?>" size="30" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Description:</td>
			<td align="left" valign="top"><textarea name="description" cols="50" rows="7" class="textbox2"><?php echo getPostParameter('description'); ?></textarea></select>
			</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td valign="middle" align="left">
				<input type="hidden" name="action" id="action" value="add" />
				<input type="submit" name="add" id="add" class="submit" value="Add Category" />
		    </td>
          </tr>
		  </table>
		  </form>


<?php require_once ("inc/footer.inc.php"); ?>