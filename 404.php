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
	$PAGE_TITLE = "Page not found";

	require_once ("inc/header.inc.php");
	
?>

	<h1>Page not found</h1>

	<div class="not_found">
		<img src="/images/404.png" />
		<h2>Sorry, the page you're looking for can't be found.</h2>
		<p><a class="goback" href="/">Go back to the home page</a></p>
	</div>

	
<?php require_once ("inc/footer.inc.php"); ?>