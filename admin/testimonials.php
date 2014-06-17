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


	$results_per_page = 10;


		// Delete //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$testimonial_id = (int)$v;
					DeleteTestimonial($testimonial_id);
				}

				header("Location: testimonials.php?msg=deleted");
				exit();
			}	
		}

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }

		$from = ($page-1)*$results_per_page;

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y') AS added_date FROM abbijan_testimonials ORDER BY testimonial_id LIMIT ".$from.",".$results_per_page;
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM abbijan_testimonials";
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


		$title = "Testimonials";

		require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="testimonial_add.php">Add Testimonial</a></div>

		<h2 class="feedbacks">Testimonials</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added": echo "Testimonial has been successfully added!"; break;
						case "updated": echo "Testimonial has been successfully updated!"; break;
						case "deleted": echo "Testimonial has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>


			<table width="100%" border="0" cellpadding="5" cellspacing="0" align="center">
			<tr>
				<td valign="middle" width="55%" align="right">
					Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
				</td>
			</tr>
			</table>

			<form id="form2" name="form2" method="post" action="">
            <table align="center" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr align="center">
				<th width="2%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="40%">Testimonial</th>
				<th width="10%">Status</th>
				<th width="10%">Actions</th>
			</tr>
			 <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['testimonial_id']; ?>]" id="id_arr[<?php echo $row['testimonial_id']; ?>]" value="<?php echo $row['testimonial_id']; ?>" /></td>
					<td align="left" valign="middle">
						<div style="float: left"><b><?php echo $row['author']; ?></b></div><div style="float: right"><span class="date"><?php echo $row['added_date']; ?></span></div>
						<div style="clear:both"></div>
						<span style="display: block; padding: 7px 0; color: #545454; text-align: justify;"><?php echo $row['testimonial']; ?></span></td>
					<td align="center" valign="middle">
						<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="testimonial_edit.php?id=<?php echo $row['testimonial_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this testimonial?') )location.href='testimonial_delete.php?id=<?php echo $row['testimonial_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
				<td colspan="4" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
				</tr>
				<tr>
				  <td colspan="4" align="center">
					<?php echo ShowPagination("testimonials",$results_per_page,"testimonials.php?column=$rrorder&order=$rorder&"); ?>
				  </td>
				</tr>
            </table>
			</form>

		</table>

        <?php }else{ ?>
				<div class="info_box">There are no testimonials at this time.</div>
        <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>