<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();

	unset($_SESSION['adm']['id']);
	
	session_destroy();

	header("Location: login.php");
	exit();
	
?>