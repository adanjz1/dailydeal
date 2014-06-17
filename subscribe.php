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



if (isset($_POST['action']) && $_POST['action'] == "subscribe")
{
	unset($errs);
	$errs = array();

	$email = mysql_real_escape_string(strtolower(getPostParameter('email')));

	if (!($email))
	{
		$errs[] = "Please enter your email address";
	}
	else
	{
		if (isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			$errs[] = "Please enter a valid email address";
		}
	}

	if (count($errs) == 0)
	{
		$check_query = "SELECT email FROM abbijan_subscribers WHERE email='$email' LIMIT 1";
		$check_result = smart_mysql_query($check_query);

		$check_query2 = "SELECT email FROM abbijan_users WHERE email='$email' LIMIT 1";
		$check_result2 = smart_mysql_query($check_query2);

		if (mysql_num_rows($check_result) != 0 || mysql_num_rows($check_result2) != 0)
		{
			header ("Location: subscribe.php?msg=2");
			exit();
		}

		$unsubscribe_key = GenerateKey($email);

		$query = "INSERT INTO abbijan_subscribers SET email='$email', unsubscribe_key='$unsubscribe_key', status='inactive', added=NOW()";
		$result = smart_mysql_query($query);
	
		////////////////////////////////  Send Message  //////////////////////////////
		$etemplate = GetEmailTemplate('subscribe');
		$esubject = $etemplate['email_subject'];
		$emessage = $etemplate['email_message'];

		$activate_link = SITE_URL."activate.php?key=".$unsubscribe_key;
		$emessage = str_replace("{activate_link}", $activate_link, $emessage);

		$subject = $esubject;
		$message = $emessage;
		$from_email = SITE_MAIL;

		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.SITE_TITLE.' <'.$from_email.'>' . "\r\n";
	
		@mail($email, $subject, $message, $headers);
		////////////////////////////////////////////////////////////////////////////////

		header("Location: /subscribe.php?msg=1");
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
	$PAGE_TITLE = "Subscribe";

	require_once ("inc/header.inc.php");

?>

	<h1>Subscribe</h1>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
	
		<div style="width: 55%;" class="success_msg">
			Thank you! You have been successfully subscribed to our newsletter.<br/>
			Please check out your email, and click on activation link to confirm your subscription.
		</div>
	
	<?php }else{ ?>
		
		<center><h2>Get daily deals delivered to your inbox!</h2></center>
		<p align="center">Subscribe to our email newsletter to have the best deals of the day conveniently sent directly to your inbox!</p>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div style="width: 55%" class="error_msg"><?php echo $allerrors; ?></div>
		<?php } ?>

		<?php if (isset($_GET['msg']) && $_GET['msg'] == 2) { ?>
			<div style="width: 55%;" class="success_msg">You are currently subscribed to our newsletter!</div>
		<?php } ?>

		<div style="width: 555px; margin: 0 auto; background: #F7F7F7; border: 1px solid #EEE; padding: 10px; text-align: center;">
		<form action="" method="post">
			<b>Your Email Address</b>: <input type="text" name="email" value="<?php echo getPostParameter('email'); ?>" class="textbox" size="32" />
			<input type="hidden" name="action" value="subscribe" />
			<input type="submit" class="submit" value="Subscribe" />
		</form>
		</div>

	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>