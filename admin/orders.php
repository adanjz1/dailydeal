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


		// Delete order //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$oid = (int)$v;
					DeleteOrder($oid);
				}

				header("Location: orders.php?msg=deleted");
				exit();
			}
		}

		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "username": $rrorder = "u.username"; break;
					case "amount": $rrorder = "o.total"; break;
					case "status": $rrorder = "o.status"; break;
					case "ids": $rrorder = "o.order_id"; break;
					case "viewed": $rrorder = "o.viewed"; break;
					default: $rrorder = "o.order_id"; break;
				}
			}
			else
			{
				$rrorder = "o.order_id";
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

			if (isset($_GET['action']) && $_GET['action'] == "filter")
			{
				$action			= "filter";
				$filter			= mysql_real_escape_string(trim(getGetParameter('filter')));
				$start_date		= mysql_real_escape_string(getGetParameter('start_date'));
				$end_date		= mysql_real_escape_string(getGetParameter('end_date'));
				$filter_by		= " AND reference_id='$filter'";
				if ($start_date != "")	$filter_by .= " AND created>='$start_date 00:00:00'";
				if ($end_date != "")	$filter_by .= " AND created<='$end_date 23:59:59'";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$query = "SELECT o.*, DATE_FORMAT(o.created, '%e %b %Y %h:%i %p') AS payment_date, u.fname, u.lname FROM abbijan_orders o LEFT JOIN abbijan_users u ON o.user_id=u.user_id WHERE 1=1 $filter_by ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		

		$query2 = "SELECT * FROM abbijan_orders WHERE 1=1".$filter_by;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


		$title = "Orders";
		require_once ("inc/header.inc.php");

?>
		<?php if ($total > 0) { ?>
			<div id="addnew"><a class="export" href="orders_export.php">Export Orders</a></div>
		<?php } ?>

		<h2 class="orders">Orders</h2>

       <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "processed": echo "Order has been successfully processed!"; break;
						case "updated": echo "Order has been successfully updated!"; break;
						case "deleted": echo "Order has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>


		<table align="center" width="98%" border="0" cellpadding="3" cellspacing="0">
		<tr>
		<td nowrap="nowrap" width="45%" valign="bottom" align="left">
           <form id="form1" name="form1" method="get" action="">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="ids" <?php if ($_GET['column'] == "ids") echo "selected"; ?>>Date</option>
			<option value="username" <?php if ($_GET['column'] == "username") echo "selected"; ?>>Customer</option>
			<option value="amount" <?php if ($_GET['column'] == "amount") echo "selected"; ?>>Amount</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
			<option value="viewed" <?php if ($_GET['column'] == "viewed") echo "selected"; ?>>Not viewed</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc"<?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
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
				<script>
					$(function() {
						$('#start_date').calendricalDate();
						$('#end_date').calendricalDate();
					});
				</script>
				<div class="admin_filter" style="width: 270px; background: #F7F7F7; border-radius: 5px; padding: 8px;">
					<div style="margin-top: 5px; float: left;">
						Reference ID: <input type="text" name="filter" value="<?php echo $filter; ?>" class="textbox" size="21" />
						<?php if (isset($filter) && $filter != "") { ?><a title="Cancel Search" href="orders.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Cancel Search" /></a><?php } ?><br/>
						Date: &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="start_date" id="start_date" value="<?php echo $start_date; ?>" size="10" maxlength="10" class="textbox" /> - <input type="text" name="end_date" id="end_date" value="<?php echo $end_date; ?>" size="10" maxlength="10" class="textbox" />
						<?php if (isset($start_date) && $start_date != "" && isset($end_date) && $end_date != "") { ?><a title="Cancel Search" href="users.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Cancel Search" /></a><?php } ?>
					</div>
					<div style="float: right; margin-top: 15px;">
						<input type="hidden" name="action" value="filter" />
						<input type="submit" class="submit" value="Search" />
					</div>
					<div style="clear: both"></div>
				</div>
				
			</td>
			<td nowrap="nowrap" width="20%" valign="bottom" align="right">
			   Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>
			</form>

			<form id="form2" name="form2" method="post" action="">
            <table align="center" width="98%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="15%">Order Number</th>
				<th width="20%">Customer</th>
				<th width="10%">Total</th>
				<th width="15%">Date</th>
				<th width="10%">Status</th>
				<th width="7%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['order_id']; ?>]" id="id_arr[<?php echo $row['order_id']; ?>]" value="<?php echo $row['order_id']; ?>" /></td>
					<td nowrap="nowrap" align="center" valign="middle">
					<a style="color: #000" href="order_details.php?id=<?php echo $row['order_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>">
					<?php
							if ($row['viewed'] == 0 && $row['reference_id'] != ""){ $tag1="<span class='new_one'>"; $tag2="</span>"; }else{ $tag1=$tag2=""; }
							echo $tag1.$row['reference_id'].$tag2;
					?>
					</a>
					</td>
					<td nowrap="nowrap" align="left" valign="middle"><a href='user_details.php?id=<?php echo $row['user_id']; ?>'><?php echo $row['fname']." ".$row['lname']; //$row['username']; ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo DisplayMoney($row['total']); ?></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['payment_date']; ?></td>
					<td nowrap="nowrap" align="left" valign="middle" style="padding-left:15px;">
					<?php
							switch ($row['status'])
							{
								case "complete": echo "<span class='complete_status'>complete</span>"; break;
								case "shipped": echo "<span class='shipped_status'>shipped</span>"; break;
								case "delivered": echo "<span class='delivered_status'>delivered</span>"; break;
								case "pending": echo "<span class='pending_status'>pending</span>"; break;
								case "declined": echo "<span class='declined_status'>declined</span>"; break;
								case "refunded": echo "<span class='refund_status'>refunded</span>"; break;
								default: echo "<span class='payment_status'>".$row['status']."</span>"; break;
							}
					?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="order_print.php?id=<?php echo $row['order_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Print"><img src="images/print.png" border="0" alt="Print" /></a>
						<a href="order_details.php?id=<?php echo $row['order_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this order?') )location.href='order_delete.php?id=<?php echo $row['order_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
             <?php } ?>
				<tr>
					<td colspan="7" align="left">
						<input type="hidden" name="action" value="delete" />
						<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
					</td>
				</tr>
				<tr>
				  <td colspan="7" align="center" valign="top">
					<?php echo ShowPagination("orders",$results_per_page,"orders.php?column=$rrorder&order=$rorder&action=$action&filter=$filter&start_date=$start_date&end_date=$end_date&show=$results_per_page&","WHERE 1=1".$filter_by); ?>
				  </td>
				</tr>
            </table>
			</form>

		</table>
        
		 <?php }else{ ?>
					<?php if (isset($filter)) { ?>
						<div class="info_box">Sorry, no results found for your search criteria! <a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></div>
					<?php }else{ ?>
						<div class="info_box">There are currently no orders.</div>
					<?php } ?>
        <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>