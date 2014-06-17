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

	// if refer a friend bonus is disabled - redirect user to home page
	if (!(is_numeric(REFER_FRIEND_BONUS) && REFER_FRIEND_BONUS > 0))
	{
		header ("Location: index.php");
		exit();
	}
	elseif (isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))
	{
		header ("Location: invite.php");
		exit();
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Refer a Friend";

	require_once ("inc/header.inc.php");

?>

<h1>Refer a Friend</h1>

<p>Refer your friends to <?php echo SITE_TITLE; ?>, and when they make first purchase, you'll receive a <b><?php echo DisplayPrice(REFER_FRIEND_BONUS); ?></b> bonus to your account. <a href="/login.php">Log in</a> or <a href="/signup.php">Sign up</a> to refer your friends.</p>

<h3>How do I refer someone?</h3>
You must be signed in to your account to refer someone. Once you are signed in, you must use your unique URL link in order to get a referral credit.

<h3>How long does my referral have to make their first purchase?</h3>
Referrals must make their purchase within 30 days of clicking your unique URL link in order for you to get the <b><?php echo DisplayPrice(REFER_FRIEND_BONUS); ?></b> credit.


<?php require_once ("inc/footer.inc.php"); ?>