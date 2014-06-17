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
		$results_per_page = RESULTS_PER_PAGE;


		// Delete deals
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$item_id = (int)$v;
					DeleteDeal($item_id);
				}

				header("Location: deals.php?msg=deleted");
				exit();
			}
		}

		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "title": $rrorder = "title"; break;
					case "added": $rrorder = "added"; break;
					case "end_date": $rrorder = "end_date"; break;
					case "price": $rrorder = "price"; break;
					case "discount": $rrorder = "discount"; break;
					case "sales": $rrorder = "sales"; break;
					case "views": $rrorder = "views"; break;
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
				$filter_by = " WHERE (title LIKE '%$filter%' OR description LIKE '%$filter%')";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$query = "SELECT *, DATE_FORMAT(start_date, '%d %b %Y') as date_start, DATE_FORMAT(end_date, '%d %b %Y') as date_end, DATE_FORMAT(added, '%d %b %Y') AS date_added, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM abbijan_items $filter_by ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM abbijan_items".$filter_by;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


	$title = "Deals";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="import" href="deals_import.php" style="margin-right: 50px;">Import Deals</a>  <a class="addnew" href="deal_add.php">Add Deal</a></div>

		<h2>Deals</h2>

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added": echo "Deal has been successfully added!"; break;
						case "updated": echo "Deal has been successfully updated!"; break;
						case "deleted": echo "Deal has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>


		<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
		<td nowrap="nowrap" valign="middle" align="left" width="50%">
            <form id="form1" name="form1" method="get" action="">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>>Recently Added</option>
			<option value="title" <?php if ($_GET['column'] == "title") echo "selected"; ?>>Title</option>
			<option value="end_date" <?php if ($_GET['column'] == "end_date") echo "selected"; ?>>End Soonest</option>
			<option value="price" <?php if ($_GET['column'] == "price") echo "selected"; ?>>Price</option>
			<option value="discount" <?php if ($_GET['column'] == "discount") echo "selected"; ?>>Discount</option>
			<option value="sales" <?php if ($_GET['column'] == "sales") echo "selected"; ?>>Sales</option>
			<option value="views" <?php if ($_GET['column'] == "views") echo "selected"; ?>>Popularity</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
		  <input type="hidden" name="page" value="<?php echo $page; ?>" />
		  &nbsp;&nbsp;View: 
          <select name="show" id="order" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
          </select>
            </form>
			</td>
			<td nowrap="nowrap" width="30%" valign="middle" align="left">
				<div class="admin_filter">
					<input type="text" name="filter" value="<?php echo $filter; ?>" class="textbox" size="30" /> <input type="submit" class="submit" value="Search" />
					<?php if (isset($filter) && $filter != "") { ?><a title="Cancel Search" href="deals.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Cancel Search" /></a><?php } ?> 
				</div>
			</td>
			<td nowrap="nowrap" valign="middle" width="33%" align="right">
				Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>

			<form id="form2" name="form2" method="post" action="">
			<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="7%">ID</th>
				<th width="35%">Title</th>
				<th width="9%">Price</th>
				<th width="9%">Options</th>
				<th width="9%">Sales</th>
				<th width="10%">Start Date</th>
				<th width="10%">End Date</th>
				<th width="10%">Status</th>
				<th width="10%">Actions</th>
			</tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>				  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td nowrap="nowrap" align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['item_id']; ?>]" id="id_arr[<?php echo $row['item_id']; ?>]" value="<?php echo $row['item_id']; ?>" /></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['item_id']; ?></td>
					<td align="left" valign="middle">
						<a href="deal_details.php?id=<?php echo $row['item_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>">
							<?php if (strlen($row['title']) > 100) echo substr($row['title'], 0, 100)."..."; else echo $row['title']; ?>
						</a>
						<?php if ($row['main_deal'] == 1) { ?><span class="main_deal" alt="Main Deal" title="Main Deal"></span><?php } ?>
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="Featured Deal" title="Featured Deal"></span><?php } ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo DisplayMoney($row['price']); ?></td>
					<td nowrap="nowrap" align="center" valign="middle"><a href="deal_options.php?id=<?php echo $row['item_id']; ?>"><?php echo GetDealOptionsTotal($row['item_id']); ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo GetDealSalesTotal($row['item_id']); ?></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo ($row['start_date'] == "0000-00-00 00:00:00") ? "---" : $row['date_start']; ?></td>
					<td nowrap="nowrap" align="center" valign="middle" title="Ends in <?php echo GetTimeLeft($row['time_left']); ?>"><?php echo $row['date_end']; ?></td>
					<td style="padding-left:7px;" nowrap="nowrap" align="left" valign="middle">
					<?php
						switch ($row['status'])
						{
							case "active": echo "<span class='active_s'>".$row['status']."</span>"; break;
							case "inactive": echo "<span class='inactive_s'>".$row['status']."</span>"; break;
							case "expired": echo "<span class='expired_status'>".$row['status']."</span>"; break;
							case "sold": echo "<span class='sold_status'>sold out</span>"; break;
							default: echo "<span class='default_status'>".$row['status']."</span>"; break;
						}
					?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="deal_details.php?id=<?php echo $row['item_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="deal_edit.php?id=<?php echo $row['item_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this deal') )location.href='deal_delete.php?id=<?php echo $row['item_id']; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>&pn=<?php echo $page?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
				<td colspan="10" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
				</tr>
				  <tr>
				  <td colspan="10" align="center">
					<?php echo ShowPagination("items",$results_per_page,"deals.php?column=$rrorder&order=$rorder&show=$results_per_page&".$filter_by); ?>
				  </td>
				  </tr>
            </table>
			</form>

          <?php }else{ ?>
					<div class="info_box">There are no deals at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>