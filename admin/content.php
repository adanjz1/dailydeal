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


	$query = "SELECT * FROM abbijan_content ORDER BY content_id ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$cc = 0;

	$title = "Content";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="content_add.php">Create Page</a></div>

		<h2>Content</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:50%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Content was successfully added!"; break;
						case "updated": echo "Content has been successfully edited!"; break;
						case "deleted": echo "Content has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>


			<table align="center" style="border-bottom: 1px solid #F7F7F7;" width="50%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th class="noborder" width="1%">&nbsp;</th>
				<th width="75%">Page Title</th>
				<th width="20%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center"><img src="images/icons/content.png" /></td>
					<td align="left" valign="middle" ><a href="content_details.php?id=<?php echo $row['content_id']; ?>"><?php echo $row['title']; ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="content_details.php?id=<?php echo $row['content_id']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="content_edit.php?id=<?php echo $row['content_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<?php if ($row['content_id'] > 5) { ?>
							<a href="#" onclick="if (confirm('Are you sure you really want to delete this page?') )location.href='content_delete.php?id=<?php echo $row['content_id']; ?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
						<?php } ?>
					</td>
				  </tr>
			<?php } ?>
            </table>

          <?php }else{ ?>
					<div class="info_box">Sorry, no content found.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>