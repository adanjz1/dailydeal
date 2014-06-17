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


	if (isset($_GET['key']) && is_string($_GET['key']) && preg_match('/^[a-z\d]{32}$/i', $_GET['key']))
	{
		$verification_code	= strtolower(mysql_real_escape_string(getGetParameter('key')));
		$verification_code	= preg_replace("/[^0-9a-zA-Z]/", " ", $verification_code);
		$verification_code	= substr(trim($verification_code), 0, 32);

		$check_result = smart_mysql_query("SELECT subscriber_id FROM abbijan_subscribers WHERE unsubscribe_key='$verification_code' LIMIT 1");
        if (mysql_num_rows($check_result) > 0)
		{
			$check_row = mysql_fetch_array($check_result);
			$subscriber_id = $check_row['subscriber_id'];
			smart_mysql_query("DELETE FROM abbijan_subscribers WHERE subscriber_id='$subscriber_id'");
			
			header ("Location: /unsubscribe.php?msg=1");
			exit();
		}

		require_once("inc/auth.inc.php");
		$check_result = smart_mysql_query("SELECT newsletter FROM abbijan_users WHERE activation_key='$verification_code' LIMIT 1");
        if (mysql_num_rows($check_result) > 0)
		{
			$check_row = mysql_fetch_array($check_result);

			if ($check_row['newsletter'] == "0")
			{
				header ("Location: /unsubscribe.php?msg=1");
				exit();
			}
			elseif ($check_row['newsletter'] == "1")
			{
				smart_mysql_query("UPDATE abbijan_users SET newsletter='0' WHERE activation_key='$verification_code' LIMIT 1");
				header ("Location: /unsubscribe.php?msg=1");
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
	$PAGE_TITLE = "Unsubscribe";

	require_once ("inc/header.inc.php");

?>

	<?php if (isset($_GET['msg']) && is_numeric($_GET['msg'])) { ?>
	
		<?php if ($_GET['msg'] == 1) { ?><h1>You have been successfully unsubscribed!</h1><?php } ?>
	
	<?php } ?>


<?php require_once("inc/footer.inc.php"); ?>