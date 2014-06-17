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
	require_once("./inc/adm_functions.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$pn			= (int)$_GET['pn'];
		$order_id	= (int)$_GET['id'];
  
		DeleteOrder($order_id);

		header("Location: orders.php?msg=deleted&page=".$pn);
		exit();
	}

?>