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
	require_once("./inc/adm_functions.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$forum_id = (int)$_GET['id'];
	}


	// results per page
	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0)
		$results_per_page = (int)$_GET['show'];
	else
		$results_per_page = 10;


		// Delete discussion comments
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$comment_id = (int)$v;
					DeleteComment($comment_id);
				}

				header("Location: discussion_details.php?id=$forum_id&msg=deleted");
				exit();
			}
		}

		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "comment": $rrorder = "comment"; break;
					case "added": $rrorder = "added"; break;
					case "user_id": $rrorder = "user_id"; break;
					case "status": $rrorder = "status"; break;
					default: $rrorder = "added"; break;
				}
			}
			else
			{
				$rrorder = "added";
			}

			if (isset($_GET['order']) && $_GET['order'] != "")
			{
				switch ($_GET['order'])
				{
					case "asc": $rorder = "asc"; break;
					case "desc": $rorder = "desc"; break;
					default: $rorder = "desc"; break;
				}
			}
			else
			{
				$rorder = "desc";
			}
			if (isset($_GET['filter']) && $_GET['filter'] != "")
			{
				$filter	= mysql_real_escape_string(trim(getGetParameter('filter')));
				$filter_by = " AND (comment LIKE '%$filter%')";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS date_added FROM abbijan_forum_comments WHERE forum_id='$forum_id' $filter_by ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM abbijan_forum_comments WHERE forum_id='$forum_id'".$filter_by;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


	$title = "Discussion Comments";
	require_once ("inc/header.inc.php");

?>

		<h2>Discussion Comments <?php echo ($total > 0) ? "<sup>$total</sup>" : ""; ?></h2>

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added": echo "Comment has been successfully added!"; break;
						case "updated": echo "Comment has been successfully updated!"; break;
						case "deleted": echo "Comment has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>

		<div style="background: #F9F9F9; margin: 5px 0; padding: 10px;">
		<?php
			$forum_query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS date_created FROM abbijan_forums WHERE forum_id='$forum_id' LIMIT 1";
			$forum_result = smart_mysql_query($forum_query);
			$forum_total = mysql_num_rows($forum_result);
			
			if ($forum_total > 0)
			{
				$forum_row = mysql_fetch_array($forum_result);

				if ($forum_row['item_id'] > 0) echo "<div style='float: right; padding-top: 2px;'<b>Deal URL</b>: <a href='/deal_details.php?id=".$forum_row['item_id']."' target='_blank'>".SITE_URL."deal_details.php?id=".$forum_row['item_id']."</a></div>";

				echo "<b>".$forum_row['title']."</b>";
			}
		?>
		</div>

		<form id="form1" name="form1" method="get" action="">
		<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
		<td nowrap="nowrap" valign="middle" align="left" width="50%">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>>Date</option>
			<option value="comment" <?php if ($_GET['column'] == "comment") echo "selected"; ?>>Comment</option>
			<option value="user_id" <?php if ($_GET['column'] == "user_id") echo "selected"; ?>>User</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
				<input type="hidden" name="id" value="<?php echo $forum_id; ?>" />
				<input type="hidden" name="page" value="<?php echo $page; ?>" />
		  &nbsp;&nbsp;View: 
          <select name="show" id="order" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
          </select>
			</td>
			<td nowrap="nowrap" width="30%" valign="middle" align="left">
				<div class="admin_filter">
					<input type="text" name="filter" value="<?php echo $filter; ?>" class="textbox" size="30" /> <input type="submit" class="submit" value="Search" />
					<?php if (isset($filter) && $filter != "") { ?><a title="Cancel Search" href="discussions.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Cancel Search" /></a><?php } ?> 
				</div>
			</td>
			<td nowrap="nowrap" valign="middle" width="33%" align="right">
				Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>
			 </form>

			<form id="form2" name="form2" method="post" action="">
			<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="70%">Comment</th>
				<th width="10%">Status</th>
				<th width="10%">Actions</th>
			</tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>				  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td nowrap="nowrap" align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['forum_comment_id']; ?>]" id="id_arr[<?php echo $row['forum_comment_id']; ?>]" value="<?php echo $row['forum_comment_id']; ?>" /></td>
					<td align="left" valign="middle">
						<div style="float: left; width: 70%;"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo GetUsername($row['user_id']); ?></a></div>
						<div style="float: right;"><span style="color: #A0A0A0; font-size: 10px;"><?php echo $row['date_added']; ?></span></div>
						<div style="clear: both"></div>
						<div style="margin: 5px 0;"><?php echo $row['comment']; ?></div>
						<?php if ($row['reply'] != "") { ?>
							<div style="float: right; width: 500px; background: #FCF0D9; border-radius: 7px; padding: 7px; margin: 5px 0; min-height: 30px;">
								<p><b>Admin</b><br/><?php echo stripslashes($row['reply']); ?></p>
							</div>
						<?php } ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
					<?php
						switch ($row['status'])
						{
							case "active": echo "<span class='active_s'>".$row['status']."</span>"; break;
							case "inactive": echo "<span class='inactive_s'>".$row['status']."</span>"; break;
							case "pending": echo "<span class='pending_status'>".$row['status']."</span>"; break;
							default: echo "<span class='default_status'>".$row['status']."</span>"; break;
						}
					?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="comment_details.php?id=<?php echo $row['forum_comment_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Reply"><img src="images/reply.png" border="0" alt="Reply" /></a>
						<a href="comment_edit.php?id=<?php echo $row['forum_comment_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this comment?') )location.href='comment_delete.php?id=<?php echo $row['forum_comment_id']; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>&pn=<?php echo $page?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
				<td colspan="6" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
				</tr>
				  <tr>
				  <td colspan="6" align="center">
					<?php echo ShowPagination("forum_comments",$results_per_page,"discussion_details.php?column=$rrorder&order=$rorder&show=$results_per_page&".$filter_by, "WHERE forum_id='$forum_id'"); ?>
				  </td>
				  </tr>
            </table>
			</form>

          <?php }else{ ?>
					<div class="info_box">There are no discussions at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>