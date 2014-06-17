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
		$uid = (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS date_created FROM abbijan_orders WHERE user_id='$uid' ORDER BY created DESC";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}  

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Orders History</title>
	<link href="css/abbijan.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/abbijan_scripts.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table width="100%" bgcolor="#FFFFFF" align="center" border="0" cellpadding="3" cellspacing="0">
<tr>
 <td valign="top" align="left">

		<h2>Orders History</h2>

	<?php
			if ($total > 0) {
	?>
            <table align="center" width="98%" border="0" cellspacing="0" cellpadding="3">
              <tr>
                <th width="25%">Date</th>
				<th width="25%">Reference ID</th>
				<th width="15%">Amount</th>
				<th width="20%">Status</th>
              </tr>
				<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr style="height:25px;" bgcolor="<?php if (($cc%2) == 0) echo "#F7F7F7"; else echo "#FFFFFF"; ?>">
                  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
                  <td valign="middle" align="center"><?php echo $row['reference_id']; ?></td>
				  <td valign="middle" align="center"><?php echo DisplayMoney($row['total']+$row['shipping_total']); ?></td>
                  <td valign="middle" align="left" style="padding-left: 15px;">
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

							if ($row['status'] == "declined" && $row['reason'] != "")
							{
								echo " <div class=\"abbijan_tooltip\"><img src=\"images/icon_question.png\" align=\"absmiddle\" /><span class=\"tooltip\">".$row['reason']."</span></div>";
							}
					?>
				  </td>
                </tr>
				<?php } ?>
           </table>
	  
	  <?php }else{ ?>
				<div class="info_box">There are no orders at this time.</div>
      <?php } ?>

	<hr size="1" color="#EEEEEE">
	<div align="right"><a onclick="window.close(); return false;" href="#" class="close">Close this window</a></div>

 </td>
</tr>
</table>
</body>
</html>