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


	if (isset($_GET['key']) && is_string($_GET['key']) && preg_match('/^[a-z\d]{32}$/i', $_GET['key']))
	{
		$activation_key = strtolower(mysql_real_escape_string(getGetParameter('key')));
		$activation_key = preg_replace("/[^0-9a-zA-Z]/", " ", $activation_key);
		$activation_key = substr(trim($activation_key), 0, 32);

		// activate user
		$check_result = smart_mysql_query("SELECT status FROM abbijan_users WHERE activation_key='$activation_key' LIMIT 1");
        if (mysql_num_rows($check_result) > 0)
		{
			$check_row = mysql_fetch_array($check_result);

			if ($check_row['status'] == "active")
			{
				header ("Location: /activate.php?msg=3");
				exit();
			}
			elseif ($check_row['status'] == "inactive")
			{
				smart_mysql_query("UPDATE abbijan_users SET status='active' WHERE activation_key='$activation_key' AND login_count='0' LIMIT 1");

				header ("Location: /activate.php?msg=2");
				exit();
			}
		}
		else
		{
			header ("Location: /");
			exit();
		}

		// activate subscriber
		$check_result = smart_mysql_query("SELECT status FROM abbijan_subscribers WHERE unsubscribe_key='$activation_key' LIMIT 1");
        if (mysql_num_rows($check_result) > 0)
		{
			$check_row = mysql_fetch_array($check_result);

			if ($check_row['status'] == "active")
			{
				header ("Location: /activate.php?msg=5");
				exit();
			}
			elseif ($check_row['status'] == "inactive")
			{
				smart_mysql_query("UPDATE abbijan_subscribers SET status='active' WHERE unsubscribe_key='$activation_key' LIMIT 1");

				header ("Location: /activate.php?msg=4");
				exit();
			}
		}
		else
		{
			header ("Location: /");
			exit();
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Account Activation";

	require_once ("inc/header.inc.php");

?>

	<?php if (isset($_GET['msg']) && is_numeric($_GET['msg'])) { ?>
		
		<?php if ($_GET['msg'] == 1) { ?>
			<h1>Thank you for registration!</h1><p>An activation email has been sent to your email address (don't forget to check your SPAM folder).</p>
			<p>Please check your email and click on the activation link.</p>
		<?php } ?>
		
		<?php if ($_GET['msg'] == 2) { ?>
			<h1>You have successfully activated your account!</h1>
			<p>Welcome to the <?php echo SITE_TITLE; ?>! Please <a href="/login.php">click here</a> to log in.</p>
		<?php } ?>
		
		<?php if ($_GET['msg'] == 3) { ?>
			<h1>You have already activated your account!</h1>
			<p>Please <a href="/login.php">click here</a> to log in.</p>
		<?php } ?>
		
		<?php if ($_GET['msg'] == 4) { ?><h1>You have successfully activated your subscription to our newsletter!</h1><?php } ?>
		
		<?php if ($_GET['msg'] == 5) { ?><h1>You have already activated your subscription!</h1><?php } ?>

	<?php } ?>


<?php require_once("inc/footer.inc.php"); ?>