<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	$admin_panel = 1;

	session_start();
	require_once("../inc/config.inc.php");


	if (isset($_SESSION['adm']['id']) && is_numeric($_SESSION['adm']['id']))
	{
		header("Location: index.php");
		exit();
	}


if (isset($_POST['action']) && $_POST['action'] == "login")
{
	$username	= mysql_real_escape_string(getPostParameter('username'));
	$pwd		= mysql_real_escape_string(getPostParameter('password'));
	$iword		= substr(GetSetting('iword'), 0, -3);
	$ip			= mysql_real_escape_string(getenv("REMOTE_ADDR"));

	if (!($username && $pwd))
	{
		$errormsg = "Please enter username and password";
	}
	else
	{
		$sql = "SELECT * FROM abbijan_settings WHERE setting_key='word' AND setting_value='".PasswordEncryption($pwd.$iword)."' LIMIT 1";
		$result = smart_mysql_query($sql);

		if ((mysql_num_rows($result) != 0) && ($username == 'admin'))
		{
			$row = mysql_fetch_array($result);

			if (!session_id()) session_start();
			$_SESSION['adm']['id'] = $row['setting_id'];
	
			header("Location: index.php");
			exit();
		}
		else
		{
			header("Location: login.php?msg=1");
			exit();
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<title>Log in | abbijan Admin Panel</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/login.css" />
		<link rel="shortcut icon" href="/favicon.ico" />
		<link rel="icon" type="image/ico" href="/favicon.ico" />

</head>
<body>

<table align="center" cellpadding="5" cellspacing="0" border="0" align="center">
<tr>
	<td height="170" valign="bottom" align="center">
		<a target="_blank" href=""><img src="images/abbijan_logo.png" height="100" alt="abbijan" title="abbijan" border="0" /></a>
		<br/><br/>
	</td>
</tr>
</table>

<table width="300" align="center" cellpadding="5" cellspacing="0" border="0" align="center">
<tr>
	<td height="250" valign="top" align="left">
      
       <h2 style="margin-bottom:5px;">Admin Panel</h2>

		<?php if (isset($errormsg) || isset($_GET['msg'])) { ?>
			<table width="100%" style="border: 1px #F3C5D4 dotted;" bgcolor="#EF0303" align="center" cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td height="30" align="center" valign="middle">
					<font color="#FFFFFF"><b>
						<?php if (isset($errormsg) && $errormsg != "") {  echo $errormsg; } ?>
						<?php if ($_GET['msg'] == 1) { echo "Wrong username or password!"; } ?>
					</b></font>
				</td>
			</tr>
			</table>
		<?php } ?>

		<form action="login.php" method="post">
        <table bgcolor="#FFFFFF" width="100%" style="border: 1px dotted #F7F7F7;" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="80" align="right" valign="middle">Username:</td>
            <td align="left" valign="top"><input type="text" class="textbox" name="username" value="" size="25" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle">Password:</td>
            <td align="left" valign="top"><input type="password" class="textbox" name="password" value="" size="25" /></td>
          </tr>
          <tr>
			<td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
		  		<input type="hidden" name="action" value="login" />
				<input type="submit" class="submit" name="login" id="login" value="Log in" />
			</td>
          </tr>
        </table>
      </form>
	  <!--
		<p align="center">
			<span style="color: #AFAFAF;">Powered by <a href="" target="_blank" style="color:#AB32FF; font-weight:bold;">abbijan</a></span>
		</p>
	  -->
	</td>
</tr>
</table>
</body>
</html>