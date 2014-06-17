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


	$cc = 0;

	$query = "SELECT * FROM abbijan_shipping_methods ORDER BY status";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$dactive_total = mysql_num_rows(smart_mysql_query("SELECT * FROM abbijan_shipping_methods WHERE status='active'"));


	$title = "Shipping Methods";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="shipping_add.php">Add Shipping Method</a></div>

		<h2>Shipping Methods</h2>

		<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
		<div style="width: 64%" class="success_box">
			<?php
				switch ($_GET['msg'])
				{
					case "added":	echo "Shipping method was successfully added!"; break;
					case "updated": echo "Shipping method has been successfully edited!"; break;
					case "deleted": echo "Shipping method has been successfully deleted!"; break;
				}
			?>
		</div>
		<?php } ?>

		<?php if ($dactive_total == 0) { ?>
			<div class="error_box">Please setup at least one shipping method!</div>
		<?php } ?>

        <?php if ($total > 0) { ?>

			<table align="center" width="70%" style="border-bottom: 1px solid #F7F7F7;" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="45%">Shipping Method</th>
				<th width="15%">Fee</th>
				<th width="20%">Status</th>
				<th width="25%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>		  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle" >
						<a href="shipping_edit.php?id=<?php echo $row['shipping_method_id']; ?>"><?php echo $row['title']; ?></a>
					</td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo DisplayMoney($row['cost']); ?></td>
					<td align="center" valign="middle">
						<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="shipping_edit.php?id=<?php echo $row['shipping_method_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="shipping_delete.php?id=<?php echo $row['shipping_method_id']; ?>" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
            </table>

          <?php }else{ ?>
					<div class="info_box">There are no shipping methods at this time.</div>
          <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>