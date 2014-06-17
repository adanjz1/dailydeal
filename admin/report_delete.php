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
		$report_id	= (int)$_GET['id'];

		DeleteReport($report_id);

		if ($_GET['type'] == "deals")
		{
			header("Location: deal_reports.php?msg=deleted&page=".$pn);
			exit();
		}
		elseif ($_GET['type'] == "users")
		{
			header("Location: user_reports.php?msg=deleted&page=".$pn);
			exit();
		}
	}

?>