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
		$id = (int)$_GET['id'];

		smart_mysql_query("DELETE FROM abbijan_shipping_methods WHERE shipping_method_id='$id'");

		header("Location: shipping.php");
		exit();
	}

?>