<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	if (!defined("abbijan_PAGE")) exit();

	$conn = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ('Could not connect to MySQL server');
	@mysql_select_db(DB_NAME, $conn) or die ('Could not select database');

?>