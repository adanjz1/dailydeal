<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/iflogged.inc.php");
	require_once("inc/config.inc.php");


if (isset($_POST['action']) && $_POST['action'] == "register")
{
	unset($errs);
	$errs = array();

	$fname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('fname'))));
	$lname		= mysql_real_escape_string(ucfirst(strtolower(getPostParameter('lname'))));
	$email		= mysql_real_escape_string(strtolower(getPostParameter('email')));
	$cemail		= mysql_real_escape_string(strtolower(getPostParameter('cemail')));
	$pwd		= mysql_real_escape_string(getPostParameter('password'));
	$pwd2		= mysql_real_escape_string(getPostParameter('password2'));
	$country	= (int)getPostParameter('country');
	$phone		= mysql_real_escape_string(getPostParameter('phone'));
	$tos		= (int)getPostParameter('tos');
	$newsletter	= (int)getPostParameter('newsletter');
	$ref_id		= (int)getPostParameter('referer_id');
	$ip			= mysql_real_escape_string(getenv("REMOTE_ADDR"));


	if (!($fname && $lname && $email && $cemail && $pwd && $pwd2)) // && $country
	{
		$errs[] = "Please fill in all required fields";
	}

	if (isset($email) && $email != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
	{
		$errs[] = "Please enter a valid email address";
	}

	if (isset($email) && $email != "" && isset($cemail) && $cemail != "")
	{
		if ($email !== $cemail)
		{
			$errs[] = "Email confirmation is wrong";
		}
	}

	if (isset($pwd) && $pwd != "" && isset($pwd2) && $pwd2 != "")
	{
		if ($pwd !== $pwd2)
		{
			$errs[] = "Password confirmation is wrong";
		}
		elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
		{
			$errs[] = "Password must be between 6-20 characters";
		}
		elseif (stristr($pwd, ' '))
		{
			$errs[] = "Password must not contain spaces";
		}
	}

	if (!(isset($tos) && $tos == 1))
	{
		$errs[] = "You must agree to the Terms &amp; Conditions";
	}

	if (count($errs) == 0)
	{
				// check if user exists
				$check_query = "SELECT username FROM abbijan_users WHERE username='$email' OR email='$email' LIMIT 1";
				$check_result = smart_mysql_query($check_query);

				if (mysql_num_rows($check_result) != 0)
				{
					header ("Location: signup.php?msg=exists");
					exit();
				}

				// delete user from subscribers list
				$check_subscriber_query = "SELECT email FROM abbijan_subscribers WHERE email='$email' LIMIT 1";
				$check_subscriber_result = smart_mysql_query($check_subscriber_query);

				if (mysql_num_rows($check_subscriber_result) != 0)
				{
					smart_mysql_query("DELETE FROM abbijan_subscribers WHERE email='$email'");
				}

				// block same ip address
				if (BLOCK_SAME_IP == 1)
				{
					$ip_check_query = "SELECT username FROM abbijan_users WHERE ip='$ip' OR last_ip='$ip' LIMIT 1";
					$ip_check_result = smart_mysql_query($ip_check_query);

					if (mysql_num_rows($ip_check_result) != 0)
					{
						header ("Location: signup.php?msg=exists2");
						exit();
					}
				}

				// check referral
				if ($ref_id > 0)
				{
					$check_referral_query = "SELECT email FROM abbijan_users WHERE user_id='$ref_id' LIMIT 1";
					$check_referral_result = smart_mysql_query($check_referral_query);

					if (mysql_num_rows($check_referral_result) != 0)
						$ref_id = $ref_id;
					else
						$ref_id = 0;
				}


				$activation_key = GenerateKey($username);

				$insert_query = "INSERT INTO abbijan_users SET username='$email', password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', nickname='', avatar='no_avatar.png', country_id='$country', phone='$phone', ref_id='$ref_id', newsletter='$newsletter', ip='$ip', status='inactive', activation_key='$activation_key', created=NOW()";
				smart_mysql_query($insert_query);
			
				////////////////////////////////  Send Message  //////////////////////////////
				$etemplate = GetEmailTemplate('activate');
				$esubject = $etemplate['email_subject'];
				$emessage = $etemplate['email_message'];

				$activate_link = SITE_URL."activate.php?key=".$activation_key;

				$emessage = str_replace("{first_name}", $fname, $emessage);
				$emessage = str_replace("{username}", $email, $emessage);
				$emessage = str_replace("{password}", $pwd, $emessage);
				$emessage = str_replace("{activate_link}", $activate_link, $emessage);

				$to_email = $fname.' '.$lname.' <'.$email.'>';
				$subject = $esubject;
				$message = $emessage;
				$from_email = SITE_MAIL;

				$headers = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.SITE_TITLE.' <'.$from_email.'>' . "\r\n";
			
				@mail($to_email, $subject, $message, $headers);
				////////////////////////////////////////////////////////////////////////////////

				header("Location: /activate.php?msg=1");
				exit();
	}
	else
	{
		$allerrors = "";
		foreach ($errs as $errorname)
			$allerrors .= "&#155; ".$errorname."<br/>\n";
	}
}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Sign Up";
	
	require_once ("inc/header.inc.php");
	
?>

	<div style="float: right; display: block; padding-top: 20px;">Already registered? <a href="/login.php">Log in</a></div>

	<h1>Sign Up</h1>

		<?php if (isset($allerrors) || isset($_GET['msg'])) { ?>
		<div class="error_msg">
			<?php if (isset($_GET['msg']) && $_GET['msg'] == "exists") { ?>
				&#155; The email address you have entered is already in use. <a href="/forgot.php">Forgot your password?</a></font><br/>
			<?php }else if (isset($_GET['msg']) && $_GET['msg'] == "exists2") { ?>
				&#155; Sorry, we have registered member from your computer. <a href="/forgot.php">Forgot your password?</a></font><br/>
			<?php }elseif (isset($allerrors)) { ?>
				<?php echo $allerrors; ?>
			<?php } ?>
		</div>
		<?php } ?>

		<p align="right"><span class="req">* denotes required field</span></p>

		<form action="" method="post">
        <table align="center" cellpadding="3" cellspacing="0" border="0">
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>First Name:</td>
            <td align="left" valign="top"><input type="text" id="fname" class="textbox" name="fname" required="required" value="<?php echo getPostParameter('fname'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>Last Name:</td>
            <td align="left" valign="top"><input type="text" id="lname" class="textbox" name="lname" required="required" value="<?php echo getPostParameter('lname'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>Email:</td>
            <td align="left" valign="top"><input type="text" id="email" class="textbox" name="email" required="required" value="<?php echo getPostParameter('email'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>Confirm Email:</td>
            <td align="left" valign="top"><input type="text" id="cemail" class="textbox" name="cemail" required="required" value="<?php echo getPostParameter('cemail'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>Password:</td>
            <td nowrap="nowrap" align="left" valign="top"><input type="password" id="password" class="textbox" name="password" required="required" value="" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>Confirm Password:</td>
            <td nowrap="nowrap" align="left" valign="top"><input type="password" id="password2" class="textbox" name="password2" required="required" value="" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>Country:</td>
            <td align="left" valign="top">
				<select name="country" class="textbox2" id="country" required="required" style="width: 170px;">
				<option value="">-- Please select your country --</option>
				<?php
					$sql_country = "SELECT * FROM abbijan_countries where active=1 ORDER BY name ASC";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							if ($country == $row_country['country_id'])
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
            <td align="right" valign="middle">Phone:</td>
            <td align="left" valign="top"><input type="text" id="phone" class="textbox" name="phone" value="<?php echo getPostParameter('phone'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td align="left" valign="top"><input type="checkbox" name="newsletter" class="checkboxx" value="1" checked="checked" /> Sign me up to the <?php echo SITE_TITLE; ?> newsletter</td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td align="left" valign="top"><input type="checkbox" name="tos" class="checkboxx" value="1" <?php echo (@$tos == 1) ? "checked" : "" ?>/> I agree with the <a href="/terms.php" target="_blank">Terms and Conditions</a></td>
          </tr>
        </tr>
          <tr>
            <td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
			<?php if (isset($_COOKIE['referer_id']) && is_numeric($_COOKIE['referer_id'])) { ?>
				<input type="hidden" name="referer_id" id="referer_id" value="<?php echo (int)$_COOKIE['referer_id']; ?>" />
			<?php } ?>
				<input type="hidden" name="action" id="action" value="register" />
				<input type="submit" class="submit" name="Register" id="Register" value="Sign Up" />
		  </td>
          </tr>
        </table>
      </form>


<?php require_once ("inc/footer.inc.php"); ?>