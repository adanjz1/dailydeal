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


if (isset($_POST['action']) && $_POST['action'] == "addcontent")
{
	$page_name	= mysql_real_escape_string(getPostParameter('page_name'));
	$page_title	= mysql_real_escape_string($_POST['page_title']);
	$page_text	= mysql_real_escape_string($_POST['page_text']);

	unset($errs);
	$errs = array();

	if (!($page_title && $page_text))
	{
		$errs[] = "Please fill in all required fields";
	}

	if (count($errs) == 0)
	{
		$check_query = smart_mysql_query("SELECT * FROM abbijan_content WHERE name='$page_name'");
		if (mysql_num_rows($check_query) == 0)
		{
			$sql = "INSERT INTO abbijan_content SET name='$page_name', title='$page_title', description='$page_text', modified=NOW()";

			if (smart_mysql_query($sql))
			{
				header("Location: content.php?msg=added");
				exit();
			}
		}
		else
		{
			$allerrors = "Sorry, please use another 'Page name'";
		}
	}
	else
	{
		$allerrors = "";
		foreach ($errs as $errorname)
			$allerrors .= "&#155; ".$errorname."<br/>\n";
	}
}


	$title = "Create New Page";
	require_once ("inc/header.inc.php");

?>
 
        <h2>Create New Page</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table width="100%" align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Page Name:</td>
            <td valign="top"><input type="text" name="page_name" id="page_name" value="<?php echo getPostParameter('page_name'); ?>" size="30" class="textbox" /><small class="note">ex. mypage</small></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Page Title:</td>
            <td valign="top"><input type="text" name="page_title" id="page_title" value="<?php echo getPostParameter('page_title'); ?>" size="70" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Content:</td>
            <td valign="top">
				<textarea cols="80" id="editor" name="page_text" rows="10"><?php echo stripslashes($_POST['page_text']); ?></textarea>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor' );
				</script>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="action" id="action" value="addcontent" />
			<input type="submit" name="update" id="update" class="submit" value="Create Page" />
			&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='content.php'" />
		  </td>
          </tr>
        </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>