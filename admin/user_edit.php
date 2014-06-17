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


	$pn = (int)$_GET['pn'];


if (isset($_POST['action']) && $_POST['action'] == "edituser")
{
	unset($errs);
	$errs = array();

	$user_id		= (int)getPostParameter('userid');
	$fname			= mysql_real_escape_string(getPostParameter('fname'));
	$lname			= mysql_real_escape_string(getPostParameter('lname'));
	$email			= mysql_real_escape_string(strtolower(getPostParameter('email')));
	$country		= (int)getPostParameter('country'); //$country = mysql_real_escape_string(getPostParameter('country'));
	$balance		= mysql_real_escape_string(getPostParameter('balance'));
	$phone			= mysql_real_escape_string(getPostParameter('phone'));
	$pwd			= mysql_real_escape_string(getPostParameter('password'));
	$pwd2			= mysql_real_escape_string(getPostParameter('password2'));
	$newsletter		= (int)getPostParameter('newsletter');
	$block_reason	= mysql_real_escape_string(nl2br(getPostParameter('block_reason')));
	$status			= mysql_real_escape_string(getPostParameter('status'));

	$flag = 0;

	if (!($fname && $lname && $email && $country && $status))
	{
		$errs[] = "Please fill in all required fields";
	}

	if (isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
	{
		$errs[] = "Invalid email address";
	}

	if (isset($pwd) && $pwd != "" && isset($pwd2) && $pwd2 != "")
	{
		if ($pwd !== $pwd2)
		{
			$errs[] = "Password confirmation is wrong";
		}
		elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
		{
			$errs[] = "Password must be between 6-20 characters (letters and numbers)";
		}
		elseif (stristr($pwd, ' '))
		{
			$errs[] = "Password must not contain spaces";
		}
		else
		{
			$flag = 1;
		}
	}

	if (isset($balance) && $balance != "" && !is_numeric($balance))
	{
		$errs[] = "Wrong account balance value";
	}

	if (count($errs) == 0)
	{
		if ($flag == 1)
		{
			$sql = "UPDATE abbijan_users SET password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', country_id='$country', balance='$balance', phone='$phone', newsletter='$newsletter', status='$status', block_reason='$block_reason' WHERE user_id='$user_id' LIMIT 1";
		}
		else
		{
			$sql = "UPDATE abbijan_users SET email='$email', fname='$fname', lname='$lname', country_id='$country', balance='$balance', phone='$phone', newsletter='$newsletter', status='$status', block_reason='$block_reason' WHERE user_id='$user_id' LIMIT 1";
		}

		if (smart_mysql_query($sql))
		{
			header("Location: users.php?msg=updated&page=".$pn."&column=".$_GET['column']."&order=".$_GET['order']);
			exit();
		}

	}
	else
	{
		$allerrors = "";
		foreach ($errs as $errorname)
			$allerrors .= "&#155; ".$errorname."<br/>\n";
	}

}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$uid = (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_users WHERE user_id='$uid' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			 $row = mysql_fetch_array($result);
		}
	}


	if (isset($_GET['action']) && $_GET['action'] == "delete_avatar")
	{
		DeleteAvatar($uid);
		smart_mysql_query("UPDATE abbijan_users SET avatar='no_avatar.png' WHERE user_id='$uid' LIMIT 1");

		header("Location: user_edit.php?id=".$uid."&column=".$_GET['column']."&order=".$_GET['order']);
		exit();
	}


	$title = "Edit User";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) { ?>

        <h2>Edit User</h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div style="width:80%;" class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

		<div style="float: right; width: 100px; text-align:center;">
			<img src="<?php echo AVATARS_URL.$row['avatar']; ?>" width="<?php echo AVATAR_WIDTH; ?>" height="<?php echo AVATAR_HEIGHT; ?>" class="thumb" border="0" />
			<?php if ($row['avatar'] != "no_avatar.png") { ?>
				<br/><a href="user_edit.php?id=<?php echo $uid; ?>&action=delete_avatar" title="Delete avatar"><img src="/images/icon_delete.png" alt="Delete avatar" /></a>
			<?php } ?>
		</div>

        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Username:</td>
            <td valign="top"><b><?php echo $row['username']; ?></b></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>First Name:</td>
            <td valign="top"><input type="text" name="fname" id="fname" value="<?php echo $row['fname']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Last Name:</td>
            <td valign="top"><input type="text" name="lname" id="lname" value="<?php echo $row['lname']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Email Address:</td>
            <td valign="top"><input type="text" name="email" id="email" value="<?php echo $row['email']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Country:</td>
            <td align="left" valign="top">
				<select name="country" class="textbox2" id="country" style="width: 185px">
				<option value="">-- Please select your country --</option>
				<?php

					$sql_country = "SELECT * FROM abbijan_countries ORDER BY name ASC";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							if ($row['country_id'] == $row_country['country_id'])
								echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
							else
								echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
						}
					}

				?>
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Phone:</td>
            <td valign="top"><input type="text" name="phone" id="phone" value="<?php echo $row['phone']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Balance:</td>
            <td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="balance" id="balance" value="<?php echo DisplayMoney($row['balance'], $hide_cyrrency_sign=1); ?>" size="5" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">New Password:</td>
            <td valign="top"><input type="password" name="password" id="password" value="" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Confirm New Password:</td>
            <td valign="top"><input type="password" name="password2" id="password2" value="" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Newsletter:</td>
            <td align="left" valign="middle"><input type="checkbox" name="newsletter" class="checkboxx" value="1" <?php echo (@$row['newsletter'] == 1) ? "checked" : "" ?>/></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Block Reason:</td>
            <td valign="top"><textarea name="block_reason" cols="40" rows="2" class="textbox2"><?php echo $row['block_reason']; ?></textarea></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="userid" id="userid" value="<?php echo (int)$row['user_id']; ?>" />
			<input type="hidden" name="action" id="action" value="edituser" />
			<input type="submit" name="update" id="update" class="submit" value="Update" />
			&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='users.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no user found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>