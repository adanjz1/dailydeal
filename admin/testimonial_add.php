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
		unset($errors);
		$errors = array();

		$author			= mysql_real_escape_string(getPostParameter('author'));
		$testimonial	= mysql_real_escape_string($_POST['testimonial']);
		$status			= mysql_real_escape_string(getPostParameter('status'));

		if (!($author && $testimonial && $status))
		{
			$errors[] = "Please fill in all required fields";
		}

		if (count($errors) == 0)
		{		
			$insert_sql = "INSERT INTO abbijan_testimonials SET author='$author', testimonial='$testimonial', status='$status', added=NOW()";
			$result = smart_mysql_query($insert_sql);
			$new_item_id = mysql_insert_id();

			header("Location: testimonials.php?msg=added");
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
}

	$title = "Add Testimonial";
	require_once ("inc/header.inc.php");

?>

    <h2 class="feedbacks">Add Testimonial</h2>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div style="margin: 0 auto; width: 500px;" class="error_box"><?php echo $errormsg; ?></div>
	<?php } ?>

      <form action="" method="post" name="form1">
        <table cellpadding="2" cellspacing="5" border="0" align="center">
			<tr>
				<td width="20%" valign="middle" align="right" class="tb1"><span class="req">* </span>Author:</td>
				<td width="80%" valign="top"><input type="text" name="author" value="<?php echo getPostParameter('author'); ?>" size="35" class="textbox" /></td>
			</tr>
			<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
			<tr>
				<td valign="middle" align="right" class="tb1"><span class="req">* </span>Testimonial:</td>
				<td valign="top"><textarea cols="80" id="editor" name="testimonial" rows="10"><?php echo stripslashes($_POST['testimonial']); ?></textarea></td>
            </tr>
				<script>
					CKEDITOR.replace( 'editor' );
				</script>
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
				<td align="center" colspan="2" valign="bottom">
					<input type="hidden" name="action" id="action" value="add">
					<input type="submit" class="submit" name="add" id="add" value="Add testimonial" />
				</td>
            </tr>
          </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>