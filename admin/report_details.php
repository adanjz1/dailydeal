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
	require_once("./inc/adm_functions.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$query = "SELECT reports.*, reports.user_id as ruser_id, reports.item_id as ritem_id, DATE_FORMAT(reports.added, '%e %b %Y %h:%i %p') AS date_added, items.* FROM abbijan_reports reports LEFT JOIN abbijan_items items ON items.item_id=reports.item_id WHERE reports.report_id='".$id."' LIMIT 1";
		
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Report Details";
	require_once ("inc/header.inc.php");

?>
    
    
     <h2>Report Details</h2>

		<img src="images/icons/alert.png" align="right" />

		<?php if ($total > 0) {

				smart_mysql_query("UPDATE abbijan_reports SET viewed='1' WHERE report_id='$id'");
				$row = mysql_fetch_array($result);
		 ?>
            <table width="70%" cellpadding="3" cellspacing="5" border="0">
			  <tr>
                <td valign="middle" align="left" class="tb1">Report ID:</td>
                <td valign="middle"><?php echo $row['report_id']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="left" class="tb1">From:</td>
                <td valign="middle"><?php echo GetUsername($row['reporter_id']); ?></td>
              </tr>
			  <?php if ($row['ruser_id'] != 0) { ?>
              <tr>
                <td valign="middle" align="left" class="tb1">User Name:</td>
                <td valign="top"><a href="user_details.php?id=<?php echo $row['ruser_id']; ?>"><?php echo GetUsername($row['ruser_id']); ?></a></td>
              </tr>
			  <?php } ?>
			  <?php if ($row['ritem_id'] != 0) { ?>
              <tr>
                <td valign="middle" align="left" class="tb1">Deal Name:</td>
                <td valign="middle"><a href="deal_details.php?id=<?php echo $row['ritem_id']; ?>"><?php echo $row['title']; ?></a></td>
              </tr>
			  <?php } ?>
              <tr>
				<td valign="middle" align="left" class="tb1">Reason:</td>
				<td valign="top"><div style="width:350px; min-height:60px; padding:10px; background:#F7F7F7; border:1px solid #EEE;"><?php echo $row['report']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="left" class="tb1">Created:</td>
                <td valign="middle"><?php echo $row['date_added']; ?></td>
              </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" />
			  </td>
            </tr>
          </table>

      <?php }else{ ?>
			<div class="info_box">Sorry, no report found.</div>
			<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>