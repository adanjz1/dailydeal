<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");


	$results_per_page = DISCUSSIONS_PER_PAGE;
	$cc = 0;

	if (isset($_GET['filter']) && $_GET['filter'] != "")
	{
		$filter	= mysql_real_escape_string(trim(getGetParameter('filter')));
		$filter_by = " AND (title LIKE '%".$filter."%' OR discussion LIKE '%".$filter."%')";
	}

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;
	$where = " status='active' ";
	
	$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y') AS date_created FROM abbijan_forums WHERE status='active' $filter_by ORDER BY featured DESC, created DESC LIMIT $from, $results_per_page";
	
	$total_result = smart_mysql_query("SELECT * FROM abbijan_forums WHERE $where ".$filter_by);
	$total = mysql_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);

			// send notification
			if (NEW_COMMENT_ALERT == 1)
			{
				$message = "New comment was added.";

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
				@mail(SITE_ALERTS_MAIL, "New Comment Added", $message, $headers);
			}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Discussion";

	require_once ("inc/header.inc.php");

?>

	<h1>Discussion</h1>


	<?php

		if ($total > 0) {

	?>
			<div style="search_form" align="right">
			<form>
				<input type="text" name="filter" value="<?php echo $filter; ?>" class="textbox" size="30" /> <input type="submit" class="submit" value="Search" />
				<?php if (isset($filter) && $filter != "") { ?><a href="/discussion.php" title="Cancel Search"><img align="absmiddle" src="/images/cancel_filter.png" border="0" alt="Cancel Search" /></a><?php } ?> 
			</form>
			</div>

            <table class="brd" align="center" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="10%"></th>
				<th width="45%">Title</th>
				<th width="15%">Comments</th>
				<th width="15%">Created</th>
				<th width="15%">Last Comment</th>
			</tr>
			 <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
			 <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<td align="center" valign="middle"><?php if ($row['item_id'] > 0) { ?><a class="forum_title" href="/forum_details.php?id=<?php echo $row['forum_id']; ?>"><?php echo GetDealThumb($row['item_id']); ?></a><?php } ?></td>
				<td align="left" valign="middle"><a class="forum_title" href="/forum_details.php?id=<?php echo $row['forum_id']; ?>"><h3><?php echo $row['title']; ?></h3></a></td>
				<td nowrap="nowrap" align="center" valign="middle"><span class="total_posts"><a href="/forum_details.php?id=<?php echo $row['forum_id']; ?>"><?php echo GetForumPostsTotal($row['forum_id']); ?></a></span></td>
				<td nowrap="nowrap" align="center" valign="middle"><?php echo relative_date(strtotime($row['created'])); ?></td>
				<td nowrap="nowrap" align="center" valign="middle"><?php echo GetLastForumPost($row['forum_id']); ?></td>
			 </tr>
			<?php } ?>
			</table>

			<?php echo ShowPagination("forums",$results_per_page,"discussion.php?column=$rrorder&order=$rorder&","WHERE 1=1".$filter_by.""); ?>

	<?php }else{ ?>
			<p align="center">There are no posts at this time.</p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>