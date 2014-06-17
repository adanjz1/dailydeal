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
	require_once("../inc/pagination.inc.php");


	$pn			= (int)$_GET['pn'];
	$user		= (int)$_GET['user'];
	$project	= (int)$_GET['project'];


if (isset($_POST['action']) && $_POST['action'] == "edit_comment")
{
	unset($errs);
	$errs = array();

	$comment_id		= (int)getPostParameter('comment_id');
	$forum_id		= (int)getPostParameter('forum_id');
	$comment		= mysql_real_escape_string(nl2br(getPostParameter('comment')));
	$reply			= mysql_real_escape_string($_POST['reply']);
	//$reply		= mysql_real_escape_string(nl2br(getPostParameter('reply')));
	$status			= mysql_real_escape_string(getPostParameter('status'));


	if(!($comment_id && $comment && $status))
	{
		$errs[] = "Please fill in all required fields";
	}

	if (count($errs) == 0)
	{
		$sql = "UPDATE abbijan_forum_comments SET comment='$comment', reply='$reply', status='$status', updated=NOW() WHERE forum_comment_id='$comment_id' LIMIT 1";

		if (smart_mysql_query($sql))
		{
			header("Location: discussion_details.php?id=$forum_id&msg=updated&page=".$pn);
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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$comment_id = (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_forum_comments WHERE forum_comment_id='$comment_id' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit Comment";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2>Edit Comment</h2>


			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div style="width:60%;" class="error_box"><?php echo $allerrors; ?></div>
			<?php } ?>


        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">By:</td>
            <td valign="top">
				<a href="user_details.php?id=<?php echo $row['user_id']; ?>"><b><?php echo GetUsername($row['user_id']); ?></b></a>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Comment:</td>
            <td valign="top"><textarea name="comment" cols="95" rows="7" class="textbox2"><?php echo strip_tags($row['comment']); ?></textarea></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Admin Reply:</td>
            <td valign="top"><textarea cols="80" id="editor" name="reply" rows="10"><?php echo stripslashes($row['reply']); ?></textarea></td>
          </tr>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
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
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="forum_id" id="forum_id" value="<?php echo (int)$row['forum_id']; ?>" />
			<input type="hidden" name="comment_id" id="comment_id" value="<?php echo (int)$row['forum_comment_id']; ?>" />
			<input type="hidden" name="action" id="action" value="edit_comment" />
			<input type="submit" name="save" id="save" class="submit" value="Update" />&nbsp;&nbsp;
			<input type="button" class="cancel" name="cancel" value="Cancel" onclick="history.go(-1);return false;" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<div class="info_box">Sorry, comment not found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>