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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$news_id = (int)$_GET['id'];

		smart_mysql_query("DELETE FROM abbijan_news WHERE news_id='$news_id'");

		header("Location: news.php?msg=deleted");
		exit();
	}

?>