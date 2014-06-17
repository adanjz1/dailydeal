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


	// results per page
	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0)
		$results_per_page = (int)$_GET['show'];
	else
		$results_per_page = 10;


		// Delete subscribers //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$subscriber_id = (int)$v;
					DeleteSubscriber($subscriber_id);
				}

				header("Location: subscribers.php?msg=deleted");
				exit();
			}	
		}

		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "ids": $rrorder = "subscriber_id"; break;
					case "email": $rrorder = "email"; break;
					case "status": $rrorder = "status"; break;
					default: $rrorder = "subscriber_id"; break;
				}
			}
			else
			{
				$rrorder = "subscriber_id";
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
				$filter_by = " AND email LIKE '%".$filter."%'";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }

		$from = ($page-1)*$results_per_page;

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS subscribe_date FROM abbijan_subscribers WHERE 1=1 $filter_by ORDER BY ".$rrorder." ".$rorder." LIMIT ".$from.",".$results_per_page;
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM abbijan_subscribers WHERE 1=1".$filter_by;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


		$title = "Subscribers";

		require_once ("inc/header.inc.php");

?>

       <h2>Subscribers</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "updated": echo "Subscriber information has been successfully edited!"; break;
						case "deleted": echo "Subscriber has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>


		<table width="100%" border="0" cellpadding="3" cellspacing="0" align="center">
		<tr>
		<td nowrap="nowrap" valign="middle" align="left" width="45%">
            <form id="form1" name="form1" method="get" action="">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="ids" <?php if ($_GET['column'] == "ids") echo "selected"; ?>>Date</option>
			<option value="email" <?php if ($_GET['column'] == "email") echo "selected"; ?>>Email</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
		  &nbsp;&nbsp;View: 
          <select name="show" id="order" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
          </select>
			</td>
			<td nowrap="nowrap" width="35%" valign="middle" align="left">
				<div class="admin_filter">
					<input type="text" name="filter" value="<?php echo $filter; ?>" class="textbox" size="35" /> <input type="submit" class="submit" value="Search" />
					<?php if (isset($filter) && $filter != "") { ?><a title="Cancel Search" href="subscribers.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Cancel Search" /></a><?php } ?> 
				</div>
			</td>
			<td nowrap="nowrap" valign="middle" width="35%" align="right">
				 Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>
			</form>

			<form id="form2" name="form2" method="post" action="">
            <table align="center" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr bgcolor="#F7F7F7" align="center">
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="10%">ID</th>
				<th width="30%">Email</th>
				<th width="15%">Join Date</th>
				<th width="15%">Status</th>
				<th width="10%">Actions</th>
			</tr>
			 <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td nowrap="nowrap" align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['subscriber_id']; ?>]" id="id_arr[<?php echo $row['subscriber_id']; ?>]" value="<?php echo $row['subscriber_id']; ?>" /></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['subscriber_id']; ?></td>
					<td nowrap="nowrap" align="left" valign="middle"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['subscribe_date']; ?></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle" >
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this subscriber?') )location.href='subscriber_delete.php?id=<?php echo $row['subscriber_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
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
						<?php echo ShowPagination("subscribers",$results_per_page,"subscribers.php?column=$rrorder&order=$rorder&show=$results_per_page&","WHERE 1=1".$filter_by.""); ?>
					</td>
				</tr>
            </table>
			</form>

		</table>

        <?php }else{ ?>
				<?php if (isset($filter)) { ?>
					<div class="info_box">No subscriber found. <a href="subscribers.php">Search again &#155;</a></div>
				<?php }else{ ?>
					<div class="info_box">There are no newsletter subscribers at this time.</div>
				<?php } ?>
        <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>