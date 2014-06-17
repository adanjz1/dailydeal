<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");


if (isset($_POST['action']) && $_POST['action'] == "forgot")
{
	$email = strtolower(mysql_real_escape_string(getPostParameter('email')));

	if (!($email) || $email == "")
	{
		header("Location: forgot.php?msg=1");
		exit();
	}
	else
	{
		if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			header("Location: forgot.php?msg=2");
			exit();
		}
	}
	
	$query = "SELECT * FROM abbijan_users WHERE email='$email' AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);

	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$newPassword = generatePassword(10);
		$update_query = "UPDATE abbijan_users SET password='".PasswordEncryption($newPassword)."' WHERE user_id='".(int)$row['user_id']."'";
		
		if (smart_mysql_query($update_query))
		{
			////////////////////////////////  Send Message  //////////////////////////////
			$etemplate = GetEmailTemplate('forgot_password');
			$esubject = $etemplate['email_subject'];
			$emessage = $etemplate['email_message'];

			$emessage = str_replace("{first_name}", $row['fname'], $emessage);
			$emessage = str_replace("{username}", $row['username'], $emessage);
			$emessage = str_replace("{password}", $newPassword, $emessage);
			$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);	
			
			$to_email = $row['fname'].' '.$row['lname'].' <'.$email.'>';
			$subject = $esubject;
			$message = $emessage;
			$from_email = SITE_MAIL;				
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.SITE_TITLE.' <'.$from_email.'>' . "\r\n";

			if (@mail($to_email, $subject, $message, $headers))
			{
				header("Location: forgot.php?msg=4");
				exit();
			}
			////////////////////////////////////////////////////////////////////////////////
		}
	}
	else
	{
		header("Location: forgot.php?msg=3");
		exit();
	}

}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Forgot Password";

	require_once "inc/header.inc.php";
	
?>

	<h1>Forgot Password</h1>	
  

	<?php if (isset($_GET['msg']) && is_numeric($_GET['msg']) && $_GET['msg'] != 4) { ?>
		<div style="width: 480px;" class="error_msg">
			<?php if ($_GET['msg'] == 1) { ?>Please enter your email address<?php } ?>
			<?php if ($_GET['msg'] == 2) { ?>Please enter a valid email address<?php } ?>
			<?php if ($_GET['msg'] == 3) { ?>Sorry, we could not find your email address in our system!<?php } ?>
		</div>
	<?php }elseif($_GET['msg'] == 4){ ?>
		<div style="width: 480px;" class="success_msg">Your new password has been sent to your email address!</div>
	<?php }else{ ?> 
		<p align="center">Please enter your email address below and we will send you an email that contains your new password.</p>
	<?php } ?>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 4) { ?>
		<p align="center"><a class="goback" href="/login.php">Back to login page</a></p>
	<?php } else { ?>
		<div style="width: 520px; margin: 0 auto; background: #F7F7F7; border: 1px solid #EEE; padding: 10px; text-align: center;">
			<form action="" method="post">
			<b>Your email address:</b></td>
			<input type="text" class="textbox" name="email" required="required" size="35" value="" />
			<input type="hidden" name="action" value="forgot" />
			<input type="submit" class="submit" name="send" id="send" value="Send Password" />
			</form>
		</div>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>