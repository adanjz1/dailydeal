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


	if (isset($_POST['action']) && $_POST['action'] == "login")
	{
		$username	= mysql_real_escape_string(getPostParameter('username'));
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$ip			= mysql_real_escape_string(getenv("REMOTE_ADDR"));

		if (!($username && $pwd))
		{
			$errormsg = "Please enter your email and password";
		}
		else
		{
			$sql = "SELECT * FROM abbijan_users WHERE username='$username' AND password='".PasswordEncryption($pwd)."' LIMIT 1";
			$result = smart_mysql_query($sql);

			if (mysql_num_rows($result) != 0)
			{
					$row = mysql_fetch_array($result);

					if ($row['status'] == 'inactive')
					{
						// if not new user
						if ($row['login_count'] > 0)
						{
							header("Location: login.php?msg=4");
							exit();
						}
						else
						{
							header("Location: login.php?msg=2");
							exit();
						}
					}

					smart_mysql_query("UPDATE abbijan_users SET last_ip='$ip', login_count=login_count+1, last_login=NOW() WHERE user_id='".(int)$row['user_id']."' LIMIT 1"); 

					if (!session_id()) session_start();
					$_SESSION['userid'] = $row['user_id'];
					$_SESSION['FirstName'] = $row['fname'];

					if ($_SESSION['go2cart'])
					{
						$redirect_url = "checkout.php";
						unset($_SESSION['go2cart']);
					}
					else
					{
						$redirect_url = "myaccount.php";
					}

					header("Location: ".$redirect_url);
					exit();
			}
			else
			{
					header("Location: login.php?msg=1");
					exit();
			}
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Log in";

	require_once ("inc/header.inc.php");

?>

<table width="100%" align="center" cellpadding="2" cellspacing="0" border="0">
<tr>
<td width="48%" valign="top" align="left">
      
        <h1>Login to your account</h1>

		<?php if (isset($errormsg) || isset($_GET['msg'])) { ?>
			<div style="width: 77%;" class="error_msg">
				<?php if (isset($errormsg) && $errormsg != ""){ echo $errormsg; }?>
				<?php if ($_GET['msg'] == 1) { echo "Invalid Email or Password"; } ?>
				<?php if ($_GET['msg'] == 2) { echo "Sorry, your account is inactive.<br/>Please check your email for activation link."; } ?>
				<?php if ($_GET['msg'] == 3) { echo "You must login first"; } ?>
				<?php if ($_GET['msg'] == 4) { echo "Sorry, your account is inactive.<br/>For more information please <a href='/contact.php'>contact us</a>."; } ?>
			</div>
		<?php } ?>

		<form action="" method="post">
        <table width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr>
            <td align="right" valign="middle">Email:</td>
            <td valign="top"><input type="text" class="textbox" name="username" required="required" value="<?php echo getPostParameter('username'); ?>" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle">Password:</td>
            <td valign="top"><input type="password" class="textbox" name="password" required="required" value="" size="25" /></td>
          </tr>
          <tr>
            <td valign="top" align="middle">&nbsp;</td>
			<td align="left" valign="bottom">
		  		<input type="hidden" name="action" value="login" />
				<input type="submit" class="submit" name="login" id="login" value="Login" />
			</td>
          </tr>
          <tr>
		   <td valign="top" align="middle">&nbsp;</td>
            <td align="left" valign="bottom">
				<a href="/forgot.php">Forgot your password?</a>
			</td>
          </tr>
        </table>
      </form>

</td>
<td width="4%" valign="top" align="left">&nbsp;</td>
<td width="48%" valign="top" align="left">
	
		<h1>Not registered yet?</h1>
		<p>Sign up today! It's free, fast and easy!</p>

		<p><b>Why Join?</b></p>
		<ul id="benefits">
			<li>Save your favorites</li>
			<li>View your past orders</li>
			<li>Discuss about items</li>
			<?php if (REFER_FRIEND_BONUS > 0) { ?>
				<li>Refer a friend - earn <b><?php echo DisplayMoney(REFER_FRIEND_BONUS); ?></b></li>
			<?php } ?>
			<li>Save time on your next purchase</li>
		</ul>

		<p style="padding-left: 45px;"><a href="/signup.php" class="submit" style="color: #FFF">Sign Up!</a></p>
	</div>

</td>
</tr>
</table>

<?php require_once ("inc/footer.inc.php"); ?>