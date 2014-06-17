<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");


	$query	= "SELECT * FROM abbijan_users WHERE user_id='$userid' AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);
	$total	= mysql_num_rows($result);

	if ($total > 0)
	{
		$row = mysql_fetch_array($result);
	}
	else
	{
		header ("Location: logout.php");
		exit();
	}

	
	if (isset($_POST['action']) && $_POST['action'] == "editprofile")
	{
		unset($errs);
		$errs = array();

		$fname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('fname'))));
		$lname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('lname'))));
		$email		= mysql_real_escape_string(strtolower(getPostParameter('email')));
		$country	= (int)getPostParameter('country');
		$city		= mysql_real_escape_string(getPostParameter('city'));
		$company	= mysql_real_escape_string(getPostParameter('company'));
		$nickname	= mysql_real_escape_string(getPostParameter('nickname'));
		$show_as	= (int)getPostParameter('show_as');
		$newsletter	= (int)getPostParameter('newsletter');


		if(!($fname && $lname && $email && $country))
		{
			$errs[] = "Please fill in all required fields";
		}

		if(isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			$errs[] = "Please enter a valid email address";
		}

		if ($nickname != "")
		{
			// check if mickname exists
			$check_query = "SELECT * FROM abbijan_users WHERE nickname='$nickname' LIMIT 1";
			$check_result = smart_mysql_query($check_query);

			if (mysql_num_rows($check_result) != 0)
			{
				header ("Location: myprofile.php?msg=4");
				exit();
			}
		}

		if (count($errs) == 0)
		{
			$up_query = "UPDATE abbijan_users SET fname='$fname', lname='$lname', country_id='$country', email='$email', nickname='$nickname', company='$company', city='$city', show_as='$show_as', newsletter='$newsletter' WHERE user_id='$userid' LIMIT 1";
		
			if (smart_mysql_query($up_query))
			{
				header("Location: myprofile.php?msg=1");
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


	if (isset($_POST['action']) && $_POST['action'] == "change_avatar" && ALLOW_AVATARS == 1)
	{
		unset($errs);
		$errs = array();

		$upload_dir	= PUBLIC_HTML_PATH.AVATARS_URL;


		if (!($_FILES['avatar']['tmp_name']))
		{
			$errs[] = "Please select image";
		}
		else
		{
			if (is_uploaded_file($_FILES['avatar']['tmp_name']))
			{
				list($width, $height, $type) = getimagesize($_FILES['avatar']['tmp_name']);

				if (!getimagesize($_FILES['avatar']['tmp_name']))
				{
					$errs[] = "Only image uploads are allowed";
				}
				elseif ($width < AVATAR_WIDTH || $height < AVATAR_HEIGHT)
				{
					$errs[] = "Too low image dimension. Min ".AVATAR_WIDTH."x".AVATAR_HEIGHT." px";
				}
				elseif ($_FILES['avatar']['size'] > 524288)
				{
					$errs[] = "The image file size is too big. It exceeds 500 Kb.";
				}
				elseif (preg_match('/\\.(gif|jpg|png|jpeg)$/i', $_FILES['avatar']['name']) != 1)
				{
					$errs[] = "Please upload a JPEG, PNG, or GIF image";
					unlink($_FILES['avatar']['tmp_name']);
				}
				else
				{
					$img_path			= $upload_dir.$_FILES['avatar']['name'];
					
					$rnd_number			= mt_rand(1,10000).time();
					$new_avatar_name	= "avatar_".$rnd_number.$userid.".jpg";
					$avatar_path		= $upload_dir.$new_avatar_name;

					create_thumb($_FILES['avatar']['tmp_name'],$avatar_path, AVATAR_WIDTH, AVATAR_HEIGHT);
				}
			}	
		}

		if (count($errs) == 0)
		{
			// delete previous avatar
			DeleteAvatar($userid);

			$upp_query = "UPDATE abbijan_users SET avatar='$new_avatar_name' WHERE user_id='$userid'";
		
			if (smart_mysql_query($upp_query))
			{
				header("Location: myprofile.php?msg=3");
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


	if (isset($_POST['action']) && $_POST['action'] == "changepwd")
	{
		unset($errs);
		$errs = array();

		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$newpwd		= mysql_real_escape_string(getPostParameter('newpassword'));
		$newpwd2	= mysql_real_escape_string(getPostParameter('newpassword2'));


		if (!($pwd && $newpwd && $newpwd2))
		{
			$errs[] = "Please fill in all fields";
		}
		else
		{
			if (PasswordEncryption($pwd) !== $row['password'])
			{
				$errs[] = "Your current password is wrong. Please try again!";
			}

			if ($newpwd !== $newpwd2)
			{
				$errs[] = "Password confirmation is wrong";
			}
			elseif ((strlen($newpwd)) < 6 || (strlen($newpwd2) < 6) || (strlen($newpwd)) > 20 || (strlen($newpwd2) > 20))
			{
				$errs[] = "Password must be between 6-20 characters (letters and numbers).";
			}
			elseif (stristr($newpwd, ' '))
			{
				$errs[] = "Password must not contain spaces";
			}
		}

		if (count($errs) == 0)
		{
			$upp_query = "UPDATE abbijan_users SET password='".PasswordEncryption($newpwd)."' WHERE user_id='$userid'";
		
			if (smart_mysql_query($upp_query))
			{
				header("Location: myprofile.php?msg=2");
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


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "My Profile";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">

	<div style="float: right; padding-top: 2px;"></div>

	<h1>My Profile</h1>


		<?php if (isset($allerrors)) { ?>
				<div style="width: 94%;" class="error_msg"><?php echo $allerrors; ?></div>
		<?php }else{ ?>

			<?php if (isset($_GET['msg']) && is_numeric($_GET['msg'])) { ?>
				<div style="width: 94%;" class="success_msg">
					<?php

						switch ($_GET['msg'])
						{
							case "1": echo "Your profile has been updated successfully"; break;
							case "2": echo "Password has been changed successfully"; break;
							case "3": echo "Your avatar has been changed successfully"; break;
							case "4": echo "Sorry, this nickname is already taken"; break;
						}

					?>
				</div>
			<?php } ?>

		<?php } ?>

	<div style="float:left; width:40%;">

		<form action="" method="post">
          <table width="400" align="center" cellpadding="2" cellspacing="0" border="0">
            <tr>
				<td width="100" align="right" valign="middle">Username:</td>
				<td align="left" valign="top"><span class="username"><?php echo $row['username']; ?></span></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>First Name:</td>
				<td align="left" valign="top"><input type="text" class="textbox" name="fname" id="fname" value="<?php echo $row['fname']; ?>" size="25" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>Last Name:</td>
				<td align="left" valign="top"><input type="text" class="textbox" name="lname" id="lname" value="<?php echo $row['lname']; ?>" size="25" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="req">* </span>Email Address:</td>
				<td align="left" valign="top"><input type="text" class="textbox" name="email" id="email" value="<?php echo $row['email']; ?>" size="25" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle">Nickname:</td>
				<td align="left" valign="top"><input type="text" class="textbox" name="nickname" id="nickname" value="<?php echo $row['nickname']; ?>" size="25" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap" align="right" valign="middle">Show my name as:</td>
				<td align="left" valign="top">
				<select name="show_as" class="textbox2" style="width: 173px">
					<option value="1" <?php if ($row['show_as'] == 1) echo "selected='selected'"; ?>><?php echo $row['fname']; ?></option>
					<option value="2" <?php if ($row['show_as'] == 2) echo "selected='selected'"; ?>><?php echo $row['fname']." ".substr($row['lname'], 0, 1)."."; ?></option>
					<option value="3" <?php if ($row['show_as'] == 3) echo "selected='selected'"; ?>><?php echo $row['fname']." ".$row['lname']; ?></option>
					<?php if ($row['nickname'] != "") { ?><option value="4" <?php if ($row['show_as'] == 4) echo "selected='selected'"; ?>><?php echo ($row['nickname'] != "") ? $row['nickname'] : "Nickname"; ?></option><?php } ?>
				</select>
				</td>
			</tr>
			<tr>
				<td align="right" valign="middle">Country:</td>
				<td align="left" valign="top">
					<select name="country" class="textbox2" id="country" style="width: 173px">
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
				<td align="right" valign="middle">City:</td>
				<td align="left" valign="top"><input type="text" class="textbox" name="city" id="city" value="<?php echo $row['city']; ?>" size="25" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle">Company:</td>
				<td align="left" valign="top"><input type="text" class="textbox" name="company" id="company" value="<?php echo $row['company']; ?>" size="25" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle">&nbsp;</td>
				<td align="left" valign="top"><input type="checkbox" name="newsletter" class="checkboxx" value="1" <?php echo (@$row['newsletter'] == 1) ? "checked" : "" ?>/> I want to receive the newsletter</td>
			</tr>
			<tr>
				<td align="center" valign="middle">&nbsp;</td>
				<td align="left" valign="middle">
					<input type="hidden" name="action" value="editprofile" />
					<input name="uid" type="hidden" value="<?php echo (int)$row['user_id']; ?>" />
					<input type="submit" class="submit" name="Update" id="Update" value="Update profile" />
				</td>
			</tr>
          </table>
		</form>
	</div>

	<div style="float:right; width:40%;">

		<a name="password"></a>
		<center><h3>Change Password</h3></center>

		<form action="" method="post">
		<table align="center" cellpadding="2" cellspacing="0" border="0">
            <tr>
              <td nowrap="nowrap" align="right" valign="middle">Old Password:</td>
              <td align="left" valign="top"><input type="password" class="textbox" name="password" id="password" value="" size="25" /></td>
            </tr>
            <tr>
              <td nowrap="nowrap" align="right" valign="middle">New Password:</td>
              <td align="left" valign="top"><input type="password" class="textbox" name="newpassword" id="newpassword" value="" size="25" /></td>
            </tr>
            <tr>
              <td nowrap="nowrap" align="right" valign="middle">Confirm New Password:</td>
              <td align="left" valign="top"><input type="password" class="textbox" name="newpassword2" id="newpassword2" value="" size="25" /></td>
            </tr>
          <tr>
            <td align="center" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="action" value="changepwd" />
				<input name="uid" type="hidden" value="<?php echo (int)$row['user_id']; ?>" />
				<input type="submit" class="submit" name="Change" id="Change" value="Change password" />
			</td>
			</tr>
		</table>
        </form>
		<br/>

		<?php if (ALLOW_AVATARS == 1) { ?>
		<center><h3>Change Avatar</h3></center>
		<form action="" method="post" enctype="multipart/form-data">
		<table align="center" cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td width="70" align="left" valign="top"><img src="<?php echo AVATARS_URL.$row['avatar']; ?>" width="50" height="50" class="thumb" border="0" /></td>
			<td nowrap="nowrap" align="center" valign="top">
				<input type="file" class="textbox" name="avatar" style="width:170px;" /><br/>
				<input type="hidden" name="action" value="change_avatar" />
				<input type="submit" class="submit" name="Change" id="Change" value="Change avatar" />
			</td>
         </tr>
		</table>
        </form>
		<?php } ?>

	</div>

</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>