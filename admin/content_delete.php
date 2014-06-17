<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("../inc/auth_adm.inc.php");
	require_once("../inc/config.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 5)
	{
		$content_id = (int)$_GET['id'];
		
		smart_mysql_query("DELETE FROM abbijan_content WHERE content_id='$content_id'");
		
		header("Location: content.php?msg=deleted");
		exit();
	}

?>