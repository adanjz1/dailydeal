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


if (isset($_POST['action']) && $_POST['action'] == "add_forum")
{
	$page_title	= mysql_real_escape_string($_POST['page_title']);
	$page_text	= mysql_real_escape_string($_POST['page_text']);
	$item_id	= (int)getPostParameter('item_id');

	unset($errs);
	$errs = array();

	if (!($page_title && $page_text))
	{
		$errs[] = "Please fill in all required fields";
	}

	if (count($errs) == 0)
	{
		$sql = "INSERT INTO abbijan_forums SET title='$page_title', item_id='$item_id', discussion='$page_text', created=NOW()";

		if (smart_mysql_query($sql))
		{
			header("Location: discussions.php?msg=added");
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


	$title = "Create New Discussion";
	require_once ("inc/header.inc.php");

?>
 
        <h2>Create New Discussion</h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>


        <form action="" method="post">
          <table width="100%" align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Title:</td>
            <td valign="top"><input type="text" name="page_title" id="page_title" value="<?php echo getPostParameter('page_title'); ?>" size="70" class="textbox" /></td>
          </tr>
			<?php
				$sql_items = "SELECT * FROM abbijan_items WHERE status!='inactive' ORDER BY added DESC";
				$rs_items = smart_mysql_query($sql_items);
				$total_items = mysql_num_rows($rs_items);

				if ($total_items > 0)
				{
			?>
          <tr>
            <td valign="middle" align="right" class="tb1">Deal:</td>
            <td align="left" valign="top">
				<select name="item_id" class="textbox2" id="item_id" style="width: 200px">
				<option value="">--- deal ---</option>
				<?php
						while ($row_items = mysql_fetch_array($rs_items))
						{
							if ($item_id == $row_items['item_id'])
								echo "<option value='".$row_items['item_id']."' selected>".$row_items['title']."</option>\n";
							else
								echo "<option value='".$row_items['item_id']."'>".$row_items['title']."</option>\n";
						}
				?>
				</select>			
			</td>
          </tr>
			<?php 
				}
			?>
          <tr>
            <td valign="middle" align="right" class="tb1"></td>
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
			<input type="hidden" name="action" id="action" value="add_forum" />
			<input type="submit" name="update" id="update" class="submit" value="Create Discussion" />
			&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='discussions.php'" />
		  </td>
          </tr>
        </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>