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


if (isset($_POST['action']) && $_POST['action'] == "editetemplate")
{
	$etemplate_id	= (int)getPostParameter('eid');
	$email_subject	= mysql_real_escape_string($_POST['esubject']);
	$email_message	= mysql_real_escape_string($_POST['emessage']);

	unset($errs);
	$errs = array();

	if (!($email_subject && $email_message))
	{
		$errs[] = "Please fill in all fields";
	}

	if (count($errs) == 0)
	{
		$sql = "UPDATE abbijan_email_templates SET email_subject='$email_subject', email_message='$email_message', modified=NOW() WHERE template_id='$etemplate_id'";

		if (smart_mysql_query($sql))
		{
			header("Location: etemplates.php?msg=updated");
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


	if (isset($_GET['id']) && is_numeric($_GET['id'])) { $eid = (int)$_GET['id']; } else { $eid = (int)$_POST['eid']; }
	$query = "SELECT * FROM abbijan_email_templates WHERE template_id='$eid'";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	$title = "Edit Email Template";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2>Edit Email Template</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table width="100%" align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td width="80" valign="middle" align="right" class="tb1">Email Subject:</td>
            <td valign="top"><input type="text" name="esubject" id="esubject" value="<?php echo $row['email_subject']; ?>" size="60" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Email Message:</td>
            <td valign="top">
				<textarea cols="80" id="editor1" name="emessage" rows="10"><?php echo stripslashes($row['email_message']); ?></textarea>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor1' );
				</script>		
			</td>
          </tr>
		  <?php if ($row['email_variables'] != "") { ?>
           <tr>
            <td>&nbsp;</td>
            <td height="50" style="border: 1px solid #EEEEEE; padding: 10px;" bgcolor="#F7F7F7" align="left" valign="middle">
				<p><b>Use following variables for this email template</b>:</p>
				<?php echo $row['email_variables']; ?>
			</td>
          </tr>
		  <?php } ?>
          <tr>
			<td>&nbsp;</td>
            <td align="center" valign="bottom">
				<input type="hidden" name="eid" id="eid" value="<?php echo (int)$row['template_id']; ?>" />
				<input type="hidden" name="action" id="action" value="editetemplate" />
				<input type="submit" name="update" id="update" class="submit" value="Update" />&nbsp;&nbsp;
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='etemplates.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no template found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>