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
	require_once("./inc/vn.inc.php");


	$today = date("Y-m-d");
	$yesterday = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

	$users_today = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_users WHERE date(created)='$today'"));
	$users_today = $users_today['total'];
	if ($users_today > 0) $users_today = "+" . $users_today;

	$orders_today = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_orders WHERE date(created)='$today'"));
	$orders_today = $orders_today['total'];
	if ($orders_today > 0) $orders_today = "+" . $orders_today;

	$orders_yesterday = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_orders WHERE date(created)='$yesterday'"));
	$orders_yesterday = $orders_yesterday['total'];

	$orders_7days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_orders WHERE date_sub(curdate(), interval 7 day) <= created"));
	$orders_7days = $orders_7days['total'];

	$orders_30days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_orders WHERE date_sub(curdate(), interval 30 day) <= created"));
	$orders_30days = $orders_30days['total'];

	$all_users = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_users"));
	$all_users = $all_users['total'];

	$a_deals = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) as total FROM abbijan_items WHERE start_date<=NOW() AND end_date>NOW() AND status='active'"));
	$a_deals = $a_deals['total'];

	$all_deals = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_items"));
	$all_deals = $all_deals['total'];

	$all_orders = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM abbijan_orders"));
	$all_orders = $all_orders['total'];

	$orders_total = mysql_fetch_array(smart_mysql_query("SELECT SUM(total) AS orders_total FROM abbijan_orders"));
	$orders_total = DisplayMoney($orders_total['orders_total']);

	$title = "Admin Home";
	require_once ("inc/header.inc.php");

?>

	<h2>Admin Home</h2>

	<?php if (file_exists("../install.php")) { ?>
		<div class="error_box">You must now delete "install.php" from your server. Failing to delete this file is a serious security risk!</div>
	<?php } ?>

 <table align="center" width="100%" border="0" cellpadding="2" cellspacing="2">
 <tr>
	<td width="40%" align="left" valign="top">

		<table align="center" width="100%" border="0" cellpadding="6" cellspacing="2">
		<tr>
			<td nowrap="nowrap" align="left" valign="middle" class="tb2"><b>abbijan</b> version:</td>
			<td align="right" valign="middle"><?php echo $abbijan_version; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">License Key:</td>
			<td nowrap="nowrap" align="right" valign="middle"><?php echo GetSetting('license'); ?></td>
		</tr>
		<tr>
			<td colspan="2"><div class="sline"></div></td>
		</tr>
		</table>

	</td>
	<td width="30%" align="left" valign="top">

		<table align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
		<tr>
			<td align="left" valign="middle" class="tb2">Orders Today:</td>
			<td align="right" valign="middle" class="stat_s"><font color="#2F97EB"><?php echo $orders_today; ?></font></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Orders Yesterday:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $orders_yesterday; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Last 7 Days Orders:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $orders_7days; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Last 30 Days Orders:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $orders_30days; ?></td>
		</tr>
		<tr>
			<td colspan="2"><div class="sline"></div></td>
		</tr>
		</table>

	</td>
	<td width="30%" align="left" valign="top">

		<table align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
		<tr>
			<td align="left" valign="middle" class="tb2">Live Deals:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $a_deals; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Total Deals:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $all_deals; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">Total Users:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $all_users; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2">All Orders:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $all_orders; ?></td>
		</tr>
		<tr>
			<td align="left" valign="middle" class="tb2"><b>Sales Total</b>:</td>
			<td align="right" valign="middle" class="stat_s"><?php echo $orders_total; ?></td>
		</tr>
		</table>

	</td>
 </tr>
 </table>


<?php require_once ("inc/footer.inc.php"); ?>