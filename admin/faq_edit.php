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


if (isset($_POST["action"]) && $_POST["action"] == "edit")
{
		unset($errors);
		$errors = array();

		$faq_id			= (int)getPostParameter('tid');
		$question		= mysql_real_escape_string(getPostParameter('question'));
		$answer			= mysql_real_escape_string($_POST['answer']);
		$status			= mysql_real_escape_string(getPostParameter('status'));

		if (!($question && $answer && $status))
		{
			$errors[] = "Please fill in all required fields";
		}

		if (count($errors) == 0)
		{
			smart_mysql_query("UPDATE abbijan_faqs SET question='$question', answer='$answer', status='$status' WHERE faq_id='$faq_id'");

			header("Location: faqs.php?msg=updated");
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

		$query = "SELECT * FROM abbijan_faqs WHERE faq_id='$id' LIMIT 1";
		$rs	= smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	$title = "Edit FAQ";
	require_once ("inc/header.inc.php");

?>


    <h2 class="feedbacks">Edit FAQ</h2>

	<?php if ($total > 0) {
		
		$row = mysql_fetch_array($rs);

	?>


	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div style="margin: 0 auto; width: 500px;" class="error_box"><?php echo $errormsg; ?></div>
	<?php } ?>

      <form action="" method="post" name="form1">
        <table cellpadding="2" cellspacing="5" border="0" align="center">
			<tr>
				<td width="20%" valign="middle" align="right" class="tb1"><span class="req">* </span>Question:</td>
				<td width="80%" valign="top"><input type="text" name="question" value="<?php echo $row['question']; ?>" size="75" class="textbox" /></td>
			</tr>
			<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
            <tr>
				<td valign="middle" align="right" class="tb1"><span class="req">* </span>Answer:</td>
				<td valign="top"><textarea cols="80" id="editor" name="answer" rows="10"><?php echo stripslashes($row['answer']); ?></textarea></td>
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
				<input type="hidden" name="tid" id="tid" value="<?php echo (int)$row['testimonial_id']; ?>" />
				<input type="hidden" name="action" id="action" value="edit">
				<input type="submit" class="submit" name="update" id="update" value="Update" />&nbsp;&nbsp;
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='testimonials.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
              </td>
            </tr>
          </table>
      </form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no faq found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>