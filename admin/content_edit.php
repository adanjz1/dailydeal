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


if (isset($_POST['action']) && $_POST['action'] == "editcontent")
{
	$content_id	= (int)getPostParameter('cid');
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
		$check_query = smart_mysql_query("SELECT * FROM abbijan_content WHERE content_id!='$content_id' AND name='$page_name'");
		if (mysql_num_rows($check_query) == 0)
		{
			// do not allow change "Page name" for main pages
			if ($content_id <= 5)
				$sql = "UPDATE abbijan_content SET title='$page_title', description='$page_text', modified=NOW() WHERE content_id='$content_id' LIMIT 1";
			else
				$sql = "UPDATE abbijan_content SET name='$page_name', title='$page_title', description='$page_text', modified=NOW() WHERE content_id='$content_id' LIMIT 1";

			if (smart_mysql_query($sql))
			{
				header("Location: content.php?msg=updated");
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


	if (isset($_GET['id']) && is_numeric($_GET['id'])) { $cid = (int)$_GET['id']; } else { $cid = (int)$_POST['cid']; }
	$query = "SELECT * FROM abbijan_content WHERE content_id='$cid' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	$title = "Edit Content";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2>Edit Content</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table width="100%" align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Page Name:</td>
            <td valign="top">
				<?php if ($row['content_id'] <= 5) { ?>
					<span style="color: #777"><?php echo $row['name']; ?></span>
				<?php }else{ ?>
					<input type="text" name="page_name" id="page_name" value="<?php echo $row['name']; ?>" size="30" class="textbox" />
				<?php } ?>
			</td>
          </tr>
          <tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Page Title:</td>
            <td valign="top"><input type="text" name="page_title" id="page_title" value="<?php echo $row['title']; ?>" size="70" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Content:</td>
            <td valign="top">
				<textarea cols="80" id="editor" name="page_text" rows="10"><?php echo stripslashes($row['description']); ?></textarea>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor' );
				</script>		
			</td>
          </tr>
		  <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="cid" id="cid" value="<?php echo (int)$row['content_id']; ?>" />
			<input type="hidden" name="action" id="action" value="editcontent" />
			<input type="submit" name="update" id="update" class="submit" value="Update" />
			&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='content.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no content found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>