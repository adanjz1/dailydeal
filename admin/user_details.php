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
		$uid	= (int)$_GET['id'];
		$pn		= (int)$_GET['pn'];

		if (isset($_GET['action']) && $_GET['action'] == "block") BlockUnblockUser($uid);
		if (isset($_GET['action']) && $_GET['action'] == "unblock") BlockUnblockUser($uid,1);

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS created, DATE_FORMAT(last_login, '%e %b %Y %h:%i %p') AS last_login_date FROM abbijan_users WHERE user_id='$uid'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total = mysql_num_rows($result);
	}


	$title = "User Details";
	require_once ("inc/header.inc.php");

?>   
    
      <?php if ($total > 0) { ?>

		<h2>User Details</h2>

          <div style="float: right"><img src="<?php echo AVATARS_URL.$row['avatar']; ?>" width="<?php echo AVATAR_WIDTH; ?>" height="<?php echo AVATAR_HEIGHT; ?>" class="thumb" border="0" /></div>

          <table align="center" cellpadding="3" cellspacing="5" border="0">
            <tr>
              <td valign="middle" align="left" class="tb1">User ID:</td>
              <td align="left" valign="middle"><?php echo $row['user_id']; ?></td>
            </tr>
           <tr>
            <td valign="middle" align="left" class="tb1">Username:</td>
            <td align="left" valign="middle"><b><?php echo $row['username']; ?></b></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Name:</td>
            <td align="left" valign="middle"><?php echo $row['fname']." ".$row['lname']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Email Address:</td>
            <td align="left" valign="middle"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
          </tr>
		  <?php if ($row['nickname'] != "") { ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Nickname:</td>
            <td align="left" valign="middle"><?php echo $row['nickname']; ?></td>
          </tr>
		  <?php } ?>
		  <?php if ($row['company'] != "") { ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Company:</td>
            <td align="left" valign="middle"><?php echo $row['company']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Country:</td>
            <td align="left" valign="middle"><?php echo GetCountry($row['country_id']); ?></td>
          </tr>
		  <?php if ($row['city'] != "") { ?>
          <tr>
            <td valign="middle" align="left" class="tb1">City:</td>
            <td align="left" valign="middle"><?php echo $row['city']; ?></td>
          </tr>
		  <?php } ?>
		  <?php if ($row['phone'] != "") { ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Phone:</td>
            <td align="left" valign="middle"><?php echo $row['phone']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Balance:</td>
            <td align="left" valign="middle"><span class="balance"><?php echo DisplayMoney($row['balance']); ?></span></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Orders:</td>
            <td align="left" valign="middle">
				<span style="color:#FFFFFF; background:#999; padding:2px 5px;"><?php echo GetUserOrdersTotal($row['user_id']); ?></span>
				<?php if (GetUserOrdersTotal($row['user_id']) > 0) { ?>
					&nbsp;<a href='javascript:openWindow("user_orders.php?id=<?php echo $row['user_id']; ?>",600,500)'>view orders  &#155;</a>
				<?php } ?>
			</td>
          </tr>
		  <?php if ($row['ref_id'] > 0) { ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Referred By:</td>
            <td align="left" valign="middle"><?php echo GetUsername($row['ref_id']); ?></td>
          </tr>
		  <?php } ?>
		  <?php
				$myReferrals = GetUserReferrals($row['user_id']);
				if (is_array($myReferrals) && count($myReferrals) > 0) {
		  ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Referrals:</td>
            <td align="left" valign="middle">
				<span style="color:#FFFFFF; background:#FF8A16; padding:2px 5px;"><?php echo count($myReferrals); ?></span>
				&nbsp;<a href='javascript:openWindow("user_referrals.php?id=<?php echo $row['user_id']; ?>",600,500)'>view referrals  &#155;</a>
			</td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Newsletter:</td>
            <td align="left" valign="middle"><?php echo ($row['newsletter'] == 1) ? "<img src='./images/icons/yes.png'>" : "<img src='./images/icons/no.png'>"; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Signup Date:</td>
            <td align="left" valign="middle"><?php echo $row['created']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">IP Address:</td>
            <td align="left" valign="middle"><?php echo $row['ip']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Login Count:</td>
            <td align="left" valign="middle"><?php echo $row['login_count']; ?></td>
          </tr>
		  <?php if ($row['login_count'] > 0) { ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Last Login:</td>
            <td align="left" valign="middle"><?php echo $row['last_login_date']; ?> &nbsp; (<?php echo relative_date(strtotime($row['last_login'])); ?>)</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">Last Login IP address:</td>
            <td align="left" valign="middle"><?php echo $row['last_ip']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Status:</td>
            <td align="left" valign="middle">
				<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
			</td>
          </tr>
		  <?php if ($row['status'] == "inactive" && $row['block_reason'] != "") { ?>
          <tr>
            <td valign="middle" align="left" class="tb1">Block Reason:</td>
            <td style="color: #EB0000; background: #FFEBEB; border-left: 2px #FF0000 solid" align="left" valign="top"><?php echo $row['block_reason']; ?></td>
          </tr>
		  <?php } ?>
		  <?php if ($row['status'] == "active") { ?>
          <tr>
            <td height="50" style="border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee;" colspan="2" align="center" valign="middle">
				<p><a class="blockit" href="user_details.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $pn; ?>&action=block">Block User</a></p>
			</td>
          </tr>
		  <?php }else{ ?>
          <tr>
            <td height="50" style="border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee;" colspan="2" align="center" valign="middle">
				<p><a class="unblockit" href="user_details.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $pn; ?>&action=unblock">UnBlock User</a></p>
			</td>
          </tr>
		  <?php } ?>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<br/>
				<input type="button" class="submit" name="edit" value="Edit User" onClick="javascript:document.location.href='user_edit.php?id=<?php echo $row['user_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" /> &nbsp; 
				<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='users.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
		  </td>
          </tr>
          </table>

	  <?php }else{ ?>
				<div class="info_box">Sorry, no user found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>