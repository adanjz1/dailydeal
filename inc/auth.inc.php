<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	if (!(isset($_SESSION['userid']) && is_numeric($_SESSION['userid'])))
	{
		header("Location: login.php?msg=3");
		exit();
	}
	else
	{
		$userid	= (int)$_SESSION['userid'];
	}

?>