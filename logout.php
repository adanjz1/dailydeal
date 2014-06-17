<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();

	unset($_SESSION['userid'], $_SESSION['FirstName']);
	
	session_destroy();

	header("Location: login.php");
	exit();
	
?>