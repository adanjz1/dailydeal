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

	$content = GetContent('aboutus');

	///////////////  Page config  ///////////////
	$PAGE_TITLE = $content['title'];

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo $content['title']; ?></h1>
	<p><?php echo $content['text']; ?></p>


<?php require_once("inc/footer.inc.php"); ?>