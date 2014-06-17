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
		$pmid = (int)$_GET['id'];

		smart_mysql_query("DELETE FROM abbijan_payment_methods WHERE payment_method_id='$pmid' and payment_method_id>3");

		header("Location: pmethods.php?msg=deleted");
		exit();
	}

?>