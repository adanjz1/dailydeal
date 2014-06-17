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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$uid = (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS signup_date FROM abbijan_users WHERE ref_id='".(int)$uid."' AND status='active' ORDER BY created DESC";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}  

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>User Referrals</title>
<link href="css/abbijan.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table width="100%" bgcolor="#FFFFFF" align="center" border="0" cellpadding="3" cellspacing="0">
<tr>
 <td valign="top" align="left">

		<h2>User Referrals</h2>

<?php

		if ($total > 0) {
 
?>
            <table align="center" width="98%" border="0" cellspacing="0" cellpadding="3">
              <tr>
				<th width="12%">User ID</th>
				<th width="40%">User Name</th>
                <th width="20%">Signup Date</th>
				<th width="10%">Orders</th>
				<th width="17%">Status</th>
              </tr>
				<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr style="height:25px;" bgcolor="<?php if (($cc%2) == 0) echo "#F7F7F7"; else echo "#FFFFFF"; ?>">
                  <td valign="middle" align="center"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['user_id']; ?></a></td>
                  <td valign="middle" align="center"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
                  <td valign="middle" align="center"><?php echo $row['signup_date']; ?></td>
				  <td valign="middle" align="center"><?php echo GetUserOrdersTotal($row['user_id']); ?></td>
                  <td valign="middle" align="center">
					<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
				  </td>
                </tr>
				<?php } ?>
           </table>
	  
	  <?php }else{ ?>
				<div class="info_box">This user does not receive any referrals at this time.</div>
      <?php } ?>

	<hr size="1" color="#EEEEEE">
	<div align="right"><a onclick="window.close(); return false;" href="#" class="close">Close this window</a></div>

 </td>
</tr>
</table>
</body>
</html>