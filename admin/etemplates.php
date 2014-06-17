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

	$query = "SELECT * FROM abbijan_email_templates ORDER BY template_id ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$cc = 0;

	$title = "Email Templates";
	require_once ("inc/header.inc.php");

?>

		<h2>Email Templates</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:45%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Email template was successfully added!"; break;
						case "updated": echo "Email template has been successfully edited!"; break;
						case "deleted": echo "Email template has been successfully deleted!"; break;
					}

				?>
			</div>
			<?php } ?>


			<table align="center" style="border-bottom: 1px solid #F7F7F7;" width="45%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th class="noborder" width="1%">&nbsp;</th>
				<th width="75%">Template Name</th>
				<th width="20%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center"><img src="images/icons/etemplate.png" /></td>
					<td align="left" valign="middle" ><a href="etemplate_details.php?id=<?php echo $row['template_id']; ?>"><?php echo $row['email_title']; ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="etemplate_details.php?id=<?php echo $row['template_id']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="etemplate_edit.php?id=<?php echo $row['template_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
					</td>
				  </tr>
			<?php } ?>
            </table>

          <?php }else{ ?>
					<div class="info_box">Sorry, no email template found.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>