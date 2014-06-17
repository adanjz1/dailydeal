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


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Payment Canceled";
	
	require_once ("inc/header.inc.php");
	
?>


	<h1 class="canceled">Payment Canceled</h1>

	<p>Sorry, your payment has failed, please try again.</p>
	<p>If you see this error message again, please <a href="/contact.php">contact us</a>.</p>


<?php require_once ("inc/footer.inc.php"); ?>